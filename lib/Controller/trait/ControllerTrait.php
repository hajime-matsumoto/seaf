<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Controller;

use Seaf\Component;
use Seaf\Com;
use Seaf\Com\Request\Request;
use Seaf\Container;
use Seaf\Routing;
use Seaf\Logging;
use Seaf\Event;
use Seaf\Annotation;

/**
 * コントローラ
 *
 * ComponentCompositeパターンを採用
 * MethodContainerパターンを採用
 */
trait ControllerTrait
{
    use Component\ComponentCompositeTrait;
    use Container\MethodContainerTrait;
    use Logging\LoggingTrait;
    use Event\ObservableTrait;

    /**
     * コントローラをイニシャライズする
     */
    protected function setupController ( )
    {
        $this->setupMethods( );
        $this->setupComponentLoader( );

        // コンポーネント作成時の処理を追加
        $this->on('component.create', function ($e) {
            $instance = $e->getVar('component');
            if ($instance instanceof Seaf\Controller\ComponentIF) {
                $instance->initControllerComponent($this);
            }
        });
    }

    /**
     * コンポーネントローダをセットアップする
     */
    protected function setupComponentLoader( )
    {
        // コンポーネントローダを追加
        $this->addComponentLoader(
            new Component\Loader\InitMethodLoader(
                $this
            )
        );
    }

    /**
     * アノテーションでセットアップする
     */
    protected function setupByAnnotation ( )
    {
        $anot = Annotation\AnnotationBuilder::build($this);

        // SeafRouteアノテーションからRouteを抜く
        $anot->mapMethodsHasAnot('SeafRoute', function ($method, $value) {
            $this->route($value, [$this, $method]);
            $this->debug([
                'Route Set By Annotation: %s %s', $method, $value
            ]);
        });

        // SeafEventOnアノテーションからEventを抜く
        $anot->mapMethodsHasAnot('SeafEventOn', function($method, $value){
            $this->on($value, [$this, $method]);
            $this->debug([
                'EventOn Set By Annotation: %s %s', $method, $value
            ]);
        });
    }

    /**
     * 定義されていないメソッドの呼び出し時の処理
     *
     * 1. MethodContainerの処理
     * 2. コンポーネントの取得
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function __call($name, $params)
    {
        if ($this->hasMethod($name)) {
            return $this->callMethodArray($name, $params);
        }
        $component = $this->getComponent($name);
        return $component;
    }


    //------------------------------------------
    // コンポーネント初期化用のメソッド
    //------------------------------------------

    /**
     * レジストリの作成
     *
     * @return Container\ArrayContainer
     */
    public function initRegistry ( )
    {
        $data = new Container\ArrayContainer();
        return $data;
    }

    /**
     * リクエストを取得する
     */
    public function initRequest ( )
    {
        $request = new Com\Request\Request( );
        return $request;
    }

    /**
     * リザルトを取得する
     */
    public function initResult ( )
    {
        $result = new Com\Result\Result();
        return $result;
    }

    /**
     * ルータを取得する
     */
    public function initRouter ( )
    {
        $router = new Routing\Router( );
        return $router;
    }
}
