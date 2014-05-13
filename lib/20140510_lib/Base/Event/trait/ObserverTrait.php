<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Event;

use Seaf\Base\Container;
/**
 * 
 */
trait ObserverTrait
    {
        private $events;
        private $notifyAction;
        private $defaultNotifyAction;

        public function initObserver ( )
        {
            $this->events = new Container\ArrayContainer();
            $this->notifyAction = new Container\CueContainer();
            $this->defaultNotifyAction = [$this, 'notifyHandler'];
        }

        public function setNotifyHandler ($action)
        {
            $this->notifyAction->prepend($action);
        }

        public function restoreNotifyHandler ($action)
        {
            $this->notifyAction->shift($action);
        }

        public function notifyAction ( )
        {
            if ($this->notifyAction->isEmpty()) {
                return $this->defaultNotifyAction;
            }
            return $this->notifyAction->first();
        }

        public function notify (EventIF $e)
        {
            return call_user_func($this->notifyAction(),$e, $this);
        }

        public function notifyHandler (Event $e)
        {
            foreach ($this->events->getArray($e->type) as $callback) 
            {
                $callback($e);
            }

            if ($this instanceof ObservableIF) {
                foreach ($this->getObservers() as $observer)
                {
                    $observer->notify($e);
                }
            }

        }

        public function bind ($type, $callback)
        {
            $this->events->add($type, $callback);
        }

    }
