<?php
namespace Seaf\Pattern;

/**
 * コンフィグ配列から設定を行う機能
 * =====================================
 */
trait Configure
{
    /**
     * set<Config名>メソッドがあればそれを呼び
     * それ以外であれば
     * $this-><Config名> = <コンフィグ値>
     *
     * @param $config
     * @return array コンフィグで使われなかった配列
     */
    protected function configure ($config, $do_prop_set = true)
    {
        $not_set = array();
        foreach ($config as $k=>$v) {
            if (method_exists($this, $method = 'set'.ucfirst($k))) {
                $this->$method($v);
            }elseif ($do_prop_set){
                $this->$k = $v;
            }else{
                $not_set[$k] = $v;
            }
        }
        return $not_set;
    }
}
