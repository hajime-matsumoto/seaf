<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\BackEnd;

use Seaf\Base\Container;
use Seaf\Base\Command;
use Seaf\Base\Module;
use Seaf\Util\Util;
use Seaf\Logging;

class ModuleDoseNotImplementsFacadeIF extends \Exception 
{
    public function __construct($name)
    {
        parent::__construct(sprintf(
            'Moduel %s Required to implements Module\FacadeIF',
            $name
        ));
    }
}

/**
 * バックエンド:仲介人
 */
class Mediator extends Command\RequestHandler
{
    use Logging\LoggableTrait;

    public function __construct ( )
    {
        // インスタンスコンテナをセットアップ
        $this->instanceContainer = new Container\InstanceContainer( );

        // インスタンス作成時にonCreateメソッドを呼ばせる
        $this->instanceContainer->on('create',[$this, 'onCreate']);

        // モジュールをセットアップする
        $this->setupModules();
    }

    /**
     * モジュールをセットアップする
     */
    public function setupModules( )
    {

        // ロギング
        $this->on('execute', function ($e) {
            if (!($e('target') instanceof Module\FacadeIF)) return true;

            $method = $e->getParam('method');
            $args = Util::dump($e->getParam('args'), true);

            $this->info('Request Executed', [
                    '%s[%s] %s',
                    get_class($e('target')),
                    $method,
                    $args
                ]
            );
        });
    }

    /**
     * モジュールを登録する
     */
    public function register($name, $class, $args = [], $options = [])
    {
        $options['alias'] = $name;
        $this->instanceContainer->getFactory( )->register($class, $args, $options);
        return $this;
    }

    /**
     * モジュール生成時の処理
     */
    public function onCreate($event)
    {
        $instance = $event->getParam('instance');
        if (!$instance) {
            $event->dump();
        }
        $instance->addObserver($this);
        $instance->initWithMediator($this);

        $this->debug('*** MODULE CREATED ***',[
            '%s as %s',
            $event->getParam('name'),
            get_class($instance)
        ]);


        if (!($instance instanceof Module\FacadeIF)) {
            throw new ModuleDoseNotImplementsFacadeIF($event->getParam('name'));
        }
    }

    /**
     * モジュールのロード処理
     */
    public function load($name)
    {
        $instance = $this->instanceContainer->getInstance($name);
        return $instance;
    }


    /**
     * リクエスト受信時
     */
    public function recieve(Command\RequestIF $req) 
    {
        $name = $req->dict('target')->current( );

        try {
            $facade = $this->load($name);
        } catch (\Exception $e) {
            $req->result()->error(
                'LOAD_FAILED',
                sprintf('Cant Load %s Cose: %s',
                    $name, (string) $e
                )
            );
        }

        try {
            if (!$req->result()->isError()) {
                $facade->execute($req, $this);
            }
        } catch (\Exception $e) {
            $req->result()->error(
                'EXECUTE_FAILED',
                sprintf('%s[%s] Cose: %s Class: %s',
                    $name,
                    $req('method'),
                    (string) $e,
                    get_class($facade)
                )
            );
        }

        // エラー終了していたら
        if ($req->result()->isError()) {
            $this->warn(
                'RUNTIME_ERROR',
                [
                    '%s [%s] Cose:%s',
                    $req->dict('target')->implode(','),
                    $req('method'),
                    $req->result()->getErrorMessage()
                ]
            );
        }
    }
}
