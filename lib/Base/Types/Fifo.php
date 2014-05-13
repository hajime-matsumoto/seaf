<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * 型ライブラリ
 */
namespace Seaf\Base\Types;

/**
 * FIFO型
 */
class FIFO
{
    private $fifopath;

    public function __construct($name, $mode = 0666)
    {
        $this->fifopath = $name;
    }

    public function open($mode)
    {
        if (!file_exists($this->fifopath)) {
            posix_mkfifo($this->fifopath, 0666);
        }
        return fopen($this->fifopath, $mode);
    }

    public function write($data)
    {
        $fp = $this->open('w');
        fwrite($fp, $data);
        fclose($fp);
    }
}
