<?php
namespace Seaf\View\Templator;

use Seaf\Exception\Exception;

/**
 * PHPテンプレータ
 */
class PhpTemplator extends Templator
{
    private $dirs;
    /**
     * __construct
     *
     * @param 
     */
    public function __construct ($config = array())
    {
        $this->dirs = isset($config['dirs']) ? $config['dirs']: array();
    }

    /**
     * @param string
     * @param array
     * @return string
     */
    public function render($file, $vars = array())
    {
        if (false == strpos($file, '.')) $file .= ".php";

        $found = false;

        foreach ($this->dirs as $dir) {
            $tpl = $dir->get($file);

            if ($tpl->exists()) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new Exception(array("%sがみつかりません",$tpl));
        }

        $vars['title'] = 'Seaf';
        return $tpl->includeWithVars($vars);
    }
}
