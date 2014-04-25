<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Event;

trait ObservableTrait
    {
        protected $events = [];

        /**
         * イベントをセットする
         *
         * @param string
         * @param callable
         * @param bool
         */
        public function on ($name, $callback, $prepend = false)
        {
            if (!isset($this->events[$name])) $this->events[$name] = [];

            if ($prepend == false) {
                array_push($this->events[$name], $callback);
            }else{
                array_unshift($this->events[$name], $callback);
            }
        }

        /**
         * イベントを解除する
         *
         * @param string
         * @param callable
         */
        public function off ($name, $callback = null)
        {
            if ($callback == null) {
                $this->events[$name] = [];
                return true;
            }

            if (!isset($this->events[$name])) return false;
            
            foreach ($this->events[$name] as $k=>$v) {
                if ($v == $callback){
                    unset($this->events[$name][$k]);
                    return true;
                }
            }
            return false;
        }

        /**
         * イベントを発動する
         *
         * @param string
         * @param callable
         * @param bool
         */
        public function trigger ($name, $params = [])
        {
            $event = new Event($name, $params);


            if (!isset($this->events[$name])) return false;

            foreach ($this->events[$name] as $v) {
                $continue = call_user_func($v, $event, $this);
                if ($continue === false) {
                    break;
                }
            }
        }
    }
