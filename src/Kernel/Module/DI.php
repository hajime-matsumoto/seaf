<?php
namespace Seaf\Kernel\Module;

use Seaf\Exception\Exception;
use Seaf\Kernel\Kernel;
use Seaf\Pattern\DI as Base;

/**
 * ディペンデンシインジェクションパターン
 */
class DI extends Base implements ModuleIF
{
    use ModuleTrait {
        ModuleTrait::__construct as ModuleConstruct;
    }

    private $kernel;

    public function __construct (Kernel $kernel)
    {
        parent::__construct();
        $this->ModuleConstruct($kernel);
    }

    public function __invoke ($name = null)
    {
        if ($name == null) return $this;
        return $this->get($name);
    }

    public function initModule (Kernel $kernel)
    {
        // ConfigとLoggerは登録しておく
        $this->register('config','Seaf\Data\Config\Config');
        $this->register('logger','Seaf\Logger\Logger');
    }

    public function DICallFallBack($name, $params)
    {
        throw new Exception(array(
            '%sは登録されていません',
            $name
        ));
    }

}
