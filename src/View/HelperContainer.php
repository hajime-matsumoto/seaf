<?php
namespace Seaf\View;

use Seaf\Kernel\Kernel;
use Seaf\Exception\Exception;
use Seaf\View\View;
use Seaf\Pattern\HelperContainer as Base;

/**
 * Environment\DI
 */
class HelperContainer extends Base
{
    /**
     * @var View
     */
    private $view;

    /**
     * @param  View
     */
    public function __construct (View $view)
    {
        parent::__construct();
        $this->addComponentNamespace(__CLASS__, '\\Helper');
        $this->view = $view;
    }
}
