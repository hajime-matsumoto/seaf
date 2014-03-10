<?php
namespace Seaf\Templator;

/**
 * テンプレータ
 */
class Templator
{
    /**
     * factory
     * ===================================
     *
     * ::factory(array(
     *  'type' => 'php',
     *  'dirs' => array('/template')
     *  ));
     *
     * @param array $config
     * @return Templator
     */
    public static function factory ($config)
    {
        $type = isset($config['type']) ? $config['type']: 'php';
        $class = 'Seaf\\Templator\\'.ucfirst($type).'Templator';
        return new $class($config);
    }

    /**
     * __construct
     *
     * @param 
     */
    public function __construct ()
    {
    }

    public function render($file, $vars = array())
    {
    }
}
