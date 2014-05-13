<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

use Seaf\Base\Command;
use Seaf\Base\Module;

class TestFacade extends Module\Facade
{
    public function kakeru ($num1, $num2)
    {
        return $num1 * $num2;
    }
}


class MediatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     */
    public function testMediator ( )
    {
        $m = new Mediator();
        $this->assertInstanceOf('Seaf\BackEnd\Mediator',$m);
    }

    /**
     * 
     */
    public function testMediatorModuleRequestLoadFailed ( )
    {
        $m = new Mediator();
        $result = $m->cache->createCache($key='1',$data='a',$expires=10);
        $this->assertTrue($m->isError($result));
        if ($m->isError($result)) {
            $this->assertEquals(
                'LOAD_FAILED',
                $result->error[0]['code']
            );
        }
    }

    /**
     * 
     */
    public function testMediatorModuleRequest ( )
    {
        $m = new Mediator();
        $m->register('test', 'Seaf\BackEnd\TestFacade');

        $m->on('log', function($e) {
            // $e->params->dump();
        });

        $m->fireEvent('log.debug',['a'=>1]);

        $result = $m->test->kakeru(2,16);

        if ($m->isError($result)) {
            $result->error->dump();
        }
        $this->assertFalse($m->isError($result));

        $this->assertEquals(32, $result);
    }
}
