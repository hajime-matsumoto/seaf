<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\P18n;

class Translation
{
    private $key;
    private $p18n;

    public function __construct ($key, P18n $p18n)
    {
        $this->key = $key;
        $this->p18n = $p18n;
    }

    private function find ($lang, $key)
    {
        $res = $this->p18n->getTable( )
            ->find( )
            ->where(['lang'=>$lang, 'key'=>['$regex'=>"/^$key/i"]])
            ->execute();
        $ret = [];
        while($rec = $res->fetch()) {
            $ret[($rec['key'])] = $rec['translation'];
        }
        return empty($ret) ? false: $ret;
    }

    public function get($key = null)
    {
        $locale = $this->p18n->locale();
        $key = $key != null ? ($this->key ? $this->key.'.': '').$key: $this->key;

        if (false === $res = $this->find($locale, $key)) {
            if (false === $res = $this->find($this->p18n->defaultLocale(), $key)) {
                return '[['.$key.']]';
            }
        }

        if (count($res) == 1) {
            $value = current($res);

            if ( func_num_args( ) > 1 )
            {
                return vsprintf($value, array_slice(func_get_args(),1));
            }
            return $value;
        }
    }

    public function __invoke($kay)
    {
        return call_user_func_array([$this,'get'], func_get_args());
    }
}
