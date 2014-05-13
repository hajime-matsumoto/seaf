<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Factory;

use Seaf\Util\Util;

use Seaf\Base\Container;
use Seaf\Base\CoR;

/**
 *  ファクトリコンテナ
 */
class Factory implements FactoryIF, Container\ContainerIF
{
    use Container\ArrayContainerTrait;

    private $nextFactory;

    public function __construct ( )
    {
        $this->caceSensitive(false);
    }

    /**
     * @See Seaf\Base\CoR\CoRIF
     */
    public function setNext(CoR\CoRIF $factory)
    {
        if ($this->nextFactory) {
            $this->nextFactory->setNext($factory);
        }else{
            $this->nextFactory = $factory;
        }
        for($i=1;$i < func_num_args(); $i++) {
            $this->setNext(func_get_arg($i));
        }
    }

    /**
     * @See Seaf\Base\CoR\CoRIF
     */
    public function getNext( )
    {
        return $this->nextFactory;
    }

    /**
     * @See Seaf\Base\Factory\FactoryIF
     */
    public function register($name, $args = [], $options = [])
    {
        $this->set($name, [
            'class'   => $name,
            'args'    => $args,
            'options' => $options
        ]);

        // エイリアスを設定
        if (isset($options['alias'])) {
            $this->dict('alias')->set($options['alias'], $name);
        }
    }

    /**
     * @See Seaf\Base\Factory\FactoryIF
     */
    public function canCreate($name, $deep = true)
    {
        if ($deep == false) {
            return $this->has($name) || $this->has(
                $this->dict('alias')->get($name, $name)
            );
        }

        $real_name = $this->dict('alias')->get($name, $name);
        if ($this->has($real_name)) {
            return true;
        }
        if ($next = $this->getNext()) {
            return $next->canCreate($name);
        }
        return false;
    }


    /**
     * @See Seaf\Base\Factory\FactoryIF
     */
    public function newInstance($name)
    {
        $args = func_num_args() > 1 ? 
            array_slice(func_get_args(),1):
            [];
        return $this->newInstanceArgs($name, $args);
    }

    /**
     * @See Seaf\Base\Factory\FactoryIF
     */
    public function newInstanceArgs($name, $args = [])
    {
        $real_name = $this->dict('alias')->get($name, $name);

        if ($this->canCreate($real_name, false)) {
            // 処理
            $info       = $this->get($real_name);

            $class_name = $info['class'];
            $args       = empty($args) ? $info['args']: $args;

            return Util::ClassName($class_name)->newInstanceArgs($args);

        }elseif($next = $this->getNext()){
            return $next->newInstanceArgs($name, $args);
        }

        return false;
    }
}
