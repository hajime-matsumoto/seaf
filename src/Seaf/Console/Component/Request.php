<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Console\Component;

use Seaf\FrameWork;
use Seaf\Helper\ArrayHelper as ArrayH;

/**
 * Request
 */
class Request extends FrameWork\Component\Request
{
    public function init ($request = null)
    {
        $GLOBALS['argv'];

        $this->setUri(ArrayH::get($GLOBALS['argv'],1,'usage'));
    }
}
