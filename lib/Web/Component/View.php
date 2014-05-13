<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web\Component;

use Seaf\Base\Event;
use Seaf\Web;
use Seaf\View\ViewModel;

/**
 * View
 */
class View implements Web\ComponentIF
{
    use Web\ComponentTrait;

    protected static $object_name = 'View';

    private $paths;
    private $viewModel;

    /**
     * コンストラクタ
     */
    public function __construct ($module = null)
    {
        if ($module) $this->setParentWebComponent($module);
    }

    public function viewModel() {
        if (!$this->viewModel) {
            $this->viewModel = new ViewModel( );
        }
        return $this->viewModel;
    }

    public function addPath($path)
    {
        $this->paths[] = $path;
    }

    public function enable( )
    {
        $this->info('Enabled');

        $this->getParent()->mapMethod([
            'render' => [$this, 'render']
        ]);
        return $this;
    }

    public function disable ( )
    {
        $this->debug('Disabled');

        $this->getParent()->mapMethod([
            'render' => function ($name) {
                return $this->noop('render');
            }
        ]);
        return $this;
    }

    public function noop($name)
    {
        $this->debug(['NoOp %s', $name]);
    }


    public function render($name)
    {
        $res = $this->getParent()->loadComponent('response');
        $arr = $res->toArray();

        $params = $arr['params'];
        $params['yield'] = $arr['body'];
        $params['vm'] = $this->viewModel();

        $res
            ->clear()
            ->write($this->module('view')->render($name, $params, $this->paths));
    }
}
