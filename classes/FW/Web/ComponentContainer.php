<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Web;

use Seaf;
use Seaf\FW;
use Seaf\Base;
use Seaf\Container\ArrayContainer;

/**
 * FW用のComponentContainer
 */
class ComponentContainer extends FW\ComponentContainer
{
    /**
     * Web用のレスポンス
     */
    public function initResponse ( )
    {
        return new Response( );
    }

    /**
     * Web用のView
     */
    public function initView ( )
    {
        $view = new View($this->controller);
        $view->addPath(Seaf::Config()->get('component.View.paths'));
        return $view;
    }

    /**
     * Web用のSession
     */
    public function initSession ( )
    {
        return Seaf::Session( );
    }

    /**
     * Web用のRequest
     */
    public function initRequest ( )
    {
        return new Request();
    }
}
