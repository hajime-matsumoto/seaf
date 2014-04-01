<?php
namespace Seaf\Core\Component;

use Seaf;

class ReflectionMethod
{
    public function __construct ( )
    {
    }

    public function helper ($class, $method)
    {
        if (is_object($class)) $class = get_class($class);

        if (!class_exists($class)) {
            Seaf::logger()->emerg(array(
                "クラス%sは定義されていません",
                $class
            ));
        }

        return new SeafReflectionMethod($class, $method);
    }
}

class SeafReflectionMethod extends \ReflectionMethod
{
    public function getShortDesc ( )
    {
        $doc = $this->getDocComment();
        $lines = explode("\n", $doc);
        array_shift($lines);

        $desc = '';
        foreach ($lines as $line) 
        {
            $desc .= $line = trim($line, ' *');
            if (empty($line)) break;
        }
        return $desc;
    }

    public function getParamDesc ( )
    {
        $doc = $this->getDocComment();
        $lines = explode("\n", $doc);
        array_shift($lines);

        $params = array();
        foreach ($lines as $line) 
        {
            $line = trim($line, ' *');
            if (0===strpos($line,'@param')) {
                $params[] = substr($line, 6);
            }
        }
        return implode(', ', $params);
    }
}
