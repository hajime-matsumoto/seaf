<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;

/**
 * リザルトセット
 */
class Result implements \Iterator
{
    /**
     * @var string
     */
    protected $model_class = false;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var mixed
     */
    private $result;

    /**
     * コンストラクタ
     *
     * @param Handler
     * @param mixed
     */
    public function __construct (Handler $handler, $result)
    {
        $this->handler = $handler;
        $this->result = $result;
    }

    /**
     * モデルクラスを設定する
     *
     * @return string
     */
    public function setModelClass ($class)
    {
        return $this->model_class = $class;
    }

    /**
     * エラー判定
     *
     * @return bool
     */
    public function isError ( )
    {
        return $this->handler->isError($this->result);
    }

    /**
     * エラー取得
     *
     * @return string
     */
    public function getError ( )
    {
        return $this->handler->getError($this->result);
    }

    /**
     * 連想配列でレコードを取得
     *
     * @return array
     */
    public function fetchAssoc ( )
    {
        return $this->handler->fetchAssoc($this->result);
    }

    /**
     * モデルでレコードを取得
     *
     * @return array
     */
    public function fetchModel ( )
    {
        if (!$result = $this->fetchAssoc()) return false;

        $model = Seaf::ReflectionClass($this->model_class)->newInstance();
        $model->setParams($result);
        $model->initFirstParams();
        $model->isNew(false);
        $model->setHandler(Seaf::DB());
        return $model;
    }

    /**
     * 連想配列でレコード取得をすべて取得
     *
     * @return array
     */
    public function fetchAssocAll ( )
    {
        $rows = [];
        while($row = $this->fetchAssoc( ))
        {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * 最初のレコードのカラムを取得する
     */
    public function getCols ($name)
    {
        $rec = $this->fetchAssoc();
        if (func_num_args() > 1) {
            foreach (func_gat_args() as $name) {
                $ret[$name] = $rec[$name];
            }
        } else {
            return $rec[$name];
        }
    }

    // ---------------------------------------------
    // イテレータ
    // ---------------------------------------------

    public function rewind ( )
    {
        $this->key = 0;
    }

    public function valid ( )
    {
        $this->current = $this->fetchAssoc();
        return $this->current ? true: false;
    }

    public function current ( )
    {
        return $this->current;
    }

    public function next ( )
    {
        $this->key++;
    }

    public function key ( )
    {
        return $this->key;
    }

    // ---------------------------------------------
    // Cache
    // ---------------------------------------------

    public function createCacheableResult ( )
    {
        return new CacheableResult($this);
    }
}
