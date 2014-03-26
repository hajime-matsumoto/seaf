<?php
namespace Seaf\DI;

use Seaf\Pattern;
use Seaf\Data;
use Seaf\Exception;

/**
 * DIコンテナ
 */
class Definition
{
    use Pattern\Factory;
    use Pattern\Event;

    private $type, $definition, $options, $callback;

    /**
     * Definitionをセットする
     */
    public function configDefinition ($definition)
    {
        if (is_string($definition) && !is_callable($definition)) {
            $type = 'class';
        } elseif (is_callable($definition)) {
            $type = 'closure';
        } else {
            throw new Exception\Exception(array(
                'Definitionが不正です %s',
                print_r($definition, true)
            ));
        }

        $this->type = $type;

        $this->definition = $definition;
    }

    /**
     * Optionsをセットする
     */
    public function configOptions ($options)
    {
        if (!is_array($options)) $options = array();
        $this->options = $options;
    }

    /**
     * Callbackをセットする
     */
    public function configCallback ($callback)
    {
        $this->callback = $callback;
    }

    /**
     * インスタンスを作成する
     */
    public function create ( )
    {
        if ($this->type == 'class') {
            $class = new \ReflectionClass($this->definition);
            $instance = $class->newInstanceArgs($this->options);
        } else {
            $instance = call_user_func_array($this->definition, $this->options);
        }
        if (is_callable($this->callback)) {
            call_user_func($this->callback, $instance);
        }
        return $instance;
    }
}
