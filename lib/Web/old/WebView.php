<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web;

use Seaf\BackEnd;
use Seaf\Util\Util;
use Seaf\Util\ContainerUserTrait;
use Seaf\Base\Event;
use Seaf\Logging;
use Seaf\View;


/**
 * WEBView
 */
class WebView implements WebComponentIF
{
    use WebComponentTrait;
    use ContainerUserTrait;

    private $component;

    public function initWebComponent(WebComponentIF $component = null)
    {
        $this->component = $component;
        $this->setWebComponentParent($component);
        $this->vm = new View\ViewModel();
    }

    public function vm() {
        return $this->vm;
    }

    public function enable ( )
    {
        $methods = $this->component->getMethodsContainer();
        $methods->set('template', [$this, 'template']);
        $methods->set('display', [$this, 'display']);
    }

    public function disable ( )
    {
        $methods = $this->component->getMethodsContainer();
        $methods->restore('display');
    }

    public function set($name, $value)
    {
        $this->container()->dict('params')->set($name, $value);
    }

    public function template ($tpl)
    {
        $this->container()->set('tpl', $tpl);
    }

    public function display ( )
    {
        $template_name = $this->container()->get('tpl');
        $template_name = $this->component->getViewPath($template_name);
        var_dump($template_name);

        $arr    = $this->component->getComponent('response')->toArray();
        $params = $arr['params'];
        $params = array_merge(
            $this->container()->dict('params')->__toArray(),
            $params
        );
        $params['yield'] = $arr['body'];
        $params['vm'] = $this->vm();


        $this->debug('WEB|VIEW', [
            'Enabled Template(%s)',
                $template_name
        ]);

        $this->closestMediator( )->view->display($template_name, $params);
    }
}
