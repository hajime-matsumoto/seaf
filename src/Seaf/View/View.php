<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\View;

use Seaf\Seaf;
use Seaf\Core\Base;
use Seaf\Http\Http as SeafHTTP;

/**
 * Viewコンポーネント
 */
class View extends Base
{
    private $params = array();

    public function __construct( )
    {
        parent::__construct();

        $this->di('registry')->set('name', 'View');

        $self = $this;
        $this->di()->factory()->register('twig',__NAMESPACE__.'\\Twig', function($twig) use ($self) {
            $twig->setView($self);
        });

        // ヘルパをマップする
        $this->di('helperHandler')->bind( $this, array(
            'render'=>'_render',
        ));
    }

    public function getMergedParams( $params )
    {
        return array_merge($this->params, $params);
    }

    public function _render( $file, $params = array() )
    {
        $sufix = substr( $file, strrpos($file,'.')+1);
        return $this->di($sufix)->render($file, $params);
    }

    public function setParam( $name, $value )
    {
        $this->params[$name] = $value;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
