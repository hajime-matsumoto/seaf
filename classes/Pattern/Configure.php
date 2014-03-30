<?php
namespace Seaf\Pattern;

use Seaf\Exception;

/**
 * 配列から一気にオブジェクトを設定する処理
 * をサポートするトレイト。
 *
 * <code>
 * use Configure;
 * $someobject->configure(配列)
 * </code>
 *
 * config<Config名>メソッドがあればそれを呼び
 * それ以外であれば
 * $this-><Config名> = <コンフィグ値>
 *
 */
trait Configure
{
    public function getConfigurePrefix ( )
    {
        return 'config';
    }

    /**
     * config<Config名>メソッドがあればそれを呼び
     * それ以外であれば
     * $this-><Config名> = <コンフィグ値>
     *
     * @param array $config
     * @param bool $do_prop_set $this->config名 = 値をセットするか
     * @param bool $strict セットできないコンフィグ名があればエラーを吐く
     * @param array $ignore 無視するコンフィグ名のリスト
     * @return array コンフィグで使われなかった配列
     */
    public function configure ($config, $do_prop_set = false, $strict = true, $ignore = array())
    {
        $prefix = $this->getConfigurePrefix();
        $not_set = array();
        foreach ($config as $k=>$v) {
            if (in_array($k, $ignore)) {

            } elseif (method_exists($this, $method = $prefix.ucfirst($k))) {
                $this->$method($v);
            }elseif ($do_prop_set){
                $this->$k = $v;
            }else{
                $not_set[$k] = $v;
            }
        }

        if (!empty($not_set) && $strict == true) {
            throw new Exception\Exception(array(
                'Configure Missed %s For %s',
                print_r($not_set, true),
                get_class($this)
            ));
        }

        return $not_set;
    }
}
