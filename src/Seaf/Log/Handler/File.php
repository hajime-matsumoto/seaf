<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Log\Handler;

use Seaf\Log;
use Seaf\Log\Level;

/**
 * ファイル保存
 */
class File extends Log\Handler 
{

    private $fp;

    public function __construct ($config) 
    {
        parent::__construct($config);

        $this->file = isset($config['file']) ? $config['file']: '/tmp/seaf.log';
        $this->fp = fopen($this->file, isset($config['fileType'])? $config['fileType']: 'w');
    }

    public function _post ($context, $level = Log\Level::INFO) 
    {
        $msg = $this->makeMessage($context, $level);

        fwrite($this->fp,$msg."\n");
    }

    public function __destruct ( )
    {
        fclose($this->fp);
    }
}
