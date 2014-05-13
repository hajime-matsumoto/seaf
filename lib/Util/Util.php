<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Util;

use Seaf\Base\Types;

class Util 
{
    public static function Dictionary ($default = [])
    {
        if(empty($default)) $default = [];
        if (func_num_args() > 1) {
            foreach(array_slice(func_get_args(),1) as $var) {
                if (is_array($var)) {
                    $default = array_merge($default, $var);
                }
            }
        }
        return new Types\Dictionary($default);
    }

    public static function MethodContainer ($default = [])
    {
        return new MethodContainer($default);
    }

    public static function ClassName ( )
    {
        return new Types\ClassNameString(func_get_args());
    }

    public static function FileName ( )
    {
        return new Types\FileNameString(func_get_args());
    }

    public static function Fifo($name, $mode = 0666)
    {
        return new Types\Fifo($name, $mode);
    }


    public static function SeparatedString ($sep = ',', $default = null, $out = false)
    {
        return new Types\SeparatedString($sep, $default, $out);
    }

    public static function dump ($data, $useReturn = false, $level = 8)
    {
        $traces = debug_backtrace(true, 4);
        $trace = static::Dictionary(array_shift($traces));
        $prev = static::Dictionary(array_shift($traces));
        $prev2 = static::Dictionary(array_shift($traces));
        $prev3 = static::Dictionary(array_shift($traces));

        $header = sprintf("\n||== SEAF DUMPER [ START ] ==\n");
        $header.= sprintf("|| Limit [$level]\n");
        $header.= sprintf("||== STACK = File [%s (#%s)]\n", $prev3->file, $prev3->line);
        $header.= sprintf("||== STACK = File [%s (#%s)]\n", $prev2->file, $prev2->line);
        $header.= sprintf("||== STACK = File [%s (#%s)]\n", $prev->file, $prev->line);
        $header.= sprintf("||== STACK = File [%s (#%s)]\n", $trace->file, $trace->line);
        $footer = sprintf("||== SEAF DUMPER [ END ] ======\n");
        $dumper = new Dumper($data, $level);

        if ($useReturn) {
            return $header.$dumper->dump($useReturn).$footer;
        }else{
            echo $header;
            $dumper->dump($useReturn);
            echo $footer;
        }
    }

    public static function help ($object)
    {
        $help = new Help($object);
        return $help->render();
        /*
        $class  = new \ReflectionClass($object);
        $text   = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
        $text  .= "\n[ HELP ]\n";
        $text  .= "||\n|| ------- CLASS INFOMATION -------\n";
        $text  .= "||\n";
        $text  .= "|| >>> [ NAME ] >>> ".get_class($object)."\n";
        $text  .= "||\n|| ------- METHOD INFOMATION -------\n";
        $text  .= "||\n";

        do {
            var_dump($class->getTraitNames());
            foreach($class->getMethods() as $m) {
                if ($m->isPrivate()) continue;
                if ($m->getFileName() !== $class->getFileName()) continue;
                if (substr($m->getName(),0,1) == "_") continue;

                $params = $m->getParameters();
                $names = [];
                foreach($params as $param) {
                    $names[] = '$'.$param->getName();
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
            }
        } while ($class = $class->getParentClass());
        $text.= "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
        $text.= "\n";
        return $text;
        */
    }

}
