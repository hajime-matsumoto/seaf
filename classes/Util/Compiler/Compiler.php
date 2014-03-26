<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\Compiler;

use Seaf\DI;

class Compiler
{
    private $compilers = array();

    /**
     * コンパイラを取得する
     *
     * @param string $name
     * @return Command\Base
     */
    public function getCompiler ($name)
    {
        $name = ucfirst($name);

        if (isset($this->compilers[$name])) {
            return $this->compilers[$name];
        }
        $class = __NAMESPACE__.'\\Command\\'.$name;
        return $this->compilers[$name] = new $class();
    }


    public function __get ($name)
    {
        return $this->getCompiler($name);
    }

    public function __call ($name, $params)
    {
        if ($name == 'auto') {
            $ext = substr($params[0], strrpos($params[0], '.') + 1);
            $name = strtolower($ext);
        }
        return call_user_func_array($this->$name, $params);
    }
}
