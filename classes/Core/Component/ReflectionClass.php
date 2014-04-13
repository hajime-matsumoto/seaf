<?php
namespace Seaf\Core\Component;

use Seaf;

class ReflectionClass
{
    use ComponentTrait;

    public function _componentHelper( $class )
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
    public function getClassAnnotation($getter, $prefix = 'Seaf')
    {
        return $this->_getAnnotation($this->getDocComment(), $getter, $prefix);
    }

    public function getPropAnnotation($getter, $prefix = 'Seaf')
    {
        $anots = [];
        foreach ($this->getProperties() as $prop) {
            if ($prop->getDeclaringClass()->getName() == $this->getName()) {
                $anot = $this->_getAnnotation($prop->getDocComment(), $getter, $prefix);
                if (!empty($anot)) {
                    $anots[$prop->getName()] = $anot;
                }
            }
        }
        return $anots;
    }

    public function getMethodAnnotation($getter, $prefix = 'Seaf')
    {
        $anots = [];
        foreach ($this->getMethods() as $prop) {
            if ($prop->getDeclaringClass()->getName() == $this->getName()) {
                $anot = $this->_getAnnotation($prop->getDocComment(), $getter, $prefix);
                if (!empty($anot)) {
                    $anots[$prop->getName()] = $anot;
                }
            }
        }
        return $anots;
    }



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

    private function _getAnnotation($doc_comment, $getter = null, $prefix = 'Seaf')
    {
        $comment = '';
        $anots = array();
        $commentBlockFlag = true;

        if (is_array($getter)) {
            foreach ($getter as $key=>$val) {
                if (is_int($key)) {
                    $key = $val;
                    $val = ['type'=>'single'];
                }
                $new_getter[$key] = $val;
            }
            $getter = $new_getter;
        }


        $line = preg_split("/\n/", $doc_comment); //改行でコメントブロックを分解
        for ($i=1; $i<(count($line)-1);$i++) 
        {
            $line[$i] = ltrim($line[$i], '*, '); // アスタリスクとスペースを削除
            if ($line[$i] == '') continue;

            // @から始まる行があったらコメントブロック終了
            if ($line[$i][0] == "@") $commentBlockFlag = false;
            if ($commentBlockFlag==true) { // コメントブロック中はコメントにデータを挿入
                $comment.= $line[$i]."\n";
                continue;
            }

            // 改行されている場合
            if ($line[$i][0] !== "@" && $key != false) {
                $anots[$key] .= "\n".$line[$i];
                continue;
            }

            list($key, $value) = explode(' ', $line[$i], 2);
            $key = trim($key,' ,@');
            if ($getter == null) {
                $anots[trim($key,' ,@')] = trim($value);
                continue;
            }
            if (0 === stripos($key, $prefix)) {
                $key = lcfirst(substr($key, strlen($prefix)));
                if (isset($getter[$key])) {
                    $policy = $getter[$key]['type'];

                    if ($policy == 'multi') {
                        $anots[$key][] = $value;
                    }else{
                        if ($policy == 'bool') {
                            if ($value == 'false') $value = false;
                            $value = (bool) $value;
                        }
                        $anots[$key] = $value;
                    }
                }
            } else {
                $key = false;
            }
        }
        if (empty($anots)) return;
        $anots['comment'] = trim($comment);
        return $anots;
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
