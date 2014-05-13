<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Event;

use Seaf\Base\Container;
/**
 * 
 */
trait ObservableTrait
    {
        private $observers = [];
        private $actions = [];

        /**
         * @param ObserverIF
         */
        public function addObserver(ObserverIF $observer)
        {
            $this->observers[] = $observer;
        }

        /**
         * @param ObserverIF
         */
        public function addObserverCallback($callback)
        {
            $this->observers[] = new ObserverCallback($callback);
        }

        /**
         * @return array
         */
        public function getObservers()
        {
            return $this->observers;
        }

        /**
         * @param string
         * @param array
         */
        public function fireEvent($type, $args = [])
        {
            $type = strtolower($type);

            if ($args instanceof EventIF) {
                $event = $args;
            }else{
                $event = new Event($type, $args, $this);
            }


            if (isset($this->actions[$type])) {
                foreach ($this->actions[$type] as $action) {
                    if (!$event->isStop()) {
                        call_user_func($action, $event);
                    }
                }
            }

            foreach($this->observers as $o) {
                $o->notify($event);
            }
        }

        /**
         * @param Event
         */
        public function notify(EventIF $e)
        {
            // 自分のイベントとして実行しなおす
            $this->fireEvent($e('type'), $e);
        }

        /**
         *
         */
        public function on ($name, $action)
        {
            $name = strtolower($name);
            if(!isset($this->actions[$name])) {
                $this->actions[$name] = array();
            }
            array_unshift($this->actions[$name],$action);
        }

        public function once ($name, $action)
        {
            $this->on($name, function($e) use ($name, $action) {
                $result = $action($e);
                $this->off($name, $action);
            });
        }


        /**
         *
         */
        public function off($name, $action)
        {
            $name = strtolower($name);

            foreach($this->actions[$name] as $k=>$registered_action) {
                if ($registered_action ==  $action) {
                    unset($this->actions[$name][$k]);
                }
            }
        }
    }
