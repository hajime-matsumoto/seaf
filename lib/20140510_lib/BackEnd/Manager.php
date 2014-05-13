<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\BackEnd;

use Seaf\Base\Singleton;
use Seaf\Base\Command;
use Seaf\Base\Event;
use Seaf\Util\Util;

/**
 * バックエンド:管理者
 */
class Manager extends Mediator implements Event\ObservableIF
{
    use Singleton\SingletonTrait;

    private $logs;


    static private $inited = false;

    /**
     * 遅延束縛
     */
    public static function who ( )
    {
        if (self::$inited == false) {
            die ('Please Initialize First');
        }
        return __CLASS__;
    }

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        self::$inited = true;

        // Mediatorのコンストラクタを呼び出す
        parent::__construct();
    }

    /**
     * イニシャライザ
     */
    public function init ( )
    {
        // コンフィグを取得
        $setup = Util::ArrayContainer($this->config->getConfig('setup'));

        // PHPを設定する
        mb_internal_encoding($setup('encoding', 'utf-8'));
        mb_language($setup('lang', 'ja'));
        date_default_timezone_set($setup('timezone', 'Asia/Tokyo'));

        // DEBUG中の初期化処理
        if ($this->registry->isDebug()) {
            $this->initOnDebug();
        }

    }

    /**
     * Debug用の初期処理
     */
    private function initOnDebug( ) 
    {
        // イベントのログ
        $eventLogs = array();

        // ログ
        $logs = array();

        // 全イベントを補足する
        $this->addObserverCallback(function($e) use(&$eventLogs){
            if ($e('type') !== 'log') {
                $eventLogs[] = sprintf(
                    "(Event) %s From %s",
                    $e('type'),
                    get_class($e('target'))
                );
            }
        });
        // 普通のログの補足
        $this->on('log', function ($e) use(&$logs){
            $logs[] = $e->getParam('log')->getMessage();
        });

        // 終了時にログを吐きだす
        $this->on('shutdown', function ( ) use (&$eventLogs, &$logs){
            if (!empty($eventLogs)) {
                echo "\n******* EVENT LOG *******\n";
                echo implode("\n", $eventLogs);
                echo "\n******* /EVENT LOG *******\n";
            }

            if (!empty($logs)) {
                echo "\n******* START *******\n";
                echo implode("\n",$logs);
                echo "\n******* STOP *******\n";
            }
        });
    }


}
