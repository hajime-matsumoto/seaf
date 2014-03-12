<?php
namespace Seaf\Application;

use Seaf\Environment\Environment;
use Seaf\Application\Component\RequestComponent;
use Seaf\Application\Component\ResponseComponent;

/**
 * Application Manager
 */
abstract class Base extends Environment
{
    public $name = 'Application\Base';
    public $mounts = array();
    public $halt = false;
    /**
     * @var Environment
     */
    protected $env;

    public function __construct ( )
    {
        parent::__construct();
        $this->event()
            ->on('before.dispatch-loop', '_beforeDispatchLoop')
            ->on('after.dispatch-loop', '_afterDispatchLoop');

        // アノテーションバインディング
        $this->kernel()->ReflectionClass($this)->mapAnnotation(function($method, $anots){
            if (isset($anots['route'])) {
                $this->router()->map($anots['route'], $method->getClosure($this));
            }
            if (isset($anots['event'])) {
                $this->event()->on($anots['event'], $method->getClosure($this));
            }
        });
        $this->initApplication();
    }

    public function _beforeDispatchLoop($req, $res, $app)
    {
        ob_start();
    }

    public function _afterDispatchLoop($req, $res, $app)
    {
        $body = ob_get_clean();
        $res->write($body);
    }

    public function mount ($path, $name)
    {
        $this->mounts[$path] = $name;
        return $this;
    }

    public function run (RequestComponent $request = null, ResponseComponent $response = null)
    {
        // リクエストを処理
        if ($request == null) $request = $this->request();
        $this->logger()->debug('Request-Recived : '.(string) $request);

        // レスポンスを初期化
        if ($response == null) $response = $this->response();

        // ディスパッチループ開始
        $this->event()->trigger('before.dispatch-loop', $request, $response, $this);

        $isDispatched = false;

        foreach ($this->mounts as $path=>$name) {
            if (strpos($request->uri,$path) === 0) {
                $this->logger()->debug('Mount PATH HIT: '.$path);
                $uri = substr($request->uri,strlen($path));

                $req = clone($request);
                $req->uri = $uri;

                $this->di($name)->run($req, $response);
                $isDispatched = true;
            }
        }

        // ルーティング処理
        while ($isDispatched != true && $route = $this->router()->route($request)) {

            $this->event()->trigger('before.dispatch', $route, $request, $response, $this);
            $result = $route->execute($request, $response, $this);
            $this->event()->trigger('after.dispatch', $route, $request, $response, $this);

            if ($result !== false) {
                $isDispatched = true;
                break;
            }

            $this->router()->next();
        }

        // ディスパッチループ終了
        $this->event()->trigger('after.dispatch-loop', $request, $response, $this);

        if ($isDispatched) {
        } else {
            $this->event()->trigger('notfound', $request, $response, $this);
            $this->logger()->debug('Route Not Found');
        }
        $this->logger()->debug('Response-Sent : '.(string) $response);
    }


    abstract public function initApplication ( );
}
