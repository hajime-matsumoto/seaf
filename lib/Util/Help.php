<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util;


class Help
{
    public function __construct ($class)
    {
        $this->class = is_string($class) ? $class: get_class($class);
    }

    public function display ( )
    {
        echo $this->render();
    }

    public function render ( )
    {
        $text = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
        $text.= "\n[ HELP ]\n";
        $text.= $this->renderClass($this->class);
        $text.= "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
        $text.= "\n";
        return $text;
        return $text;
    }

    public function renderClass($class)
    {
        $class  = new \ReflectionClass($class);
        $text   = "||\n|| ------- CLASS INFOMATION -------\n";
        $text  .= "||\n";
        $text  .= "|| >>> [ NAME ] >>> ".$class->getName()."\n";
        foreach ($class->getInterfaces() as $i) {
            $text  .= "|| >>> [ Interface ] >>> ".$i->getName()."\n";
        }
        foreach ($class->getTraitNames() as $t) {
            $text  .= "|| >>> [ Trait ] >>> ".$t."\n";
        }
        $parent = $class->getParentClass();

        while($parent) {
            $text  .= "|| >>> [ Parent ] >>> ".$parent->getName()."\n";
            $parent = $parent->getParentClass();
        } 

        $text  .= "||\n";
        $text  .= "||\n|| ------- METHOD INFOMATION -------\n";
        $text  .= "||\n";
        $text  .= $this->renderMethods($class);
        // 継承したメソッドリスト

        return $text;
    }

    private function renderMethods(\ReflectionClass $class)
    {
        $text ='';
        $fromOthers = '';
        foreach($class->getMethods() as $m) {
            if ($m->isPrivate()) continue;
            if ($m->isProtected()) continue;
            if (substr($m->getName(),0,1) == "_") continue;
            if ($m->getFileName() !== $class->getFileName()) {
                $fromOthers.= $this->renderMethod($m);
                continue;
            }
            $text.=$this->renderMethod($m);
        }
        return $text . $fromOthers;
    }

    private function renderParam(\ReflectionParameter $p)
    {
        $text = '';
        if ($p->getClass()) {
            $text.=$p->getClass()->getName().' ';
        }
        $text.= '$'.$p->getName();
        if ($p->isOptional()) {
            $text.= ' = '.gettype($p->getDefaultValue());
        }
        return $text;
    }
    private function renderMethod(\ReflectionMethod $m)
    {
        $text = '';
        $params = $m->getParameters();
        $names = [];
        foreach($params as $param) {
            //$names[] = '$'.$param->getName();
            $names[] = $this->renderParam($param);
        }
        $comment = $m->getDocComment();
        $lines = [];
        foreach(explode("\n", $comment) as $line) {
            $line = ltrim($line, '/* ');
            if (empty($line)) continue;
            $lines[] = '||                    > '.$line;
        }
        if (!empty($names)) {
            $text.= "|| >>> [ METHOD ] >>> ".$m->getName()." ( ".implode(', ',$names)." )\n";
        }else{
            $text.= "|| >>> [ METHOD ] >>> ".$m->getName()." ( )\n";
        }
        if (!empty($lines)) {
            $text.= implode("\n", $lines)."\n";
        }
        return $text;
    }
}
