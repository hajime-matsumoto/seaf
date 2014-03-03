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

namespace Seaf\Component;

use Seaf\DI\DIContainer;
use Seaf\Collection\ArrayCollection;

/**
 * Registryコンポーネント
 */
class Registry extends ArrayCollection
{
    /**
     * @param object $di
     */
    public function acceptDIContainer( DIContainer $di )
    {
        $this->di = $di;

        if( $this->di->has('helperHandler') )
        {
            $helperHandler = $this->di->get('helperHandler');

            $helperHandler->bind( $this,
                array( 
                    'setRegistry' => 'set',
                    'getRegistry' => 'get'
                )
            );
        }
    }

    /**
     * 取得時の処理
     */
    public function get( $name, $default = null )
    {
        $data = parent::get( $name, $default );

        if( !is_string($data) ) return $data;

        $self = $this;
        return preg_replace_callback('/\{\{(.+)\}\}/',  function($m) use ($self){
            return $self->get( $m[1], $m[0] );
        }, $data);
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
