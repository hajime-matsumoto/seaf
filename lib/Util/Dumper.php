<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Util;

/**
 * 変数の中身をダンプする
 */
class Dumper
{
    private $limit;
    private $object_hash_list = [];

    public function __construct($data, $level)
    {
        $this->data = $data;
        $this->limit = $level;
    }

    public function dump($useReturn = false)
    {
        if (!$useReturn) {
            echo $this->realDump($this->data, 0)."\n";
        }else{
            return $this->realDump($this->data, 0);
        }
    }

    private function realDump($data, $level)
    {
        if (is_object($data)) {
            return $this->dumpObject($data, $level);
        } elseif (is_array($data)) {
            return $this->dumpArray($data, $level);
        } else {
            return $this->dumpSchaller($data, $level);
        }
    }

    private function dumpObject($object, $level)
    {
        $hash = spl_object_hash($object);

        if (isset($this->object_hash_list[$hash])) {
            return $this->sprintf($level, "*RECURSION(%s #%s)*", get_class($object), $hash);
        }else{
            $this->object_hash_list[$hash] = $object;
        }

        if ($this->limit <= $level) {
            return $this->sprintf(
                $level,
                "Object('%s') *NEST_LIMIT(%s) LEVEL(%s)*\n",
                get_class($object),
                $this->limit,
                $level
            );
        }

        if ($object instanceof \Closure) {
            return $this->dumpClosure($object, $level);
        }

        $ref = new \ReflectionClass($object);

        $dumpText = $this->sprintf($level, "class %s #%s {\n", $ref->getName(), $hash);
        $props = [];
        foreach ($ref->getProperties() as $prop) {
            $props[$prop->getName()] = $prop;
        }

        $parent = $ref->getParentClass();
        while($parent) {
            $parent_props = $parent->getProperties();
            foreach($parent_props as $k=>$parent_prop)
            {
                if (!array_key_exists($parent_prop->getName(), $props))
                {
                    $props[$parent_prop->getName()] = $parent_prop;
                }
            }
            $parent = $parent->getParentClass();
        }
        $dumpText.=$this->dumpProperties($object, $props, $level + 1);
        $dumpText.= $this->sprintf($level, "}");

        return $dumpText;
    }

    private function dumpProperties($object, $props, $level)
    {
        $dumpText = '';
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            if ($prop->isPublic( )) {
                $access = 'public';
            }elseif ($prop->isProtected( )) {
                $access = 'protected';
            }elseif ($prop->isPrivate( )) {
                $access = 'private';
            }
            $dumpText .= $this->sprintf(
                $level,
                "%s $%s =>\n",
                $access,
                $prop->getName()
            );
            $dumpText .= $this->sprintf(
                null,
                "%s \n",
                $this->realDump($prop->getValue($object), $level+ 1)
            );
        }
        return $dumpText;
    }

    private function dumpSchaller($data, $level)
    {
        $type = gettype($data);
        switch($type)
        {
        case 'integer':
            return $this->sprintf($level, "int(%s)", $data);
            break;
        case 'boolean':
            return $this->sprintf($level, "bool(%s)", $data ? 'true': 'false');
            break;
        case 'string':
            return $this->sprintf($level, "string(%s) \"%s\"", strlen($data), $data);
            break;
        case 'NULL':
            return $this->sprintf($level, "%s", 'NULL');
            break;
        default:
            return $this->sprintf($level,"unknown %s", $type);
            break;
        }
    }

    private function dumpArray($data, $level)
    {
        $cnt = count($data);

        if ($this->limit < $level) {
            $dumpText = $this->sprintf($level, "array(%s) {", $cnt);
            $dumpText.= $this->sprintf(
                null, "*NEST_LIMIT(%s) LEVEL(%s)*", $this->limit, $level
            );
            $dumpText.= $this->sprintf(null, "}");
            return $dumpText;
        }

        if ($cnt == 0) {
            return $this->sprintf($level, 'array(%s) { }', $cnt);
        }else{
            $dumpText = $this->sprintf($level, "array(%s) {\n", $cnt);
            foreach ($data as $k=>$v)
            {
                $dumpText.= $this->sprintf($level, "[%s] =>\n",
                    is_int($k) ? $k: "\"$k\""
                );
                $dumpText.= $this->sprintf(null, "%s\n",
                    $this->realDump($v, $level +1)
                );
            }
            $dumpText.= $this->sprintf($level, "}");
        }
        return $dumpText;
    }

    private function dumpClosure($object, $level)
    {
        $ref = new \ReflectionFunction($object);
        $dumpText = $this->sprintf($level,"Closure{} =>\n");
        $dumpText.= $this->sprintf($level,"%s(%s)", $ref->getFileName(), $ref->getStartLine());
        return $dumpText;
    }

    private function sprintf($level, $format)
    {
        $nest = $level;
        return 
            str_repeat("  ",$nest).
            vsprintf(
                $format,
                array_slice(func_get_args(),2)
        );
    }



}
