<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource;

use Seaf;
use Seaf\Base;

/**
 * データソーステーブル
 */
class Table
{
    private $table_name, $ds, $model;

    /**
     * コンストラクタ
     *
     * @param string
     */
    public function __construct ($table_name, DataSource $ds)
    {
        $this->table_name = $table_name;
        $this->ds = $ds;
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

    /**
     * リクエストを作成
     *
     * @return Request
     */
    public function newRequest ($method = false)
    {
        $req = Request::Factory( )
            ->path($this->table_name)
            ->model($this->model)
            ->ds($this->ds);
        if ($method != false) $req->method($method);
        return $req;
    }

    /**
     * 検索用のリクエストを作成
     *
     * @return Request
     */
    public function find ( )
    {
        return $this->newRequest('FIND');
    }

    /**
     * 更新用のリクエストを作成
     *
     * @return Request
     */
    public function update ( )
    {
        return $this->newRequest('UPDATE');
    }

    /**
     * データ挿入用のリクエストを作成
     *
     * @return Request
     */
    public function insert ( )
    {
        return $this->newRequest('INSERT');
    }
}
