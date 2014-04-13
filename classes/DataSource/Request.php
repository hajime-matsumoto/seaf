<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource;

use Seaf;
use Seaf\Base;

/**
 * データソースへのリクエスト
 */
class Request extends Seaf\Request\Request
{
    protected $where_parts;
    protected $handler_name;
    protected $orders;
    protected $limit;
    protected $offset;
    protected $model;
    protected $expires;
    protected $unless;
    protected $fields = [];
    protected $useCache = false;

    protected $options;
    protected $ds;

    public static function who ( )
    {
        return __CLASS__;
    }

    public function clear ( )
    {
        parent::clear();
        $this->where_parts = [];
        $this->orders = [];
        $this->limit = -1;
        $this->offset = -1;
        $this->model = false;
    }

    /**
     * 実行する
     */
    public function execute ( )
    {
        $result = $this->ds->execute($this);
        if ($this->model) {
            $result->model($this->model);
        }
        return $result;
    }

    /**
     * ノーマライズ
     */
    public function normalize ( )
    {
        $this->ds->normalize($this);
        return $this;
    }

    // ---------------------------------
    // セッター

    /**
     * データソースを追加する
     */
    public function ds (DataSource $ds)
    {
        $this->ds = $ds;
        return $this;
    }

    /**
     * データハンドラを指定する
     */
    public function setDataSourceHandlerName ($handler_name)
    {
        $this->handler_name = $handler_name;
        return $this;
    }

    /**
     * 検索クエリを作成する
     */
    public function where ($where)
    {
        $this->where_parts[] = $where;
        return $this;
    }

    /**
     * ソート条件を指定する
     */
    public function order ($key, $asc = true)
    {
        $this->orders[$key] = $asc;
        return $this;
    }

    /**
     * リミットを指定する
     */
    public function limit ($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * オフセットを指定する
     */
    public function offset ($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * オプションの指定
     */
    public function option ($key, $value = null)
    {
        if ($this->recurseCallIfArray($key, __FUNCTION__)) return $this;
        $this->options[$key] = $value;
        return $this;
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
     * キャッシュの設定
     */
    public function cache ($expires = 0, $unless = 0)
    {
        $this->useCache();
        $this->expires = $expires;
        $this->unless = $unless;
        return $this;
    }
    public function useCache($flg = true)
    {
        $this->useCache = $flg;
        return $this;
    }


    /**
     * フィールドの設定
     */
    public function field ($field)
    {
        $this->fields[] = $field;
        return $this;
    }


    // ---------------------------------
    // ゲッター


    /**
     * データハンドラ名を取得
     */
    public function getDataSourceHandlerName ( )
    {
        return $this->handler_name;
    }

    /**
     * 検索クエリを取得する
     */
    public function getWhereParts( )
    {
        if (empty($this->where_parts)) {
            return [];
        }
        return $this->where_parts;
    }

    /**
     * ソート条件を取得する
     */
    public function getOrder ( )
    {
        return $this->orders;
    }

    /**
     * リミットを取得する
     */
    public function getLimit ( )
    {
        return $this->limit;
    }

    /**
     * オフセットを取得する
     */
    public function getOffset ( )
    {
        return $this->offset;
    }

    /**
     * オプションの取得
     */
    public function getOptions ( )
    {
        return $this->options;
    }

    /**
     * モデルスキーマの取得
     */
    public function getSchema( )
    {
        if ($this->model) {
            $class = $this->model;
            return $class::schema();
        }
        return false;
    }

    /**
     * フィールドの取得
     */
    public function getFields ( )
    {
        if (empty($this->fields)) return ['*'];

        return $this->fields;
    }
}
