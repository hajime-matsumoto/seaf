<?php
namespace Seaf\Core\Component;

use Seaf;

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
        $t->add('Method', 'Desc', 'Params','File');
        foreach ($rc->getMethods() as $m) {
            if (!$m->isPublic()) continue;
            $type = $m->isStatic() ? '::': '->';
            $t->add(
                $class.$type.$m->getName(),
                $m->getShortDesc(),
                $m->getParamDesc(),
                $m->getFileName()
            );
        }
        $t->display();

        if (is_callable(array($object, 'getDynamicMethods'))) {
            $t = Seaf::Table('Dynamic Methods');
            $t->add('Key', 'Handler', 'Desc', 'Params','File');
            foreach ($object->getDynamicMethods() as $k=>$v) {
                if (is_array($v)) {
                    list($class, $method) = $v;
                    if (is_object($class)) {
                        $v = get_class($class).'->'.$method;
                    }
                    $method = Seaf::ReflectionMethod($class,$method);
                    $desc = $method->getShortDesc();
                    $params = $method->getParamDesc();
                    $file = $method->getFileName();
                    $t->add($k, $v, $desc, $params, substr($file, -20));
                }else{
                    $t->add($k, 'Closure');
                }

            }
            $t->display();
        }
    }
}
