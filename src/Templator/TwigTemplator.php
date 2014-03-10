<?php
namespace Seaf\Templator;

use Seaf\Core\Exception;
use Seaf\Core\Kernel;

// Twigクラスを読み込む
if (!class_exists('\Twig_Loader_Filesystem')) {
    require_once 'Twig/Autoloader.php';
    \Twig_Autoloader::register();
}

/**
 * Twigテンプレータ
 * ========================
 * Kernel::fs() の /templateと/cacheをデフォルトで利用します
 */
class TwigTemplator extends Templator
{
    private $twig;

    /**
     * __construct
     *
     * @param array [dirs, cache
     */
    public function __construct ($config = array())
    {
        $dirs = isset($config['dirs']) ? $config['dirs']: array('/template');

        foreach ($dirs as $k=>$dir) {
            $dirs[$k] = Kernel::fs()->transRealPath($dir);
        }

        $loader = new \Twig_Loader_Filesystem($dirs);
        $this->twig   = new \Twig_Environment(
            $loader, 
            array(
                'cache' => Kernel::fs()->transRealPath('/cache')
            )
        );

        if (
            Kernel::registry()->get('env', 'development') != 'production'
        ) {
            $this->twig->clearcachefiles();
        }
    }

    /**
     * @param string
     * @param array
     * @return string
     */
    public function render($file, $vars = array())
    {
        return $this->twig->render($file, $vars);
    }
}
