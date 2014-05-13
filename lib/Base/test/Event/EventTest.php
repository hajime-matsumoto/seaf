<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Event;


/**
 * イベントのテスト
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testSet ( )
    {
        $source = new Observable( );
        $source->name = 'test1';

        $res = false;
        $source->on('some', function ($e) use (&$res){
            $res = true;
        });

        $source->fireEvent('some');
        $this->assertTrue($res);
    }

    public function testStop ( )
    {
        $source = new Observable( );
        $source->name = 'test1';

        $res = 0;
        $source->on('some', $func = function ($e) use (&$res){
            if ($res == 3) {
                $e->stop();
                return ;
            }
            $res++;
        });
        $source->on('some', $func);
        $source->on('some', $func);
        $source->on('some', $func);
        $source->on('some', $func);
        $source->on('some', $func);
        $source->on('some', $func);
        $source->on('some', $func);

        $source->fireEvent('some');
        $this->assertTrue($res == 3);
    }

    public function testPropagation ( )
    {
        $source1 = new Observable( );
        $source1->id = 1;
        $source2 = new Observable( );
        $source2->id = 2;
        $source3 = new Observable( );
        $source3->id = 3;
        $source = new Observable( );
        $source->id = 4;

        $source2->addObserver($source1);
        $source3->addObserver($source2);
        $source->addObserver($source3);

        $res = 0;
        $source1->on('some', $func = function ($e) use (&$res){
            $callers = $e->getCallers();
            foreach ($callers as $caller) {
                $ids[] = $caller->id;
            }
            $this->assertEquals([1,2,3,4], $ids);
        });

        $source->fireEvent('some');
    }

}
