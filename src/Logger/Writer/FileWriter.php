<?php
namespace Seaf\Logger\Writer;

/**
 * ログをファイルに書きむクラス
 */
class FileWriter extends EchoWriter
{
    public $fileName, $mode;

    private $fp;

    public function initWriter()
    {
        $this->fp = fopen($this->fileName, $this->mode);
    }

    public function _post($context, $level)
    {
        $message = $this->_makeMessage($context, $level);
        flock($this->fp, LOCK_EX);
        fwrite($this->fp, $message."\n");
        flock($this->fp, LOCK_UN);
    }

    public function shutdownWriter( )
    {
        fclose($this->fp);
    }
}
