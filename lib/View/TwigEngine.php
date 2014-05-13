<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\View;

use Seaf\Util\Util;
use Seaf\Base\Module;
use Seaf\Base\ConfigureTrait;
use Seaf\Base\Component;

use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;


if (!class_exists('Twig_Autoloader')) {
    require_once 'Twig/Autoloader.php';
    Twig_Autoloader::register();
}


/**
 * モジュールファサード
 */
class TwigEngine implements Component\ComponentIF
{
    use ConfigureTrait;
    use Component\ComponentTrait;

    protected static $object_name = 'Twig';

    /**
     * コンストラクタ
     */
    public function __construct (Module\ModuleIF $module = null, $configs = [])
    {
        if ($module instanceof Module\ModuleIF) {
            $this->setParentComponent($module);
        }
    }

    public function render($tpl, $params, $paths = [])
    {
        $tpl = $tpl.".twig";

        $php = $this->module('util')->phpFunction;
        $reg = $this->module('registry');

        $cache = $reg->get('cache_dir', '/tmp/twig.'.$reg->getSapiName());
        $flg = $reg->isDebug();

        $this->debug(['Going to render %s', $tpl]);
        $this->debug(['Twig Cache Dir ( %s )', $cache]);
        $this->debug(['Twig Debug Flag ( %s )', $flg ? 'On':'Off']);
        $this->debug(['Twig Template Paths ( %s )', $php->implode(',', $paths)]);

        $loader = new Twig_Loader_Filesystem($paths);
        $twig = new Twig_Environment($loader, [
            'cache' => $this->module('registry')->get('cache_dir', '/tmp/twig'),
            'debug' => $this->module('registry')->isDebug()
        ]);
        $twig->addFilter('var_dump', new \Twig_Filter_Function('var_dump'));
        $this->info(["Rendering %s in %s", $tpl, $php->implode(",", $paths)]);
        $rendered = $twig->render($tpl, $params);
        return $rendered;
    }

    public function display ($template, $params, $paths)
    {
        echo $this->render($template, $params, $paths);
    }
}
