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

        public function addObserver(ObserverIF $observer)
        {
            $this->observers[] = $observer;
        }

        public function getObservers()
        {
            return $this->observers;
        }

        public function fireEvent($type, $args = [])
        {
            if ($args instanceof Event) {
                $event = $args;
            }else{
                $event = new Event($type, $args, $this);
            }

            foreach($this->observers as $o) {
                $o->notify($event);
            }
        }
    }
