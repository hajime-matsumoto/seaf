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
    private $cnt = 0;

    public function __construct ($config) 
    {
        parent::__construct($config);


        $this->file = isset($config['file']) ? $config['file']: '/tmp/seaf.log';
        if (!file_exists($this->file)) {
            touch($this->file);
            chmod($this->file,0666);
        }
        $this->fp = fopen($this->file, isset($config['fileType'])? $config['fileType']: 'w');

    }

    public function _post ($context, $level = Log\Level::INFO) 
    {
        $msg = $this->makeMessage($context, $level);

        if ($this->cnt == 0) {
            fwrite($this->fp,"====[START]===================\n");
        }

        fwrite($this->fp,$msg."\n");
        $this->cnt++;
    }

    public function __destruct ( )
    {
        if ($this->cnt > 0) {
            fwrite($this->fp,"====[END]===================\n");
        }
        fclose($this->fp);
    }
}