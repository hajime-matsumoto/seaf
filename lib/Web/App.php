<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web;

use Seaf\BackEnd;
use Seaf\Util\Util;
use Seaf\Web;
use Seaf\Base\DI;
use Seaf\Base\Event;


/**
 * WEBモジュールアプリケーション
 */
abstract class App implements WebComponentIF
{
    use WebComponentTrait;
    use DI\DITrait;

    private $methods;
    private $root_path;
    private $views_path;

    public function setRootPath($path)
    {
        $this->root_path = $path;
        return $this;
    }

    /**
     * アプリのパスを取得する
     */
    public function getPath( )
    {
        return (string) Util::FileName($this->root_path, func_get_args());
    }

    public function getViewPath( )
    {
        return (string) Util::FileName($this->views_path, func_get_args());
    }

    public function initWebApp ($component = null)
    {
        $this->initDI();
        $this->methods = Util::MethodContainer( );
        $this->methods->set(
            'display',
            [$this, '_display']
        );
        $this->initWebComponent($component);
    }

    public function getMethodsContainer( )
    {
        return $this->methods;
    }

    public function __call ($name, $params)
    {
        if ($this->methods->has($name)) {
            return $this->methods->callArray($name, $params);
        }
        throw new \Exception (sprintf('Invalid Method %s', $name));
    }

    public function initWebComponent(WebComponentIF $component = null)
    {
        if ($component) {
            $this->setWebComponentParent($component);
        }
    }

    protected function setup ( )
    {
        $anot = $this->root()->annotation->build($this);

        // SeafRouteアノテーションからRouteを抜く
        $anot->mapMethodsHasAnot('SeafRoute', function ($method, $value) {
            $this->debug(get_class($this),[
                'Route Set By Annotation: %s %s', $method, $value
            ]);
            $this->route($value, [$this, $method]);
        });

        // SeafEventOnアノテーションからEventを抜く
        $anot->mapMethodsHasAnot('SeafEventOn', function($method, $value){
            $this->debug(get_class($this),[
                'EventOn Set By Annotation: %s %s', $method, $value
            ]);
            $this->on($value, [$this, $method]);
        });
    }

    public function onInstanceCreate(Event\EventIF $e)
    {
        $instance = $e->instance;
        if ($instance instanceof WebComponentIF) {
            $instance->initWebComponent($this);
        }
    }

    protected function setupDIComponents(DI\InstanceManagerIF $DI)
    {
        $DI->register([
            'request'  => __NAMESPACE__.'\WebRequest',
            'response' => __NAMESPACE__.'\WebResponse',
            'router'   => __NAMESPACE__.'\WebRouter',
            'view'     => __NAMESPACE__.'\WebView',
        ]);
    }

    public function view ( )
    {
        return $this->getComponent('view');
    }

    /**
     * URLをマップする
     *
     * @param string
     * @param string
     */
    public function route ($path, callable $action)
    {
        $this->debug('WEB',['Routed %s', $path]);
        $this->getComponent('router')->map($path, $action);
        return $this;
    }

    /**
     * 実行
     *
     * @param string
     * @param string
     */
    public function run ($request = null, $response = null, &$dispatched = false, $nest = 0)
    {
        $this->prepare($request, $response, $dispatched, $nest);

        if ($dispatched) {
            return;
        }

        // ルーティング
        $router = $this->getComponent('router');

        while($route = $router->route($request)) {
            $result = $route->execute($request, $response, $this);

            if ($result !== false) {
                $dispatched = true;
                break;
            }
            $route->next();
        }

        if ($dispatched) {
            $this->afterDispatchLoop($request, $response, $dispatched);
        }elseif ($nest == 0) {
            $this->notfound();
        }
    }

    public function _display( )
    {
        $this->fireEvent('web.before-display');
        $this->getComponent('response')->send();
        $this->fireEvent('web.after-display');
        return;
    }

    public function notfound( )
    {
        $this->getComponent('response')
            ->clear( )
            ->status(404)
            ->write('404 not found')
            ->send();
    }

    protected function prepare (&$request, &$response, &$dispatched, &$nest)
    {
        if (!$request) {
            $request = $this->getComponent('request');
        }else{
            $this->setComponent('request', $request);
        }
        if (!$response) {
            $response = $this->getComponent('response');
        }else{
            $this->setComponent('response', $response);
        }

        // リクエストからURLを取得
        $url  = $request->url();
        $path = $url->toPath();

        if ($nest == 0) {
            $this->info('WEB', ['URL >>> %s:%s <<<', $url, $path]);
            $this->beforeDispatchLoop($request, $response, $dispatched);
        }else{
            $this->debug('WEB',['Nest(%s) Path(%s) >>> %s <<<', $nest, $path, get_class($this)]);
        }
    }


    protected function beforeDispatchLoop($request, $response, &$dispatched)
    {
        $this->fireEvent(
            'web.before-dispatch-loop', [
                'request' => $request,
                'response' => $response,
                'dispatched' => &$dispatched
            ]
        );
    }

    protected function afterDispatchLoop($request, $response, &$dispatched)
    {
        $this->fireEvent(
            'web.after-dispatch-loop', [
                'request' => $request,
                'response' => $response,
                'dispatched' => &$dispatched
            ]
        );

        if ($dispatched == false) {
            $this->info('WEB', ['Notfound %s', $request->url()]);
            $this->fireEvent(
                'web.notfound', [
                    'request' => $request,
                    'response' => $response,
                    'dispatched' => &$dispatched
                ]
            );
        }

        $this->debug('WEB', ["Dispatched %s", get_class($this)]);

        // 出力処理
        if ($dispatched) {
            $this->display();
        }else{
            $this->notfound();
        }
    }

}
