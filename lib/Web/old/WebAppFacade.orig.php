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


/**
 * WEBモジュールファサード
 */
class WebFacade extends App\Base
{
    use Module\ModuleFacadeTrait;

    public function __construct(Module\ModuleIF $module = null)
    {
        if ($module) $this->setParentModule($module);
    }

    private $mounts = [];
    private $appPrefix;


    public function initFacade()
    {
        $this->initWebApp( );
    }

    /**
     * アプリケーションプレフィックス
     *
     * @param string
     */
    public function setAppPrefix($prefix)
    {
        $this->appPrefix = $prefix;
        return $this;
    }

    /**
     * アプリケーションをビルドする
     *
     * @param string
     */
    protected function buildApp($name)
    {
        $instance = Util::ClassName($this->appPrefix, $name)->newInstanceArgs( );
        if ($instance instanceof WebComponentIF) {
            $instance->initWebApp($this);
        }
        return $instance;
    }

    /**
     * アプリケーションをマウントする
     *
     * @param string
     * @param string
     */
    public function mount ($path, $class)
    {
        $this->debug('WEB',['Mounted %s %s', $path, $class]);
        $this->mounts[$path] = $class;
        return $this;
    }

    protected function prepare (&$request, &$response, &$dispatched, &$nest)
    {
        parent::prepare($request, $response, $dispatched, $nest);

        $path = $request->url()->toPath();

        foreach ($this->mounts as $k=>$v) {
            if(0 === strpos($path, $k)) {
                $next = substr($path,strlen($k));
                $next_request = clone $request;
                $next_request->url()->initPath($next);
                $this->info('WEB',[
                    'Mount Hit[%s:%s] >>> Origin:%s Next:%s <<<',
                    $k,
                    $v,
                    $path,
                    $next_request->url()->toPath()
                ]);

                $app = $this->buildApp($v);
                $app->setComponent('request', $next_request);
                $app->setComponent('response', $response);
                $app->run($next_request, $response, $dispatched, $nest + 1);
            }
        }
    }

}
