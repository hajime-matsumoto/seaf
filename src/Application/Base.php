<?php
namespace Seaf\Application;

use Seaf\Exception\Exception;
use Seaf\Data\Container;
use Seaf\Net\Request;

class Base extends Container\Base
{
    /**
     * アプリケーションをマウントする
     * @var array
     */
    public $mounts = array();
    /**
     * アプリケーション用の環境
     * @var Environment
     */
    protected $environment;

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->_initApplication();
    }

    public function init ( )
    {
        $this->_initApplication();
        return $this;
    }

    /**
     * 初期化処理
     */
    private function _initApplication ( )
    {
        $this->environment = new Environment($this);
        $this->environment->bind($this->environment, array(
            'di' => 'di'
        ));

        $this->environment->bind($this, array(
            'route'              => '_route',
            'run'                => '_run',
            'mount'              => '_mount',
            'beforeDispatchLoop' => '_beforeDispatchLoop',
            'afterDispatchLoop'  => '_afterDispatchLoop'
        ));

        $this->event()
            ->on('before.dispatch-loop', 'beforeDispatchLoop')
            ->on('after.dispatch-loop', 'afterDispatchLoop');

        $this->initApplication();
    }

    /**
     * RUNの初期に呼ばれる
     */
    public function _beforeDispatchLoop($req, $res, $app)
    {
        ob_start();
    }

    /**
     * RUNの終わりに呼ばれる
     */
    public function _afterDispatchLoop($req, $res, $app, $isDispatched)
    {
        if ($isDispatched == false) {
            return false;
        }

        $body = ob_get_clean();
        $res->write($body)->send();
    }

    /**
     * 継承したメソッド用に初期化メソッドを残す
     */
    protected function initApplication( )
    {
    }

    /**
     * Call
     */
    public function __call ($name, $params)
    {
        return $this->environment->call($name, $params);
    }

    /**
     * ルートを設定する
     *
     * @param string
     * @param mixed
     * @return Base
     */
    public function _route ($pattern, $command)
    {
        $this->router()->map($pattern, $command);
        return $this;
    }

    /**
     * マウントする
     *
     * @param string
     * @param string
     */
    public function _mount ($path, $name)
    {
        $this->mounts[$path] = $name;
        return $this;
    }

    /**
     * 実行
     *
     * @param Request
     * @param Response
     * @return Base
     */
    public function _run (
        Request\Base $req = null,
        Component\Response $res = null,
        $isDispatched = false)
    {
        $this->logger()->debug('Request-Recived-Class : '.get_class($this));

        // リクエストを処理
        if ($req == null) $req = $this->request();
        $this->logger()->debug('Request-Recived : '.(string) $req);

        // レスポンスを初期化
        if ($res == null) $res = $this->response();

        // ディスパッチループ開始
        $this->event()->trigger('before.dispatch-loop', $req, $res, $this);


        foreach ($this->mounts as $path=>$name) {
            if (strpos($req->uri,$path) === 0) {
                $this->logger()->debug('Mount PATH HIT: '.$path);

                $next_req = clone $req;
                $next_req->addBasePath($path);


                $this->logger()->debug(array(
                    'Mount Request URI:%s ORIGIN:%s HIT:%s',
                    $next_req->uri, $req->uri, $path
                ));

                if ( $this->environment->di( )->has($name) ) {
                    $instance = $this->environment->di()->get($name);
                } elseif (class_exists($name)) {
                    $instance = new $name();
                } else {
                    throw new Exception(array(
                        '%s実行できないマウントです',
                        $name
                    ));
                }

                $instance->di()->register('request', $next_req);
                $instance->di()->register('response', $res);
                $instance->run($next_req, $res, $isDispatched);
            }
        }

        // ルーティング処理
        while ($isDispatched != true && $route = $this->router()->route($req)) {

            $this->event()->trigger('before.dispatch', $route, $req, $res, $this);
            $result = $route->execute($req, $res, $this);
            $this->event()->trigger('after.dispatch', $route, $req, $res, $this);

            if ($result !== false) {
                $isDispatched = true;
                break;
            }

            $this->router()->next();
        }

        // ディスパッチループ終了
        $this->event()->trigger('after.dispatch-loop', $req, $res, $this, $isDispatched);

        if ($isDispatched) {
        } else {
            $this->event()->trigger('notfound', $req, $res, $this);
            $this->logger()->debug('Route Not Found '.$req->uri);
        }
        $this->logger()->debug('Dispatch-Status : '.($isDispatched ? "true":"false"));
        $this->logger()->debug('Response-Sent : '.(string) $res);
    }
}
