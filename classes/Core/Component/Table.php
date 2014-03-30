<?php
namespace Seaf\Core\Component;

use Seaf;
use Seaf\Util\ArrayHelper as AH;

/**
 * テーブル状にデータを表示する
 */
class Table
{
    private $rows = [];
    private $max = [];
    private $title;

    public function __construct ($title = null)
    {
        $this->title = $title;
    }

    public function helper ($title = null)
    {
        return new Table($title);
    }

    public function add ( )
    {
        $datas = func_get_args();
        foreach ($datas as $k=>$v)
        {
            if ($this->getMax($k) < mb_strwidth($v)) {
                $this->setMax($k, mb_strwidth($v));
            }
        }
        $this->rows[] = $datas;
    }

    public function display ( )
    {
        if ($this->title) {
            Seaf::System()->printfn("\n%s\n", $this->title);
        }
        $cnt = 0;
        foreach ($this->rows as $row)
        {
            $vals = $seps = array();
            foreach ($row as $k=>$v) 
            {
                $vals[] = $v.str_repeat(' ', $this->getMax($k) - mb_strwidth($v));
                if ($cnt == 0) {
                    $seps[] = str_repeat('-', $this->getMax($k));
                }
            }

            echo implode(' ', $vals)."\n";
            if ($cnt == 0) {
                echo implode(' ', $seps)."\n";
            }
            $cnt++;
        }
    }

    private function getMax($k)
    {
        return AH::get($this->max, $k, 0);
    }
    private function setMax($k, $v)
    {
        return $this->max[$k] = $v;
    }
}
