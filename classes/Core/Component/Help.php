<?php
namespace Seaf\Core\Component;

use Seaf;
use Seaf\Exception;

/**
 * クラスの説明を取得する
 */
class Help
{
    public function helper ($class)
    {
        if (is_object($class)) {
            $object = $class;
            $class = get_class($class);
        }

        $rc = Seaf::ReflectionClass($class);

        Seaf::System()->printfn("\nCLASS %s\n", $class);
        $t = Seaf::Table('Methods');
        $t->add('Method', 'Params','File');
        foreach ($rc->getMethods() as $m) {
            if (!$m->isPublic()) continue;
            $type = $m->isStatic() ? '::': '->';
            $params = array();
            foreach($m->getParameters() as $p) {
                $params[] = "$".$p->name;
            }
            $t->add(
                $class.$type.$m->getName(),
                implode(", ", $params),
                $m->getFileName()
            );
            $descs[$m->getName()] = $m->getShortDesc();
        }
        $t->display();
        foreach ($descs as $k=>$v) {
            Seaf::System()->printfn("%s\n\t%s", $k,$v);
        }

        if (is_callable(array($object, 'getDynamicMethods'))) {
            try {
                $object->getDynamicMethods();
                $t = Seaf::Table('Dynamic Methods');
                $t->add('Key', 'Handler', 'Desc', 'Params','File');
                foreach ($object->getDynamicMethods() as $k=>$v) {
                    if (is_array($v)) {
                        list($class, $method) = $v;
                        if (is_object($class)) {
                            $v = get_class($class).'->'.$method;
                        }
                        $method = Seaf::ReflectionMethod($class,$method);
                        $params = array();
                        foreach($method->getParameters() as $p) {
                            $params[] = "$".$p->name;
                        }
                        $desc = $method->getShortDesc();
                        //$params = $method->getParamDesc();
                        $params = implode(", ", $params);
                        $file = $method->getFileName();
                        $t->add($k, $v, $desc, $params, substr($file, -20));
                    }else{
                        $t->add($k, 'Closure');
                    }

                }
                $t->display();
            } catch (Exception\Exception $e) {
                
            }
        }
    }
}
