<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW;

use Seaf;
use Seaf\Pattern;

/**
 * FW用のController
 */
class Controller
{
    use Pattern\Environment;
    protected $name = 'FW';

    private $mounts = array();

    public function __construct ( )
    {
        $this->initController( );
    }

    public function initController( )
    {
        $this->initEnvironment();

        // -----------------------------------
        // DIの調整
        // -----------------------------------
        $di = $this->di();
        $di->factory->configAutoload(__NAMESPACE__.'\\Component\\');
        $di->register('logger', function ( ) {
            return Seaf::logger($this->name);
        });

        // DI作成時にAcceptControllerを呼ぶ
        // -----------------------------------
        $di->on('create', function ($instance) {
            if (method_exists($instance, 'acceptController')) {
                $instance->acceptController($this);
            }
        });

        // -----------------------------------
        // マップ処理
        // -----------------------------------
        $this->bind($this, array(
            'route' => '_route',
            'run'   => '_run',
            'mount' => '_mount'
        ));

        // -----------------------------------
        // アノテーション処理
        // -----------------------------------
        Seaf::ReflectionClass($this)->mapAnnotation(function($method, $anots) {
            if (array_key_exists('route', $anots)) {
                foreach ($anots['route'] as $route) {
                    $this->route($route, $method->getClosure($this));
                }
            }
            if (array_key_exists('event', $anots)) {
                foreach ($anots['event'] as $event) {
                    $this->on($event, $method->getClosure($this));
                }
            }
        });

    }

    /**
     * マウントを追加する
     *
     * @param string
     * @param Controller
     */
    public function _mount ($path, $controller)
    {
        $this->mounts[$path] = $controller;
        return $this;
    }

    /**
     * ルートを追加する
     *
     * @param string
     * @param callback
     */
    public function _route ($pattern, $action)
    {
        $this->router()->map($pattern, $action);
        return $this;
    }

    /**
     * 実行する
     *
     * @param Request
     * @param Response
     * @param bool
     */
    public function _run ($request = null, $response = null, $dispatchFlag = false)
    {
        if ($request == null) $request = $this->request();
        if ($response == null) $response = $this->response();

        $this->logger($this->name)->debug(array(
            'Recived-Path: %s', $request->uri()->path()
        ));

        // --------------------------------------------------
        // マウントの処理
        // --------------------------------------------------
        foreach ($this->mounts as $path=>$m) 
        {
            if (0 === strpos($request->uri()->path(), $path)) {
                // リクエスト情報をコピーする
                $newRequest = $request->getClone();
                // URIをマスクする
                $newRequest->uri()->set('mask', $path);
                // マウントしているコントローラを実体化
                $ctl = $this->buildMount($m);
                $ctl->register('request', $newRequest);
                $ctl->register('response', $response);

                // ログを吐いておく
                $this->logger($this->name)->debug(array(
                    "Mounted: Path %s To %s Class: %s",
                    $request->uri()->path(),
                    $newRequest->uri()->path(),
                    get_class($ctl)
                ));

                $dispatchFlag = $ctl->run($newRequest, $response, $dispatchFlag);

                // ディスパッチ成功していたら以降処理しない
                if ($dispatchFlag == true) {
                    return true;
                }
            }
        }

        // --------------------------------------------------
        // ルートの処理
        // --------------------------------------------------
        $this->trigger('before.run', $request, $response, $this);
        while ($route = $this->router()->route($request)) {

            $this->dispatch($route, $request, $response, $dispatchFlag);

            if ($dispatchFlag == true)
            {
                break;
            }

            $this->router()->next();
        }

        $this->triggerArgs('after.run', array(
            $request,
            $response,
            &$dispatchFlag,
            $this
        ));
        return $dispatchFlag;
    }

    /**
     * ヒットしたルートをディスパッチする
     *
     * @param Route
     * @param Request
     * @param Response
     * @param bool
     */
    public function dispatch ($route, $request, $response, &$dispatchFlag)
    {
        $this->trigger('before.dispatch', $request, $response, $dispatchFlag);


        $result = call_user_func_array($route->getAction(), $route->getParams(
            array($request, $response,$this)
        ));

        if ($result !== false) {
            $dispatchFlag = true;
        }

        $this->trigger('after.dispatch', $request, $response, $dispatchFlag);

        return $result;
    }

    // -----------------------------------------------
    // utility
    // -----------------------------------------------
    private function buildMount($mount)
    {
        if (is_string($mount) && class_exists($mount)) {
            $ctl = new $mount();
        } elseif (is_object($mount)) {
            $ctl = $mount;
        } elseif (is_callable($mount)) {
            $ctl = $mount();
        } elseif (is_string($mount)) {
            $ctl = $this->env->di($mount);
        }
        return $ctl;
    }
}
