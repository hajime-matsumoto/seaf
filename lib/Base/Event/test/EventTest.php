<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Event;

use Seaf\Util\Util;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testEventObservable ( )
    {
        // イベントの監視
        $client = new Observable( );
        $client->addObserver(
            new ObserverCallback(function($e) use(&$event){
                $event = $e;
            })
        );
        $client->fireEvent('event', ['a'=>'b']);

        $this->assertInstanceOf(
            'Seaf\Base\Event\Event',
            $event
        );
    }


}
