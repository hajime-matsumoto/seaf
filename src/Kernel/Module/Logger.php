<?php
namespace Seaf\Kernel\Module;

use Seaf\Exception\Exception;
use Seaf\Kernel\Kernel;
use Seaf\Logger\Logger as Base;

/**
 * ロガー
 */
class Logger extends Base implements ModuleIF
{
    public $name = 'Kernel';

    use ModuleTrait {
        ModuleTrait::__construct as ModuleConstruct;
    }

    private $kernel;

    public function __construct (Kernel $kernel)
    {
        parent::__construct();
        $this->ModuleConstruct($kernel);
    }

    public function initModule (Kernel $kernel)
    {
    }

    /**
     * stringの場合は名前付きロガーを返却
     * arrayの場合はWriterを追加
     * なすの場合はモジュールを返す
     *
     * @param string|array 
     * @return mixed
     */
    public function __invoke ($config = null)
    {
        if ($config == null) return $this;

        if (is_string($config)) {
            return $this->get($config);
        }

        foreach ($config as $k => $v)
        {
            $this->setWriter($k, $v);
        }
        return $this;
    }
}
