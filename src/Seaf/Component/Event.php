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

use Seaf\Seaf;

/**
 * Eventコンポーネント
 */
class Event
{
    /**
     * @param object $di
     */
    public function acceptDIContainer( DIContainer $di )
    {
    }

    public function __construct( )
    {
        $this->init();
    }

    public function init()
    {
        $this->eventContainer = new ArrayCollection();
    }

    /**
     * フックを作成
     * @param string $name
     */
    public function addHook( $key, $func )
    {
        $this->eventContainer->push($key, $func);
    }

    /**
     * フックをトリガー
     *
     * @param string $name
     * @param mixed $v,...
     */
    public function trigger( $key )
    {
        if( !$this->eventContainer->has($key) ) return false;

        foreach( $this->eventContainer->get($key) as $hook )
        {
            $result = call_user_func_array( $hook, array_slice(func_get_args(),1));
            if( $result === false ) break;
        }
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
