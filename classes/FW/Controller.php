<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW;

use Seaf;
use Seaf\Base;
use Seaf\Container\ArrayContainer;

/**
 * FW用のController
 */
class Controller
{
    use Base\SeafAccessTrait;
    use Base\LoggerTrait;
    use Base\EventTrait;
    use Base\ComponentCompositeTrait;
    use Base\DynamicMethodTrait;

    protected $name = 'FW';

    private $mounts = array();

    public function __construct ( )
    {
        $this->setComponentContainer('Seaf\FW\ComponentContainer');
        $this->mounts = new ArrayContainer( );
    }

    public function callFallBack($name, $params)
    {
        return $this->componentCall($name, $params);
    }

    /**
     * コントローラの初期化
     */
    public function initController( )
    {
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

            // Routingを処理
            // @SeafRoute パス
            if (array_key_exists('route', $anots)) {
                foreach ($anots['route'] as $route) {
                    $this->route($route, $method->getClosure($this));
                }
            }
            // イベントを処理
            // @SeafEvent イベント名
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
        if ($request == null)  $request = $this->request();
        if ($response == null) $response = $this->response();

        $this->debug(['Recived-Path: %s', $request->getPath()]);


        // --------------------------------------------------
        // マウントの処理
        // --------------------------------------------------
        $dispatchFlag = $this->procMounts($request, $response, $dispatchFlag);

        // --------------------------------------------------
        // ルートの処理
        // --------------------------------------------------
        $this->trigger('before.run', [
            'request'      => $request,
            'response'     => $response,
            'dispatchFlag' => &$dispatchFlag,
            'controller'   => $this
        ]);

        while ($route = $this->router()->route($request)) {

            $this->dispatch($route, $request, $response, $dispatchFlag);

            if ($dispatchFlag == true)
            {
                break;
            }

            $this->router()->next();
        }

        $this->trigger('after.run', [
            'request'      => $request,
            'response'     => $response,
            'dispatchFlag' => &$dispatchFlag,
            'controller'   => $this
        ]);
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
        } elseif (is_object($mount) && !($mount instanceof \Closure)) {
            $ctl = $mount;
        } elseif (is_callable($mount)) {
            $ctl = $mount();
        } elseif (is_string($mount)) {
            $ctl = $this->di($mount);
        }
        return $ctl;
    }

    private function procMounts($request, $response, $dispatchFlag)
    {
        // --------------------------------------------------
        // マウントの処理
        // --------------------------------------------------
        foreach ($this->mounts as $path=>$m) 
        {
            if (0 === strpos($request->getPath(), $path)) {
                // リクエスト情報をコピーする
                $newRequest = clone $request;
                // URIをマスクする
                $newRequest->pathMask($path);
                // マウントしているコントローラを実体化
                $ctl = $this->buildMount($m);
                $ctl->component()->register('request', $newRequest);
                $ctl->component()->register('response', $response);

                // ログを吐いておく
                $this->debug(array(
                    "Mounted: Path %s To %s Class: %s",
                    $request->getPath(),
                    $newRequest->getPath(),
                    get_class($ctl)
                ));

                $dispatchFlag = $ctl->run($newRequest, $response, $dispatchFlag);

                // ディスパッチ成功していたら以降処理しない
                if ($dispatchFlag == true) {
                    return true;
                }
            }
        }
        return false;
    }

}
