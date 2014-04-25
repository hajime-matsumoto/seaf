<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging;

class LogHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * インスタンス生成可能か
     */
    public function testInitialize ( )
    {
        $l = new LogHandler( );

        $this->assertInstanceOf(
            'Seaf\Logging\LogHandler',
            $l
        );
    }

    /**
     * ログ送信+イベント処理
     */
    public function testPostDebugLog ( )
    {
        $l = new LogHandler( );
        $message = '';

        // ロガーの設定
        $l->on('log.post',function($event) use (&$message) {
            $log = $event->getVar('log');
            if ($log->hasTag('seaf')) {
                $message = $log->getMessage();
            }
        });

        $l->debug('デバッグだよ', null, ['seaf']);

        $this->assertEquals(
            'デバッグだよ',
            $message
        );
    }

    /**
     * PHPのログハンドラを横取りする
     */
    public function testPHPLog ( )
    {
        $l = new LogHandler( );
        $message = '';

        // ロガーの設定
        $l->on('log.post',function($event) use (&$level, &$message) {
            $log     = $event->getVar('log');
            $message = $log->getMessage();
            $level   = $log->getLevelAsString();
            $time    = $log->getTimeWithFormat('Y-m-d G:i:s');
            var_Dump($level, $message, $time);
        });

        $l->register();

        trigger_error('test');

        $this->assertEquals('WARNING', $level);
        $this->assertEquals('test', $message);

        throw new \Exception();
    }

}
