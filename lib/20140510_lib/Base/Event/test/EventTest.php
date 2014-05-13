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

    public function testEventObservablePrivent ( )
    {
        // イベントの監視
        $client1 = new Observable( );
        $client2 = new Observable( );

        $client1->addObserver($client2);

        $text = '';
        $client1->on('event',function($e) use (&$text) {
            $text.= 'event1';
        });
        $client2->on('event',function($e) use (&$text) {
            $text.= 'event2';
        });

        $client1->fireEvent('event');
        $client2->fireEvent('event');

        $this->assertEquals(
            'event1event2event2',$text
        );
    }

    public function testEventObsererCallback ( )
    {
        // イベントの監視
        $client1 = new Observable( );
        $client1->name = "client1";
        $client2 = new Observable( );
        $client3 = new Observable( );

        $client2->addObserver($client3);
        $client1->addObserver($client2);

        $client3->addObserverCallback(function($e) {
            $this->assertEquals(
                'client1',
                $e->get('target')->name
            );
        });


        $client1->fireEvent('event');

    }

}
