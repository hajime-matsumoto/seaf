<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View\Engine;

use Seaf;
use Seaf\Util\FileSystem;
use Seaf\Exception;

/**
 * PHPテンプレータ
 */
class Php extends Base
{
    /**
     * @param string $file
     * @param array $vars
     * @return string
     */
    public function render($file, $vars = array())
    {
        if (false === FileSystem\Helper::getExt($file)) {
            $file.=".php";
        }
        $File = $this->view->loader->file($file);
        if(!$File) {
            throw new Exception\Exception(array("%sがみつかりません",$tpl));
        }
        return $File->includeWithVars($vars);
    }
}
