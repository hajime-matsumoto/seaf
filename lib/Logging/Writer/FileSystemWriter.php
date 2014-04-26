<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging\Writer;

use Seaf\Logging;
use Seaf\Container;
use Seaf\Wrapper;

/**
 * ログライター
 */
class FileSystemWriter extends Logging\Writer
{
    private $fileName;
    private $writeMode;

    public function __construct ($cfg)
    {
        parent::__construct($cfg);
        $this->fileName = $cfg('fileName');
        $this->writeMode = $cfg('writeMode', 'a');
    }

    /**
     * ログハンドラ終了時の処理
     */
    public function shutdown ( ) {
        $formatter = $this->formatter( );

        if (empty($this->buf)) return;

        $fp = fopen($this->fileName, $this->writeMode);
        flock($fp, LOCK_EX);
        foreach ($this->buf as $log) {
            fwrite($fp, $log = $formatter->format($log)."\n");
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
