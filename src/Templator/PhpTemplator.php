<?php
namespace Seaf\Templator;

use Seaf\Core\Exception;
use Seaf\Core\Kernel;

/**
 * PHPテンプレータ
 */
class PhpTemplator extends Templator
{
    /**
     * __construct
     *
     * @param 
     */
    public function __construct ($config = array())
    {
        $dirs = isset($config['dirs']) ? $config['dirs']: array();
    }

    /**
     * @param string
     * @param array
     * @return string
     */
    public function render($file, $vars = array())
    {
        if (!Kernel::fs()->fileExists($file)) {
            throw new Exception(array("%sがみつかりません",$file));
        }

        ob_start();
        Kernel::fs()->includeFile($file, $vars);
        return ob_get_clean( );
    }
}
