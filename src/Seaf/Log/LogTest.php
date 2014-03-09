<?php
namespace Seaf\Log;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-08 at 11:07:30.
 */
class LogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Log
     */
    protected $object;

    protected $handler;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->handler = Handler::factory(array(
            'type'=>'console'
        ))->register();
        Log::register();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->handler->unregister();
    }

    /**
     * @covers Seaf\Log\Log::post
     */
    public function testPost()
    {
        $c = Level::ALL;

        Log::debug($level = Level::ALL ^ Level::INFO ^ Level::ALERT );
        Log::debug($level & Level::ALERT);

        $this->assertEquals(0, $level & Level::ALERT);
        $this->assertEquals(0, $level & Level::INFO);
        $this->assertEquals(level::DEBUG, $level & Level::DEBUG);

        $level = Level::ALERT;
        $this->assertEquals(Level::ALERT, $level & Level::ALERT);
        $this->assertEquals(0, $level & Level::INFO);

        $level = Level::ALERT | Level::WARNING;
        $this->assertEquals(Level::ALERT, $level & Level::ALERT);
        $this->assertEquals(Level::WARNING, $level & Level::WARNING);
        $this->assertEquals(0, $level & Level::INFO);

        Log::emerg('エマージェンシー');
        Log::alert('アラート');
        Log::critical('クリティカル');
        Log::error('エラー');
        Log::warn('ワーニング');
        Log::info('インフォ');
        Log::debug('デバッグメッセージ');
    }

    /**
     * @covers Seaf\Log\Log::post
     */
    public function testLevelMask()
    {
        $this->handler->unregister();

        $this->handler = Handler::factory(array(
            'type'=>'callback',
            'level'=>Level::EMERGENCY|Level::ALERT,
            'callback'=>function ($context) {
                var_dump($context);
            }
        ))->register();

        Log::emerg('エマージェンシー');
        Log::alert('アラート');
        Log::critical('クリティカル');
        Log::error('エラー');
        Log::warn('ワーニング');
        Log::info('インフォ');
        Log::debug('デバッグメッセージ');

    }

    /**
     * @covers Seaf\Log\Log::post
     */
    public function testPHPError()
    {
        Log::register();

        trigger_error('test', E_USER_ERROR);
    }

    public function testTrace()
    {
        $this->handler->unregister();

        $this->handler = Handler::factory(array(
            'type'=>'debugger'
        ))->register();

        Log::debug('aaaa');
    }
}
