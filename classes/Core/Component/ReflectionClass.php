<?php
namespace Seaf\Core\Component;

use Seaf;

class ReflectionClass
{
    public function __construct ( )
    {
    }

    public function helper( $class )
    {
        if (is_object($class)) $class = get_class($class);

        if (!class_exists($class)) {
            Seaf::logger()->emerg(array(
                "クラス%sは定義されていません",
                $class
            ));
        }

        return new SeafReflectionClass($class);
    }
}

class SeafReflectionClass extends \ReflectionClass
{
    public function mapAnnotation($callback, $prefix = 'Seaf')
    {
        foreach($this->getMethods() as $method){
            if ($method->getDeclaringClass()->getName() == $this->getName()) {

                $comment = $method->getDocComment();
                $anots = $this->getAnnotation($comment, $prefix);
                $callback($method, $anots);
            }
        }
    }
    public function mapPropAnnotation($callback, $prefix = 'Seaf')
    {
        foreach($this->getProperties() as $prop){
            if ($prop->getDeclaringClass()->getName() == $this->getName()) {

                $comment = $prop->getDocComment();
                $anots = $this->getAnnotation($comment, $prefix);
                $callback($prop, $anots);
            }
        }
    }

    public function mapClassAnnotation($callback, $prefix = 'Seaf')
    {
        $anot = $this->getAnnotation($this->getDocComment(), $prefix);
        $callback($this, $anot);
    }

    private function getAnnotation($comment, $prefix)
    {
        $anots = array();
        $line = preg_split("/\n/", $comment);
        $desc = '';
        for ($i=1;$i<(count($line)-1);$i++) {
            if (preg_match('#[^@]+@'.$prefix.'([^\s]+)\s+(.+)#',$line[$i],$m)) {
                $key = lcfirst($m[1]);
                if (isset($anots[$key])) {
                    if (!is_array($anots[$key])) {
                        $anots[$key] = array($anots[$key]);
                    }
                    $anots[$key][] = $m[2];
                }else{
                    $anots[$key] = array($m[2]);
                }
            }else{
                $desc.=trim($line[$i],' *');
            }
        }
        $anots['desc'] = $desc;
        return $anots;
    }

    public function getMethods ($filter = null)
    {
        $ret = array();
        foreach (get_class_methods($this->getName()) as $m)
        {
            $ret[] = Seaf::ReflectionMethod($this->getName(), $m);
        }
        return $ret;
    }

}
