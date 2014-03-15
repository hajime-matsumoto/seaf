<?php
namespace Seaf\Pattern;

use Seaf\Kernel\Kernel;
use Seaf\Exception\Exception;
use Seaf\View\View;

/**
 * ヘルパーコンテナ
 * ========================
 *
 * DIコンテナ + DynamicMethod
 */
class HelperContainer extends DI
{
    use DynamicMethod {
        DynamicMethod::__call as MethodCall;
    }

    /**
     * Pattern\DI::hasをオーバライドする
     *
     * @param $name
     * @return bool
     */
    public function has ($name)
    {
        $result = parent::has($name);

        // カーネルのDIから探す
        if (Kernel::DI()->has($name)) {
            $this->register($name, Kernel::DI()->get($name));
            return true;
        }

        if (!$result && !empty($this->tryed)) {
            Kernel::logger(get_class($this))->warning(array(
                'Tryed: %s',
                implode(',', $this->tryed)
            ));
        }
        return $result;
    }

    /**
     * Pattern\DI::getをオーバライドする
     *
     * @param $name
     * @return object
     */
    public function get ($name)
    {
        $instance = parent::get($name);
        if (method_exists($instance,'getHelper')) {
            return $instance->getHelper();
        }
        return $instance;
    }

    /**
     * Pattern\DI::createをオーバライドする
     *
     * @param $name
     * @return object
     */
    public function create ($name)
    {
        $instance = parent::create($name);
        if ($instance instanceof HelperIF) {
            $instance->initHelper($this->view);
        }

        return $instance;
    }

    /**
     * コール
     *
     * @param string
     * @return mixed
     */
    public function __get ($name)
    {
        return $this->get($name);
    }

    /**
     * コール
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function __call ($name, $params)
    {
        return $this->MethodCall($name, $params);
    }

    /**
     * ダイナミックメソッドのコールが失敗したら
     * Pattern\DIの__callを呼び出す
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function callFallBack($name, $params)
    {
        return parent::__call($name, $params);
    }

    /**
     * DICallFallBack
     *
     * @param string $name
     * @param array $params
     * @return void
     */
    public function DICallFallBack ($name, $parms)
    {
        throw new Exception(array(
            "Helper %s を解決できません",
            $name
        ));
    }
}
