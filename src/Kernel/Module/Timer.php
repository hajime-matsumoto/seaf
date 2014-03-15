<?php
namespace Seaf\Kernel\Module;

use Seaf\Exception\Exception;
use Seaf\Kernel\Kernel;
use Seaf\Pattern\DynamicMethod;

/**
 * 実行速度の計測
 */
class Timer extends Module
{
    /**
     * timers
     */
    private $timers = array();
    private $timers_start = array();
    private $start;
    private $lasts;

    private function microtime( )
    {
        return explode(' ', microtime());
    }

    /**
     * モジュールの初期化
     */
    public function initModule (Kernel $kernel)
    {
        $this->start = $this->microtime();
        $this->timers = array();
    }

    /**
     * @param string
     */
    public function __invoke ($name = null)
    {
        if ($name == null) return $this;

        $now = $this->microtime();

        $this->timers[] = array($name, $now);
    }

    private function diff ($start, $end)
    {
        $sec2 = $end[1] - $start[1];
        return round($sec2 + ($end[0] - $start[0]), 6);
    }

    /**
     * テキスト化する
     */
    public function __toString ( )
    {
        return $this->toString();
    }
    public function toString ( )
    {
        $ret = "\n";

        //var_dump($this->start);

        $cnter = array();
        $last = array();
        foreach ($this->timers as $k=>$v) {
            $name = $v[0];
            if (isset($last[$name])) { $last_time = $last[$name][1]; }else{ $last_time = false; }
            if (isset($cnter[$name])) { $cnter[$name]++; }else{ $cnter[$name] = 0; }
            $last[$name] = $v;

            $ret .= sprintf(
                "Timer %s#%03d %ss %s\n",
                $name,
                $cnter[$name],
                $this->diff($this->start, $v[1]),
                $last_time ?  $this->diff($last_time, $v[1])."s": ""
            );
        }

        return $ret;
    }
}
