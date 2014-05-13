<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Event;

use Seaf\Base\Container;
use Seaf\Util\Util;
/**
 * 
 */
trait ObservableTrait
    {
        private $observers;
        private $actions;

        private function observers( )
        {
            if (!$this->observers) {
                $this->observers = Util::Dictionary();
            }
            return $this->observers;
        }

        private function actions( )
        {
            if (!$this->actions) {
                $this->actions = Util::Dictionary();
                $this->actions->caseSensitive(false);
            }
            return $this->actions;
        }

        /**
         * @param ObserverIF
         */
        public function addObserver(ObserverIF $observer)
        {
            $this->observers( )->prepend($observer);
        }

        /**
         * @param string
         * @param array
         */
        public function fireEvent($type, $event = [])
        {
            // イベントオブジェクトの作成
            if (!($event instanceof EventIF)) {
                $params = $event;
                $event = new Event($type, $params, $this);
            }

            $event->addCallers($this);

            foreach ($this->actions()->get($type,[]) as $action) {
                if(!$event->isStoped()) {
                    call_user_func($action, $event);
                }
            }

            foreach($this->observers( ) as $o) {
                $o->notify($event);
            }
        }

        /**
         * @param Event
         */
        public function notify(EventIF $e)
        {
            // 自分のイベントとして実行しなおす
            $this->fireEvent($e->getType(), $e);
        }

        /**
         *
         */
        public function on ($name, callable $action = null)
        {
            if (is_array($name)) {
                foreach ($name as $k=>$v) {
                    $this->on($k, $v);
                }
                return $this;
            }
            $this->actions()->prepend($name, $action);
            return $this;
        }

        public function once ($name, callable $action)
        {
            $this->on($name, function($e) use ($name, $action) {
                $result = $action($e);
                $this->off($name, $action);
            });
        }

        public function off($name, callable $action)
        {
            $name = strtolower($name);

            foreach($this->actions()->get($name,array()) as $k=>$registered_action) {
                if ($registered_action ==  $action) {
                    $this->actions()->dict($name)->clear($k);
                }
            }
        }
    }
