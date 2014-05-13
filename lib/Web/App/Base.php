<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web\App;

use Seaf\Base\Module;
use Seaf\Util\Util;
use Seaf\Web;
use Seaf\Base\DI;
use Seaf\Base\Component;
use Seaf\Base\ExtendableMethodTrait;


/**
 * WEBアプリケーションベース
 */
class Base implements Web\AppIF
{
    use ExtendableMethodTrait;
    use Web\AppTrait;

    /**
     * コンストラクタ
     */
    public function __construct(Component\ComponentIF $parent)
    {
        $this->setParentWebComponent($parent);
        $this->setupApplication();
    }

    protected function setupApplication ( ) 
    {
        // View
        $this->registerComponent('view', 'Seaf\Web\Component\View');

        // メソッドの設定の設定
        $this->mapMethod([
            'display' => '_display'
        ]);

        // イベントの設定
        $this->on([
            'afterDispatchLoop' => [$this, 'onAfterDispatchLoop']
        ]);
    }

    protected function setupByAnnotation( )
    {
        $anot = $this->module('util')->annotation->build($this);

        // SeafRouteアノテーションからRouteを抜く
        $anot->mapMethodsHasAnot('SeafRoute', function ($method, $value) {
            $this->info([
                'Route Set By Annotation: %s %s', $value, $method
            ]);
            $this->route($value, [$this, $method]);
        });

        // SeafEventOnアノテーションからEventを抜く
        $anot->mapMethodsHasAnot('SeafEventOn', function($method, $value){
            $this->debug([
                'EventOn Set By Annotation: %s %s', $value, $method
            ]);
            $this->on($value, [$this, $method]);
        });
    }

    final private function _display ( )
    {
        $response = $this->loadComponent('response');
        $response->send();
    }

    public function onAfterDispatchLoop($e) 
    {
        if ($e->nest > 0) return;

        if ($e->dispatched) {
            $this->display();
            $e->stop();
        }
    }

    protected function __callWhenMethodNotExists($name, $params) {
        $this->crit(['invalid call %s', $name]);
    }

}
