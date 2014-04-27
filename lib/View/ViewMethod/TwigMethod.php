<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View\ViewMethod;

use Seaf\View;
use Seaf\Registry\Registry;

use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;


if (!class_exists('Twig_Autoloader')) {
    require_once 'Twig/Autoloader.php';
    Twig_Autoloader::register();
}

class TwigMethod extends View\ViewMethod
{
    private $twig;

    public function setupViewMethod(View\View $View)
    {
        if (!class_exists('Twig_Environment')) {
            throw new View\Exception\TwigClassNotFound( );
        }

        $loader = new Twig_Loader_Filesystem($View->getViewFileDirs(), [
            'debug' => Registry::isDebug()
        ]);

        $twig = new Twig_Environment($loader, [
            'cache' => Registry::getSingleton( )->getVar('cache_dir', null)
        ]);

        $this->twig = $twig;
    }

    public function _render ($template, View\ViewModel $vm)
    {
        try {
            return $this->twig->render($template, $vm->getExtractVars());
        } catch (\Exception $e) {
            throw new View\Exception\TwigNativeException($e);
        }
    }

}
