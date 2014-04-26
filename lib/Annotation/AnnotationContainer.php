<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Annotation;

use Seaf\Wrapper;

class AnnotationContainer
{
    /**
     * コンストラクタ
     */
    public function __construct ($class)
    {
        $rfc = Wrapper\ReflectionClass::create($class);

        $data = [];
        $data['class'] = $this->parseDocComment($rfc->getDocComment());

        foreach ($rfc->getMethods() as $method) {
            if ($rfc->getName() == $method->getDeclaringClass()->getName()) {
                if($anots = $this->parseDocComment($method->getDocComment())) {
                    $data['methods'][$method->getName()] = $anots;
                }
            }
        }
        foreach ($rfc->getProperties() as $prop) {
            if ($rfc->getName() == $prop->getDeclaringClass()->getName()) {
                if($anots = $this->parseDocComment($prop->getDocComment())) {
                    $data['props'][$prop->getName()] = $anots;
                }
            }
        }
        $this->data = $data;
    }

    /**
     * 検索する
     */
    public function findMethodsHasAnot($anot) 
    {
        $result = [];
        foreach($this->data['methods'] as $method=>$anots) 
        {
            foreach($anots['anots'] as $k=>$v) {
                if ($anot == $k) {
                    $result[$method] = $v;
                }
            }
        }
        return empty($result) ? []: $result;
    }

    /**
     * 検索結果に関数を適用する
     */
    public function mapMethodsHasAnot($anot, $callback) 
    {
        foreach ($this->findMethodsHasAnot($anot) as $method=>$values) {
            foreach ($values as $value) {
                $callback($method, $value);
            }
        }
    }

    /**
     * DocCommentブロックをパースする
     *
     * @param string
     * @return array
     */
    private function parseDocComment ($comment)
    {
        $lines = preg_split('/\n/', $comment);
        array_shift($lines);
        unset($lines[count($lines)-1]);
        $anot_start = false;
        $anots = [];
        $comment = '';
        foreach ($lines as $line) {
            $line = ltrim($line, '\t *');
            if (empty($line)) continue;
            if ($line{0} == '@') {
                $anot_start = true;
            }
            if ($anot_start == false) {
                $comment.=  $line."\n";
                continue;
            }
            $anot = explode(' ', $line, 2);
            if (count($anot) == 1) {
                $key   = $anot[0];
                $value = 1;
            }else{
                $key   = $anot[0];
                $value = $anot[1];
            }
            $key = ltrim($key, '@');
            $anots['anots'][$key][] = $value;
        }

        $comment = trim($comment);
        if (!empty($comment)) {
            $anots['comment'] = $comment;
        }
        return empty($anots) ? false: $anots;
    }
}
