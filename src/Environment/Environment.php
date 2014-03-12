<?php
namespace Seaf\Environment;

use Seaf\Kernel\Kernel;
use Seaf\Core\Pattern\ExtendableMethods;
use Seaf\Exception\Exception;

/**
 * 環境クラス
 * ===============================
 * 上書き不能メソッド
 * - map
 * - isMaped
 * - di
 */
class Environment
{
    public $owner;

    use ExtendableMethods {
        ExtendableMethods::call as methodCall;
    }


    /**
     * コンストラクタ
     *
     * @param
     * @return void
     */
    public function __construct ( $owner = null)
    {
        if (empty($owner)) {
            $owner = $this;
        }
        $this->owner = $owner;

        $this->di = new ComponentManager($this);
        $this->initEnvironment();
    }


    protected function initEnvironment()
    {
    }

    public function di($name = null, $params = null) 
    {
        if ($name == null) return $this->di;
        if (!$this->di->has($name)) {
            throw new Exception(
                array(
                    "%sは登録されていないコンポーネントです",
                    $name
                )
            );
        }

        if (!empty($params) && is_array($params) && is_callable($this->di->get($name))) {
            return Kernel::dispatch($this->di->get($name), $params, $this);
        }
        return $this->di->get($name);
    }

    public function call ($name, $params)
    {
        if ($this->isMaped($name)) {
            return $this->methodCall($name, $params);
        } elseif (isset($this->di) && $this->di->has($name)) {
            return $this->di($name, $params);
        }

        throw new Exception(
            array(
                "%sの%sは登録されていない呼び出しです",
                get_class($this),
                $name
            )
        );
    }

    public function __call($name, $params)
    {
        return $this->call($name, $params);
    }
}
