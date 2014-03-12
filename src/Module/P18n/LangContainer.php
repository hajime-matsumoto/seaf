<?php
namespace Seaf\Module\P18n;

/**
 * LangContainer
 */
class LangContainer 
{
    private $langs = array();
    private $data = "";
    private $name;

    /**
     * __construct
     *
     * @param $file
     */
    public function __construct ($data = array(), $name = null)
    {
        if (is_array($data)) {
            $this->loadArray($data);
        }elseif(is_string($data)){
            $this->data = $data;
        }

        $this->name = $name;
    }

    public function loadArray($data)
    {
        foreach ($data as $k=>$v)
        {
            $this->set($k, $v);
        }
    }

    public function set($k, $v)
    {
        $this->langs[$k] = new self($v, $k);
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return $this->langs[$key];
        }
        \Seaf::logger()->warning(array(
            "%sが見つかりません",$key
        ));
        return '[['.$key.']]';
    }

    public function has($key)
    {
        return isset($this->langs[$key]);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __toString()
    {
        return $this->data;
    }

    public function __invoke()
    {
        if(strpos($this->name,'_') !== false) {
            $type = substr($this->name,strrpos($this->name,'_')+1);
            return $this->{"filter".ucfirst($type)}(func_get_args());
        }
        return vsprintf($this->data, func_get_args());
    }

    public function filterPl($params)
    {
        $num = $params[0];
        if ($this->has($num)) {
            return sprintf($this->get($num), $num);
        }else{
            return sprintf($this->get(''), $num);
        }
        \Seaf::logger()->warning(
            "nが見つかりません"
        );
    }
}
