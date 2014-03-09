<?php
namespace Seaf\Commander;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-08 at 15:31:27.
 */
class CommanderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Commander
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->cmder = new Commander;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Seaf\Commander\Commander::map
     * @todo   Implement testMap().
     */
    public function testMap()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testAutoEvent( )
    {
        $this->cmder->on('before.run',function(){
            echo 'before.run';
        });
        $this->cmder->on('after.run',$func = function(){
            echo 'after.run';
        });
        $this->cmder->off('after.run',$func);

        $this->cmder->route('init', function() {
            $this->cmder->out('イニシャライズしました');
        });

        $this->cmder->out('Hello');

        //$this->cmder->run('init');
    }
}
