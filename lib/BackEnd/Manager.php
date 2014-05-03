<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\BackEnd;

use Seaf\Base\Singleton;
use Seaf\Base\Command;
use Seaf\Base\Event;

/**
 * バックエンド:管理者
 */
class Manager extends Command\Commander implements Event\ObservableIF
{
    use Singleton\SingletonTrait;
    use Event\ObservableTrait;

    /**
     * 遅延束縛
     */
    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
    }

    /**
     * リクエストを取得する
     */
    public function __get($name)
    {
        return $this->newRequest()->scope($name);
    }

    /**
     * リクエストを受け付ける
     *
     * @See Seaf\Base\Command\Commander
     */
    public function recieveRequest(Command\Request $request)
    {
        $request->setResult(new Command\Result( ));

        try
        {
            $this->trigger('request.recieved', [
                'request' => $request
            ]);

            // スコープをクラス名に変換
            $class_name = $request->getScopeToString('\\');
            $method     = $request->getName();
            $args       = $request->getParams();

            // ハンドラクラスをロードする
            $handler = $this->loadHandler($class_name);

            $this->trigger('before.callHandler', [
                'request' => $request
            ]);

            // ハンドラのメソッドを呼び出す
            $request->getResult()->returnValue(
                call_user_func_array([$handler, $method], $args)
            );
        } catch (\Exception $e) {
            $request->getResult()->error(
                'EXCEPTION',
                ['message' => (string) $e]
            )->returnValue(false);
        }

        $this->trigger('after.request', [
            'request' => $request,
            'result' => $request->getResult()
        ]);
    }

    /**
     * 現在のインスタンスをシングルトンに登録する
     */
    public function register ( )
    {
        $this->registerSingleton();
    }

    public function showHelp( )
    {
        var_dump(func_get_Args());
        return 'たっけてー';
    }

    protected function loadHandler ($class_name)
    {
        $this->trigger('before.loadHandler', [
            'className' => &$class_name
        ]);

        if (!class_exists($class_name)) {
            throw new \Exception(sprintf('CLASS %s NOT FOUND',$class_name));
        }
        $handler = new $class_name();

        $this->trigger('after.loadHandler', [
            'className' => &$class_name,
            'handler' => $handler
        ]);
        return $handler;
    }
}
