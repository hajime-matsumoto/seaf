<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web;

use Seaf\Base\Module;
use Seaf\Util\Util;
use Seaf\Web;
use Seaf\Base\DI;
use Seaf\Base\Component;


/**
 * WEBモジュールファサード
 */
class WebFacade extends App\Base
{
    protected static $object_name = 'WEB';

    private $mounts;

    /**
     * コンストラクタ
     */
    public function __construct(Component\ComponentIF $caller = null)
    {
        if ($caller) $this->setParentModule($caller);

        // マウント
        $this->mounts  = Util::Dictionary();

        // アプリの初期化
        $this->setupApplication();
    }

    protected function setupApplication ( ) 
    {
        parent::setupApplication();

        // メソッドの追加
        $this->mapMethod([
            'notfound' => '_notfound'
        ]);

        // イベントの追加
        $this->on([
            'afterRun' => [$this, 'onAfterRun'],
            'beforeDispatchLoop' => [$this, 'onBeforeDispatchLoop']
        ]);
    }

    public function onBeforeDispatchLoop($e)
    {
        $params = $e->getParams();
        $request = $e->request;
        $response = $e->response;
        $nest = $e->nest;
        $dispatched =& $params['dispatched'];

        $path = $request->getPath();

        foreach ($this->mounts as $k=>$v) {
            if(0 === strpos($path, $k)) {
                $next = substr($path,strlen($k));
                $next_request = clone $request;
                $next_request->url()->initPath($next);
                $this->info([
                    'Mount Hit[%s:%s] >>> Origin:%s Next:%s <<<',
                    $k,
                    $v,
                    $path,
                    $next_request->url()->toPath()
                ]);
                $app = $this->buildApp($v);
                $app->run($next_request, $response, $dispatched, $nest + 1);
            }
        }
    }

    /**
     * アプリケーションをビルドする
     *
     * @param string
     */
    protected function buildApp($name)
    {
        $instance = Util::ClassName($name)->newInstance($this);
        if ($instance instanceof WebComponentIF) {
            $instance->initWebApp($this);
        }
        return $instance;
    }

    public function onAfterRun ($e)
    {
        if ($e->dispatched == false)
        {
            $this->notfound(sprintf("404 Not Found %s", $e->request->url()));
        }
    }

    public function _notfound($message = '404 Not Found', $code = 404)
    {
        $this->warn("Not Found ".$this->loadComponent('request')->url());
        $this->loadComponent('response')->clear()->status($code)->write($message)->send();
    }

    public function mount ($path, $action)
    {
        $this->mounts->set($path, $action);
    }
}
