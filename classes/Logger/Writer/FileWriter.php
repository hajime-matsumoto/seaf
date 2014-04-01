<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logger\Writer;

use Seaf\Exception;
use Seaf\Helper;
use Seaf\Pattern;

class FileWriter extends Base
{
    private $fp;
    private $buf = [];

    /**
     * コンストラクタ
     *
     * @param array $config
     * @return Base
     */
    protected function __construct ($config)
    {
        $this->configure($config, false, false, array('type'));
    }

    public function configFileName($file)
    {
        $this->file = $file;
    }

    public function configMode($mode)
    {
        $this->mode = $mode;
    }

    public function _post($message)
    {
        $this->buf[] = $message;
    }

    public function shutdown()
    {
        $this->fp = fopen($this->file, $this->mode);
        flock($this->fp, LOCK_EX);
        fwrite($this->fp, implode("\n", $this->buf));
        flock($this->fp, LOCK_UN);
        fclose($this->fp);
    }
}
