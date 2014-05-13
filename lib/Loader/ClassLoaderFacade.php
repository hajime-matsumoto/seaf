<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * クラスローダ
 */
namespace Seaf\Loader;

use Seaf\Base\Proxy;
use Seaf\Base\Module;

/**
 * モジュールファサード
 */
class ClassLoaderFacade implements Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;

    protected static $object_name = 'ClassLoader';

    private $loader;

    public function __construct(Module\ModuleIF $module, ClassLoader $loader)
    {
        $this->setParentModule($module);
        $this->loader = $loader;
    }

    protected function addNamespace($ns, $path = null)
    {
        if (is_array($ns)) {
            foreach($ns as $k=>$v) {
                $this->addNamespace($k, $v);
            }
            return true;
        }

        $this->debug("Namaspace Added >>> $ns to $path <<<");
        return $this->loader->addNamespace($ns, $path);
    }

    /**
     * 現状を表示する
     */
    protected function explain( )
    {
        $text = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'."\n";
        $text.= sprintf("[ EXPLAIN ]\n");
        $text.= '|| '."\n";
        foreach($this->loader->namespaces as $ns => $path) {
            $text.= sprintf("|| [ Name Space ] >>> $ns > %s\n", implode(',', $path));
        }
        $text.= '|| '."\n";
        $text.= 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'."\n";

        echo $text;
    }
}
