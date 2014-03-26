<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View\Engine;

use Seaf;
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
        $file = Seaf::fileSystem($file);
        if (false === $file->ext()) {
            $file->ext('php');
        }

        $found = false;

        foreach ($this->paths as $dir) {
            $dir = Seaf::fileSystem($dir);

            $tpl = $dir->get($file);

            if ($tpl->isExists()) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new Exception\Exception(array("%sがみつかりません",$tpl));
        }

        $vars['title'] = 'Seaf';
        return $tpl->includeWithVars($vars);
    }
}
