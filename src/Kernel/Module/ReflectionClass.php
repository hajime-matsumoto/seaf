<?php
namespace Seaf\Kernel\Module;

use Seaf\Core\Pattern\ExtendableMethods;

class ReflectionClass
{
    public function __construct ( )
    {
    }

    public function __invoke( $class )
    {
        return new SeafReflectionClass($class);
    }
}

class SeafReflectionClass extends \ReflectionClass
{
    public function mapAnnotation($callback)
    {
        foreach($this->getMethods() as $method){
            $anots = array();
            if ($method->getDeclaringClass()->getName() == $this->getName()) {

                $comment = $method->getDocComment();
                $line = preg_split("/\n/", $comment);
                for ($i=1;$i<(count($line)-1);$i++) {
                    if (preg_match('#[^@]+@Seaf([^\s]+)\s+(.+)#',$line[$i],$m)) {
                        $anots[lcfirst($m[1])] = $m[2];
                    }
                }
                $callback($method, $anots);
            }
        }
    }
}
