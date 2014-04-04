<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

/**
 * 結果クラス
 */
class Result
{
    const FETCH_MODE_ASSOC  = 'assoc';
    const FETCH_MODE_ARRAY  = 'array';
    const FETCH_MODE_OBJECT = 'object';

    /**
     */
    private $fetch_mode = self::FETCH_MODE_ASSOC;

    /**
     * @var DataSource
     */
    private $ds;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var string
     */
    private $fetch_class = 'Seaf\Data\Container\ArrayContainer';

    /**
     * @var array
     */
    private $recs = [];

    /**
     * @var array
     */
    private $saved = false;

    /**
     * @var string
     */
    private $cache_status = false;

    /**
     * コンストラクタ
     *
     * @param DataSource
     * @param mixed
     * @param Handler
     */
    public function __construct (DataSource $ds, $result, Handler $handler)
    {
        $this->ds      = $ds;
        $this->result  = $result;
        $this->handler = $handler;
    }

    /**
     * ラストインサートIDを取得する
     */
    public function lastInsertId( )
    {
        return $this->ds->lastInsertId($this->result);
    }

    /**
     * エラー判定
     */
    public function isError( )
    {
        return $this->ds->isError($this->result);
    }

    /**
     * エラー取得
     */
    public function getError( )
    {
        return $this->ds->getError($this->result);
    }


    /**
     * 結果レコードを内部に取り込む
     */
    public function save( )
    {
        $this->recs = [];
        while($rec = $this->ds->fetchAssoc($this->result)) {
            $this->recs[] = $rec;
        }
        $this->saved = true;
    }

    /**
     * 結果の全部取得
     */
    public function fetchAll($mode = null)
    {
        $recs = [];
        while ($rec = $this->fetch($mode)) {
            $recs[] = $rec;
        }
        return $recs;
    }

    /**
     * fetch modeの変更
     */
    public function fetchMode($mode)
    {
        $this->fetch_mode = $mode;
    }

    /**
     * 結果取得
     */
    public function fetch($mode = null)
    {
        if ($mode == null) {
            $mode = $this->fetch_mode;
        }
        $method = "fetch".$mode;
        return $this->$method();
    }

    /**
     * 結果を連想配列で取得
     */
    public function fetchAssoc( )
    {
        if (!$this->saved) {
            return $this->ds->fetchAssoc($this->result);
        }else{
            $rec = current($this->recs);
            next($this->recs);
            return $rec;
        }
    }

    /**
     * 結果をオブジェクトで取得
     */
    public function fetchObject( )
    {
        $params = $this->fetchAssoc( );
        if (!$params) return false;

        $object =  $this->createObject($params);
        if ($object instanceof Model\Base) {
            $object->rebaseParams();
        }
        return $object;
    }

    /**
     * 返却用オブジェクトを作成
     */
    private function createObject($params)
    {
        $class = $this->getClass();
        $object = new $class( );
        foreach ($params as $k=>$v) {
            $object->$k = $v;
        }
        return $object;
    }

    public function setClass($class)
    {
        if (class_exists($class)) {
            $this->fetchMode(self::FETCH_MODE_OBJECT);
            $this->fetch_class = $class;
        }
    }

    protected function getClass( )
    {
        return $this->fetch_class;
    }

    public function setCacheStatus($status)
    {
        $this->cache_status = $status;
    }

    public function getCacheStatus( )
    {
        return $this->cache_status;
    }
}
