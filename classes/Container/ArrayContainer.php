<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Container;

use Seaf\Util\ArrayHelper;

/**
 * ArrayContainer
 */
class ArrayContainer implements \ArrayAccess,\Iterator
{
    protected $data = array();

    /**
     * コンストラクタ
     *
     * @param mixed $data = array()
     */
    public function __construct ($data = array())
    {
        $this->data = $data;
    }

    /**
     * クリア
     *
     * @param mixed $data = array()
     */
    public function clear ( )
    {
        foreach ($this->data  as $k=>$v) {
            unset($this->data[$k]);
        }
    }

    /**
     * 値を取得する
     *
     * hoge.hugaのように.区切りで配列内配列にアクセスできる
     *
     * @param stirng $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return ArrayHelper::get($this->data, $name, $default);
    }

    public function getOnlyArray($name)
    {
        $res = ArrayHelper::get($this->data, $name, []);
        if (!is_array($res)) {
            return array();
        }
        return $res;
    }
    /**
     * 値を設定する
     *
     * @param stirng $name
     * @param mixed $value
     */
    public function set ($name, $value = false)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) {
                $this->set($k, $v);
            }
            return $this;
        }
        $this->data[$name] = $value;
    }

    /**
     * 値が存在すればTrue
     *
     * @param stirng $name
     * @return bool
     */
    public function has ($name)
    {
        return ArrayHelper::has($this->data, $name);
    }

    /**
     * 値を削除する
     *
     * @param stirng $name
     */
    public function del ($name)
    {
        unset($this->data[$name]);
    }

    public function toArray ( )
    {
        return $this->data;
    }

    // ----------------------------------
    // マジックメソッド
    // ----------------------------------

    /**
     * __get
     *
     * ->getにつなぐ
     * ->hoge = ->get('hoge')
     */
    public function __get ($name)
    {
        return $this->get($name);
    }

    /**
     * __set
     *
     * ->setにつなぐ
     * ->hoge = 'huga'; // ->set('hoge', 'huga')
     */
    public function __set ($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * __invoke
     *
     * ->getにつなぐ
     *
     * $this($name, $default); // $this->get($name, $default)
     */
    public function __invoke ($name, $default = null)
    {
        return call_user_func_array(array($this,'get'), func_get_args());
    }


    // ----------------------------------
    // Util
    // ----------------------------------
    
    /**
     * 値を複数取得する
     *
     * @param $v,...
     * @return array
     */
    public function export ( )
    {
        $args = func_get_args();

        $ret = array();
        foreach ($args as $k) {
            $ret[] = $this->get($k);
        }
        return $ret;
    }

    /**
     * 設定されているキーをすべて取得する
     *
     * @return array
     */
    public function getKeys ( )
    {
        return array_keys($this->data);
    }

    // ----------------------------------
    // For ArrayAccess 
    // ----------------------------------

    /**
     * \ArrayAccess::offsetGet()
     */
    public function offsetGet ($offset)
    {
        return $this->get($offset);
    }

    /**
     * \ArrayAccess::offsetSet()
     */
    public function offsetSet ($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * \ArrayAccess::offsetUnset()
     */
    public function offsetUnset ($offset)
    {
        return $this->del($offset);
    }

    /**
     * \ArrayAccess::offsetExists()
     */
    public function offsetExists ($offset)
    {
        return $this->has($offset);
    }

    // ----------------------------------
    // For Iterator
    // ----------------------------------

    /**
     * \Iterator::current
     */
    public function current ( )
    {
        return $this->get($this->key());
    }

    /**
     * \Iterator::key
     */
    public function key ( )
    {
        return key($this->data);
    }

    /**
     * \Iterator::next
     */
    public function next ( )
    {
        return next($this->data);
    }

    /**
     * \Iterator::rewind
     */
    public function rewind ( )
    {
        reset($this->data);
    }

    /**
     * \Iterator::valid
     */
    public function valid ( )
    {
        if (current($this->data)) {
            return true;
        } else {
            return false;
        }
    }
}
