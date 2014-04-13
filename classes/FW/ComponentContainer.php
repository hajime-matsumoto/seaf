<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW;

use Seaf;
use Seaf\DI;
use Seaf\Base;
use Seaf\Container\ArrayContainer;
use Seaf\Response;

/**
 * FW用のComponentContainer
 */
class ComponentContainer extends DI\Container
{
    protected $controller;

    public function __construct (Controller $ctrl )
    {
        $this->controller = $ctrl;

        parent::__construct();

        // initXXXファクトリを使う
        $this->addFactory(new DI\Factory\MethodBaseFactory($this));

        // ネームスペースベースファクトリを使う
        $this->addFactory(new DI\Factory\NamespaceBaseFactory(__NAMESPACE__.'\\Component'));
    }

    /**
     * ルーター
     */
    public function initRouter( )
    {
        return new Routing\Router( );
    }

    /**
     * リクエスト
     */
    public function initRequest( )
    {
        return new Request( );
    }

    /**
     * レスポンス
     */
    public function initResponse( )
    {
        return new Response\Response( );
    }
}
