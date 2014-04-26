<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging;

use Seaf\Container;
use Seaf\Wrapper;
use Seaf\Event;

/**
 * ログライター
 */
class Writer
{
    private $formatter;
    protected $buf;
    private $filters = [];

    public function __construct ($cfg)
    {
        // フォーマッタを設定
        $this->formatter = Formatter::factory($cfg('formatter', ['type'=>'text']));

        // フィルタを設定
        foreach ($cfg('filters', []) as $v) {
            $this->filters[] = Filter::factory($v);
        }
    }

    /**
     * Writerを作成
     */
    public static function factory ($cfg)
    {
        $cfg = new Container\ArrayContainer($cfg);

        $type = $cfg('type', 'fileSystem');

        return Wrapper\ReflectionClass::create(
            __NAMESPACE__.'\\Writer\\'.ucfirst($type).'Writer'
        )->newInstanceArgs([$cfg]);
    }

    /**
     * Filterを作成
     */
    public function addLevelFilter ($level)
    {
        $this->addFilter(Filter::factory([
            'type'=>'level',
            'value' => $level
        ]));
        return $this;
    }

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * アタッチする
     */
    public function attach (LogHandler $LogHandler)
    {
        $LogHandler->on('log.post', [$this, "logPost"]);
        $LogHandler->on('shutdown', [$this, "shutdown"]);
        return $this;
    }

    /**
     * ログをバッファする
     */
    public function logPost (Event\Event $e)
    {
        $log = $e->getVar('log');

        // フィルターする
        if($this->filter($log)) {
            $this->buf[] = $log;
        }
    }

    /**
     * フィルター
     */
    public function filter($log)
    {
        foreach($this->filters as $filter) {
            if(false == $filter->filter($log)) {
                return false;
            }
        }
        return true;
    }

    /**
     * ログフォーマッタを取得
     */
    protected function formatter( )
    {
        return $this->formatter;
    }

    /**
     * ログハンドラ終了時の処理
     */
    public function shutdown ( ) {
    }

}
