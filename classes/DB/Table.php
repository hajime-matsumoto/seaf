<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

class Table
{
    /**
     * @var string
     */
    private $name;

     /**
      * @var Handler
      */
    private $handler;

     /**
      * @var DataSource
      */
    private $ds = false;

    /**
     * コンストラクタ
     *
     * @param string
     * @param Handler
     */
    public function __construct ($name, Handler $handler)
    {
        $this->name = $name;
        $this->handler = $handler;
    }

    /**
     * データソースを設定する
     *
     * @param DataSource
     */
    public function setDataSource (DataSource $ds)
    {
        $this->ds = $ds;
    }

    /**
     * リクエストを作成する
     *
     * @param string
     */
    public function newRequest ($proc_type)
    {
        if ($this->ds) {
            $req = $this->ds->newRequest($proc_type);
        }else{
            $req = $this->handler->newRequest($proc_type);
        }
        $req->setTargetTable($this->name);
        return $req;
    }

    /**
     * インサートリクエストを作成する
     */
    public function insert ( )
    {
        return $this->newRequest('INSERT');
    }

    /**
     * 更新リクエストを作成する
     */
    public function update ( )
    {
        return $this->newRequest('UPDATE');
    }

    /**
     * 取得リクエストを作成する
     */
    public function find ( )
    {
        return $this->newRequest('FIND');
    }

    /**
     * 削除リクエストを作成する
     */
    public function delete ( )
    {
        return $this->newRequest('DELETE');
    }


    /**
     * クエリリクエストを作成する
     */
    public function query ( )
    {
        return $this->newRequest('QUERY');
    }
}
