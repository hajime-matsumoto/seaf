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


/**
 * WEBモジュールファサード
 */
class WebAppFacade implements WebComponentIF
{
    use WebComponentTrait;
    use DI\DITrait;

    protected $root_path;
    protected $views_path = 'views';

    private $mounts;

    /**
     * アプリケーションプレフィックス
     *
     * @var string
     */
    private $appPrefix;

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->initFacade();
    }
    /**
     * 初期化
     */
    protected function initFacade( )
    {
        $this->mounts = Util::Dictionary();

        // ルートパスを設定する
        $this->root_path = $this->root()->registry->get('root_path');
        $this->initDI();
        $this->initApp( );
    }

    protected function onInstanceCreate($e)
    {
        $e->instance->addObserver($this);
        if (method_exists($e->instance, 'initWebComponent')) {
            $e->instance->initWebComponent($this);
        }
    }

    protected function setupDIComponents($di)
    {
        $di->register([
            'request'  => __NAMESPACE__.'\WebRequest',
            'response' => __NAMESPACE__.'\WebResponse',
            'router'   => __NAMESPACE__.'\WebRouter',
            'view'     => __NAMESPACE__.'\WebView',
        ]);
    }

    protected function initApp ( )
    {
    }

    /**
     * アプリのパスを取得する
     */
    protected function getPath( )
    {
        return (string) Util::FileName($this->root_path, func_get_args());
    }

    /**
     * アノテーションでセットアップする
     */
    protected function setupByAnnotation ( )
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
        $instance->addObserver($this);
        $instance->setParent($this);
        $instance->initFacade( );
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

    /**
     * URLをマップする
     *
     * @param string
     * @param string
     */
    public function route ($path, callable $action)
    {
        $this->debug('WEB',['Routed %s', $path]);
        $this->router()->map($path, $action);
        return $this;
    }

    /**
     * リクエストをセットする
     */
    public function setRequest (WebRequest $request)
    {
        return $this->instanceManager->register('request', $request);
    }

    public function setResponse (WebResponse $response)
    {
        return $this->instanceManager->register('response', $response);
    }

    public function request ( )
    {
        return $this->instanceManager->getInstance('request');
    }

    public function response ( )
    {
        return $this->instanceManager->getInstance('response');
    }


    protected function router ( )
    {
        return $this->instanceManager->getInstance('router');
    }

    public function getComponent($name)
    {
        return $this->instanceManager->getInstance($name);
    }

    public function view( )
    {
        return $this->getWebParent( )->getComponent('view');
    }

    /**
     * テンプレートをセットする
     */
    public function template($tpl)
    {
        return $this->view()->template($tpl);
    }


    protected function getWebParent( )
    {
        if ($this->hasParent()) {
            $parent = $this->getParent();
            if ($parent instanceof WebAppFacade) {
                $parent = $parent->getWebParent();
                return $parent;
            }else{
                $parent = $this;
            }
        }

        $this->debug('WEB', [
            'Get Web Parent >>> %s to %s <<<',
            get_class($this),
            get_class($parent)
        ]);

        return $this;
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

        // 出力処理
        $this->display();
    }

    protected function startMountProcess($request, $response, &$dispatched, &$nest)
    {
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
                $app->setRequest($next_request);
                $app->setResponse($response);
                $app->run($next_request, $response, $dispatched, $nest + 1);
            }
        }
    }

    /**
     * 実行
     *
     * @param string
     * @param string
     */
    public function run ($request = null, $response = null, &$dispatched = false, $nest = 0)
    {
        if (!$request) {
            $request = $this->request();
        }
        if (!$response) $response = $this->response();

        // リクエストからURLを取得
        $url  = $request->url();
        $path = $url->toPath();

        if ($nest == 0) {
            $this->info('WEB', ['URL >>> %s:%s <<<', $url, $path]);
            $this->beforeDispatchLoop($request, $response, $dispatched);
        }else{
            $this->debug('WEB',['Nest(%s) Path(%s) >>> %s <<<', $nest, $path, get_class($this)]);
        }

        $this->startMountProcess($request, $response, $dispatched, $nest);

        // ルーティング
        $router = $this->instanceManager->getInstance('router');

        while($route = $router->route($request)) {
            $result = $route->execute($request, $response, $this);

            if ($result !== false) {
                $dispatched = true;
                break;
            }
            $route->next();
        }

        if ($nest == 0) $this->afterDispatchLoop($request, $response, $dispatched);
    }

    public function display ( )
    {
        // Viewを使う場合
        if ($this->view( )->isEnabled( )) {
            $template_name = $this->view()->getTemplateName();
            $template_path = $this->getPath($this->views_path);

            $this->debug('WEB|VIEW', [
                'Enabled Template(%s) Path(%s)',
                $template_name,
                $template_path
            ]);

            $arr    = $this->response()->toArray();
            $params = $arr['params'];
            $params['yield'] = $arr['body'];

            $this->view( )->display($this->response());
            return;
        }

        $this->response()->send();
        return;
    }
}
