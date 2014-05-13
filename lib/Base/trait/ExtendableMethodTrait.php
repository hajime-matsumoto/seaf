<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * BASEモジュール
 */
namespace Seaf\Base;

use Seaf\Util\Util;


trait ExtendableMethodTrait
{
    private $methodBox;

    protected function methodbox()
    {
        if (!$this->methodBox) {
            $this->methodBox =  Util::Dictionary();
        }
        return $this->methodBox;
    }

    public function mapMethod($name, $act = null)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) {
                $this->mapMethod($k, $v);
            }
            return $this;
        }

        if (is_string($act)) {
            $act = [$this, $act];
        }
        $this->methodBox()->$name = $act;
        return $this;
    }

    public function __call($name, $params)
    {
        if ($this->methodBox()->has($name)) {
            return call_user_func_array($this->methodBox()->$name, $params);
        }
        $this->__callWhenMethodNotExists($name, $params);
    }

    abstract protected function __callWhenMethodNotExists($name, $params);

}
