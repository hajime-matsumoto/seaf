<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Controller;

use Seaf\Component;
use Seaf\Com;
use Seaf\Com\Request\Request;
use Seaf\Com\Result\Result;
use Seaf\Container;
use Seaf\Routing;
use Seaf\Logging;
use Seaf\Wrapper;

/**
 * コントローラ
 *
 * ComponentCompositeパターンを採用
 * MethodContainerパターンを採用
 */
class Controller
{
    use ControllerTrait;

    /**
     * @var array
     */
    private $mounts = [];

    /**
     * コンストラクタ
     */
    public function __construct( )
    {
        $this->setupController( );
    }

    /**
     * ログ
     */
    public function logPost(Logging\Log $Log)
    {
        $Log->addTag(get_class($this));
        Logging\LogHandler::getSingleton()->logPost($Log);
    }

    /**
     * メソッドをセットアップする
     */
    protected function setupMethods ( )
    {
        $this->setMethod([
            'route'    => '_route',
            'run'      => '_run',
            'mount'    => '_mount',
            'notfound' => '_notfound'
        ]);
    }

    //------------------------------------------
    // ダイナミックメソッド用のメソッド
    //------------------------------------------

    /**
     * ルートを作成する
     *
     * @param string
     * @param callback
     * @return Controller
     */
    public function _route ($pattern, $action)
    {
        $this->trigger('before.route', [
            'pattern' => $pattern,
            'action' => $action
        ]);

        $this->Router( )->map($pattern, $action);

        $this->trigger('after.route', [
            'pattern' => $pattern,
            'action' => $action
        ]);
        return $this;
    }

    /**
     * マウントパスを追加する
     *
     * @param string
     * @param string
     * @return Controller
     */
    public function _mount ($path, $class)
    {
        $this->mounts[$path] = $class;
        return $this;
    }

    /**
     * Notfouund
     */
    public function _notfound ($Request = null)
    {
        if ($Request == null) $Request = $this->Request();

        $this->warn([
            'NotFound: Path %s',
            $Request->getPath()
        ]);

        $this->trigger('notfound', [
            'Request' => $Request
        ]);
    }

    /**
     * コントローラを実行する
     *
     * @param Request
     * @param Result
     * @return bool ディスパッチされたか
     */
    public function _run (Request $Request = null, Result $Result = null, $nestLevel = 0)
    {
        if ($Request == null) $Request = $this->Request();
        if ($Result == null) $Result = $this->Result();
        $Router = $this->Router();

        // ディスパッチフラグ
        $dispatched = false;

        $this->trigger('before.run', [
            'request' => $Request,
            'result' => $Result
        ]);

        // マウントされたパスを処理する
        if ($this->mountProcess($Request, $Result, $nestLevel)) {
            return true;
        }


        // ルートを検索する
        while($Route = $Router->route($Request)) {
            $closure = $Route->getClosure();

            $endStatus = $closure->invokeArgs(
                $Route->getParams([
                    $Request,
                    $Result,
                    $this
                ])
            );

            if ($endStatus === false) {
                $Router->next();
                continue;
            }else{
                $dispatched = true;
            }

            break;
        }

        $this->trigger('after.run', [
            'request' => $Request,
            'result'  => $Result
        ]);

        if ($dispatched == true) {
            $this->info([
                'Dipatched: %s %s; Controller: %s;',
                $Request->getMethod( ),
                $Request->getPath( ),
                get_class($this)
            ]);
        }elseif ($nestLevel == 0) { // 完全にNotFound
            $this->notfound($Request);
        }
        return $dispatched;
    }

    /**
     * マウントされたパスを処理する
     *
     * @param Request
     * @param Result
     * @param int
     * @return bool
     */
    protected function mountProcess (Request $Request, Result $Result, $nestLevel)
    {
        foreach ($this->mounts as $path => $class) {
            if (strpos($Request->getPath(), $path) === 0) {
                $this->debug([
                    'Mount: Hit %s Cose %s', $path, $Request->getPath()
                ]);
                $Ctrl = Wrapper\ReflectionClass::create($class)->newInstance();
                $newRequest = clone $Request;
                $newRequest->mask($path);
                $this->debug([
                    'Rewrite Path: %s to %s', $Request->getPath(), $newRequest->getPath()
                ]);
                $Ctrl->setComponent('Request', $newRequest);
                $Ctrl->setComponent('Result', $Result);
                $dispatched = $Ctrl->run($newRequest, $Result, $nestLevel + 1);
                $this->debug([
                    'Mount Dispatched: %s', $dispatched ? 'true': 'false'
                ]);
                if ($dispatched == true) return true;
            }
        }
        return false;
    }
}
