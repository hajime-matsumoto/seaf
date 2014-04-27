<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View\ViewMethod;

use Seaf\View;

class PHPMethod extends View\ViewMethod
{
    public function setupViewMethod ( )
    {
    }

    public function _render ($template, View\ViewModel $vm)
    {
        ob_start( );
        $this->display($template, $vm);
        return trim(ob_get_clean());
    }

    public function display ($template, View\ViewModel $vm)
    {
        if (!$vm->callMethodArray('searchViewFilePath', [$template, &$path])) {
            throw new View\Exception\ViewFileNotFound($template, $vm->getViewFileDirs());
        }

        extract($vm->getExtractVars());

        include $path;
    }
}
