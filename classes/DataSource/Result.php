<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource;

use Seaf;
use Seaf\Base;


/**
 * データソースのレスポンス
 */
class Result extends Seaf\Response\Response
{
    private $raw;
    private $dsh;
    private $log;
    private $model;
    private $isError = false;
    private $error = '';


    /**
     * コンストラクタ
     *
     * @param mixed
     * @param DataSourceHandler
     * @param array $req_log
     */
    public function __construct ($result_raw, DataSourceHandler $handler, $isError, $error, $log = [])
    {
        parent::__construct( );
        $this->raw = $result_raw;
        $this->dsh = $handler;
        $this->log = $log;
        $this->isError = $isError;
        $this->error = $error;
    }

    /**
     * エラー判定
     */
    public function isError ()
    {
        return $this->isError;
    }

    /**
     * エラー取得
     */
    public function getError ()
    {
        return $this->error;
    }

    /**
     * ログ取得
     */
    public function getLog ()
    {
        return $this->log;
    }

    /**
     * Fetch
     */
    public function fetch ( )
    {
        $data = $this->_fetch();

        if (!$data) return false;

        if ($this->model) {
            $class = $this->model;
            $model = new $class($data);
            $model->rebaseParams();
            return $model;
        }
        return $data;
    }

    /**
     * Fetch
     */
    public function fetchAll ($key = null)
    {
        $list = [];
        while($res = $this->fetch()) {
            if ($key == null) {
                $list[] = $res;
            }else{
                $list[($res->$key)] = $res;
            }
        }
        return $list;
    }

    private function _fetch ( )
    {
        return $this->dsh->fetch($this->raw);
    }

    /**
     * ラストインサートＩＤを取得する
     */
    public function lastInsertId ( )
    {
        return $this['lastInsertId'];
    }

    /**
     * モデルを結びつける
     *
     * @param string
     */
    public function model ($model_class)
    {
        $this->model = $model_class;
        return $this;
    }
}
