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

use Seaf;

/**
 * Eventコンポーネント
 * ===================
 * イベントを管理するコンポーネントです。
 */
class Event
{
    private $events = array();

    public function on ($event_name, $action, $object = null) 
    {
        if (is_object($object)) {
            $action = array($object,$action);
        }

        $this->events[$event_name][] = $action;
    }

    public function off ($event_name, $action)
    {
        foreach ($this->events[$event_name] as $k => $event_action) {
            if ($event_action === $action) {
                unset($this->events[$event_name][$k]);
            }
        }
    }

    public function trigger ($event_name) 
    {
        if (func_num_args() > 0) {
            $args = func_get_args();
        }
        if (isset($this->events[$event_name]) && is_array($this->events[$event_name])) {
            foreach ($this->events[$event_name] as $action) {
                $isContinue = call_user_func_array($action, $args);
                if ($isContinue === false) break;
            }
        }
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
