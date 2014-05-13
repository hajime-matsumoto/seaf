<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Util;

/**
 * オブジェクトのマニュアルを出す
 */
class Man
{
    private $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function show ( )
    {
        $ref = new \ReflectionClass(get_class($this->object));
        echo "\n";
        echo str_repeat('=',40)."\n";
        echo "Class ".get_class($this->object)."\n";
        echo str_repeat('-',40)."\n";

        foreach ($ref->getMethods() as $method) {
            if (
                $ref->getName() == $method->getDeclaringClass()->getName()
                &&
                $method->isPublic()
            ) {
                //echo $ref->getName().'->'.$method->getName();
                echo "Method: ->".$method->getName();
                echo '(';

                $parts = [];
                foreach ($method->getParameters() as $param) {
                    $part = '$'.$param->getName();
                    //var_dump($param->getPosition());
                    if ($param->isOptional()) {
                        $part.= ' = "'.$param->getDefaultValue().'"';
                    }
                    $parts[] = $part;
                }
                echo implode(', ', $parts);
                echo ')';
                echo "\n";
                foreach(explode("\n", $method->getDocComment()) as $line) {
                    $line = trim($line, " */");
                    if(!empty($line)) {
                        echo "# " .$line."\n";
                    }
                }

            }
        }
    }
}
