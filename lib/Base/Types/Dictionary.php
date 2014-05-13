<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * 型ライブラリ
 */
namespace Seaf\Base\Types;

use Seaf\Util\Util;

/**
 * ディクショナリ型
 */
class Dictionary implements DictionaryIF,\ArrayAccess,\Iterator
{
    protected $data = [];
    private $indexes = 0;
    private $caseSensitiveFlg = true;
    private $useDotedNameFlg = false;

    /**
     * コンストラクタ
     *
     * @param array $default
     * @param bool $caceSensitiveFlg 大文字小文字を区別するか
     */
    public function __construct ($default = [])
    {
        $this->init($default);
    }

    private function hasWithDot ($name)
    {
        if (false === strpos($name, '.')) {
            return $this->has($name);
        }
        $token = strtok($name, '.');
        $head = $this->data;
        do {
            if (!isset($head[$token])) return false;
            $head =& $head[$token];

        } while($token = strtok('.'));
        return true;
    }

    private function getWithDot($name, $default = null)
    {
        if (false === strpos($name, '.')) {
            return $this->get($name);
        }
        $token = strtok($name, '.');
        $head = $this->data;
        do {
            if (!isset($head[$token])) return $default;
            $head =& $head[$token];

        } while($token = strtok('.'));
        return $head;
    }

    private function setWithDot($name, $value)
    {
        if (false === strpos($name, '.')) {
            return $this->set($name, $value);
        }
        $token = strtok($name, '.');
        $head =& $this->data;
        do {
            if (!isset($head[$token])) $head[$token] = array();
            $head =& $head[$token];

        } while($token = strtok('.'));
        $head = $value;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }


    public function __invoke($name, $default = null)
    {
        return $this->get($name, $default);
    }

    public function __toArray( )
    {
        return iterator_to_array($this);
    }

    /**
     * @param bool $flg 大文字小文字を区別するか
     */
    public function caseSensitive($flg = true)
    {
        $this->caseSensitiveFlg = $flg;
    }

    /**
     * @param bool $flg 大文字小文字を区別するか
     */
    public function useDotedName($flg = true)
    {
        $this->useDotedNameFlg = $flg;
    }

    /**
     * Keyフィルタ
     */
    protected function keyFilter($key)
    {
        if ($this->caseSensitiveFlg == false) {
            return strtolower($key);
        }
        return $key;
    }

    public function bind(&$data)
    {
        $this->data =& $data;
    }

    public function init($data = [])
    {
        $this->data = $data;
    }

    // Interface [ DirectoryIF ] に紐づく定義
    //

    // 通常の挙動
    public function set($name, $value = null)
    {
        $name = $this->keyFilter($name);

        if ($this->useDotedNameFlg && false !== strpos($name, '.')) {
            return $this->setWithDot($name, $value);
        }

        $this->data[$name] = $value;
    }

    public function get($name, $default = null)
    {
        $name = $this->keyFilter($name);

        if ($this->useDotedNameFlg && false !== strpos($name, '.')) {
            return $this->getWithDot($name, $default);
        }

        if ($this->has($name)) {
            return $this->data[$name];
        }
        return $default;
    }

    public function &getRef($name)
    {
        if ($this->has($name)) {
            return $this->data[$name];
        }
        return false;
    }

    public function clear( )
    {
        if(func_num_args() == 0) {
            $this->data = [];
            return;
        }

        $name = func_get_arg(0);
        $name = $this->keyFilter($name);

        if ($this->has($name)) {
            unset($this->data[$name]);
        }
    }

    public function has($name) 
    {
        $name = $this->keyFilter($name);

        if ($this->useDotedNameFlg && false !== strpos($name, '.')) {
            return $this->hasWithDot($name, $default);
        }

        if (!is_array($this->data)) {
            return false;
        }
        return array_key_exists($name,$this->data);
    }

    public function isEmpty( )
    {
        if(func_num_args() == 0) {
            return empty($this->data);
        }

        $name = func_get_arg(0);

        $name = $this->keyFilter($name);
        if ($this->has($name)) {
            return empty($this->data[$name]);
        }
        return true;
    }

    // キュー的な処理
    public function append($name)
    {
        if(func_num_args() == 1) {
            $value = $name;
            array_push($this->data, $value);
            return;
        }

        $value = func_get_arg(1);

        $name = $this->keyFilter($name);
        if ($this->has($name)) {
            $array = $this->get($name);
            array_push($array, $value);
            $this->set($name, $array);
        }else{
            $this->set($name, [$value]);
        }
    }

    public function prepend($name)
    {
        if(func_num_args() == 1) {
            $value = $name;
            array_unshift($this->data, $value);
            return;
        }

        $value = func_get_arg(1);

        $name = $this->keyFilter($name);

        if ($this->has($name)) {
            $array = $this->get($name);
            array_unshift($array, $value);
            $this->set($name, $array);
        }else{
            $this->set($name, [$value]);
        }
    }

    public function pop( )
    {
        if(func_num_args() == 0) {
            return array_pop($this->data);
        }

        $name = func_get_arg(0);
        $name = $this->keyFilter($name);

        if ($this->isEmpty($name)) {
            return false;
        }

        return array_pop($this->data[$name]);
    }

    public function shift( )
    {
        if(func_num_args() == 0) {
            return array_unshift($this->data);
        }

        $name = func_get_arg(0);
        $name = $this->keyFilter($name);

        if ($this->isEmpty($name)) {
            return false;
        }

        return array_shift($this->data[$name]);
    }

    public function first($name)
    {
        if($this->isEmpty($name)) {
            return false;
        }
        return current($this->data[$name]);
    }

    public function dict($name)
    {
        if ($this->has($name)) {
            $data = $this->get($name);
            if (!is_array($data)) {
                $this->set($name, [$data]);
            }
        }else{
            $this->set($name, []);
        }

        $dict = new static();
        $dict->caseSensitive($this->caseSensitiveFlg);
        $dict->bind($this->getRef($name));
        return $dict;
    }

    public function dump()
    {
        Util::Dump(iterator_to_array($this));
    }

    // ======================================
    // ArrayAccess
    // ======================================

    /**
     * Offset Get
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Offset Set
     */
    public function offsetSet($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * Offset UnSet
     */
    public function offsetUnset($name)
    {
        unset ($this->data[$name]);
    }

    /**
     * Offset Exists
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    // ======================================
    // Iterator
    // ======================================

    public function rewind ( )
    {
        $this->indexes = array_keys($this->data);
    }

    public function valid ( )
    {
        return false !== current($this->indexes);
    }

    public function current ( )
    {
        return $this->get(current($this->indexes));
    }
    public function key ( )
    {
        return current($this->indexes);
    }
    public function next ( )
    {
        return next($this->indexes);
    }

}
