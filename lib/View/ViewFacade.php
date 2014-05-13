<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\View;

use Seaf\Util\Util;
use Seaf\Base\Module;
use Seaf\Base\ConfigureTrait;
use Seaf\Base\Component;


/**
 * モジュールファサード
 */
class ViewFacade extends Module\ModuleFacade
{
    use ConfigureTrait;
    use Component\ComponentContainerTrait;

    protected static $object_name = 'View';

    private $paths = [];

    /**
     * コンストラクタ
     */
    public function __construct (Module\ModuleIF $module = null, $configs = [])
    {
        if ($module instanceof Module\ModuleIF) {
            $this->setParentModule($module);
        }

        if($m = $this->mediator()) {
            $configs = $m->util->phpFunction->array_merge(
                $configs, $m->config->getConfig('view', [])
            );
        }

        // 設定
        $this->configure($configs,[
            'paths' => [],
            'engine' => 'twig'
        ]);

        $this->registerComponent('twig', __NAMESPACE__.'\TwigEngine');
    }

    private function _configPaths($paths)
    {
        foreach ($paths as $path) {
            $this->addPath($dir);
        }
    }
    private function _configEngine($name)
    {
        $this->setEngine($name);
    }

    /**
     * Viewパスをセット
     *
     * @param string
     * @return self
     */
    public function addPath($path)
    {
        $this->debug("Path Added $path");
        $this->paths[] = $path;
        return $this;
    }

    /**
     * Viewエンジンをセット
     *
     * @param string
     * @return self
     */
    public function setEngine($name)
    {
        $this->debug(['Set Engine %s', $name]);
        $this->engine = $name;
    }

    private function getEngine($name = null)
    {
        if ($name == null) $name = $this->engine;
        return $this->loadComponent($this->engine);
    }

    /**
     * 描画
     *
     * @param string テンプレート名
     * @param array 埋め込む変数
     * @return string
     */
    public function render($tpl, $vars, $paths = [])
    {
        $php = $this->module('util')->phpFunction;

        $this->debug(['Templete %s', $tpl]);
        $e = $this->getEngine( );
        return $e->render($tpl, $vars, $php->array_merge($paths, $this->paths));
    }
}
