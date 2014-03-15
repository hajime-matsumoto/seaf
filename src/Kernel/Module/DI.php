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

    /**
     * @param Kernel
     */
    private $kernel;

    public function __construct (Kernel $kernel)
    {
        parent::__construct();
        $this->ModuleConstruct($kernel);
    }

    /**
     * 呼び出された時の処理
     */
    public function __invoke ($name = null)
    {
        if ($name == null) return $this;
        return $this->get($name);
    }

    /**
     * モジュールを初期化する
     *
     * @param Kernel
     */
    public function initModule (Kernel $kernel)
    {
        // Configをグローバル化
        $this->register('config','Seaf\Data\Config\Config');

        // Loggerをグローバル化
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
