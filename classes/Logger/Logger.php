<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logger;

use Seaf\Container\ArrayContainer;

class Logger
{
    private $writers = [];

    public function __construct ( )
    {
    }

    public function attachWriter(Writer $writer)
    {
        $this->writers[] = $writer;
    }

    public function dettachWriter(Writer $target)
    {
        foreach ($this->writers as $k=>$writer) {
            if ($writer == $target) {
                $writer->shutdown();
                unset($this->writers[$k]);
            }
        }
    }

    public function post ($level, $data)
    {
        foreach ($this->writers as $writer) {
            $writer->post($level, $data);
        }
    }

}
