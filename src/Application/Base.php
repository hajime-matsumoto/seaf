<?php
namespace Seaf\Application;

class Base
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

    /**
     * 初期化処理
     */
    private function _initApplication ( )
    {
        $this->environment = new Environment($this);

        $this->environment->bind($this, array(
            'route' => '_route',
            'run'   => '_run',
            'mount' => '_mount'
        ));

        $this->event()
            ->on('before.dispatch-loop', '_beforeDispatchLoop')
            ->on('after.dispatch-loop', '_afterDispatchLoop');

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
    public function _afterDispatchLoop($req, $res, $app)
    {
        $body = ob_get_clean();
        $res->write($body);
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
    public function _run (Request $req = null, Response $res = null)
    {
        // リクエストを処理
        if ($req == null) $req = $this->request();
        $this->logger()->debug('Request-Recived : '.(string) $req);

        // レスポンスを初期化
        if ($res == null) $res = $this->response();

        // ディスパッチループ開始
        $this->event()->trigger('before.dispatch-loop', $req, $res, $this);

        $isDispatched = false;

        foreach ($this->mounts as $path=>$name) {
            if (strpos($req->uri,$path) === 0) {
                $this->logger()->debug('Mount PATH HIT: '.$path);
                $uri = substr($req->uri,strlen($path));

                $req = clone($req);
                $req->uri = $uri;

                $this->di($name)->run($req, $res);
                $isDispatched = true;
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
        $this->event()->trigger('after.dispatch-loop', $req, $res, $this);

        if ($isDispatched) {
        } else {
            $this->event()->trigger('notfound', $req, $res, $this);
            $this->logger()->debug('Route Not Found');
        }
        $this->logger()->debug('Response-Sent : '.(string) $res);
    }



}
