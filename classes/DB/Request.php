<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

/**
 * DBへのリクエストを管理する
 */
abstract class Request
{
    /**
     * 処理タイプ
     *
     * @var string
     */
    protected $type;

    /**
     * 処理ターゲット
     *
     * @var string
     */
    private $target;

    /**
     * 処理ターゲットテーブル
     *
     * @var string
     */
    private $targetTable;

    /**
     * 処理ハンドラ
     *
     * @var Handler
     */
    private $handler;

    /**
     * 処理内容
     *
     * @var string
     */
    private $body;

    /**
     * 処理結果キャッシュの有効期限
     *
     * @var int
     */
    private $expires = 0;

    /**
     * 処理結果キャッシュの使用フラグ
     *
     * @var bool
     */
    private $isAllowCache = false;

    /**
     * 処理パラメータ
     *
     * @var array
     */
    private $params = [];

    /**
     * 処理オプション
     *
     * @var array
     */
    private $options = [];


    /**
     * スキーマ
     *
     * @var Schema
     */
    private $schema = null;

    /**
     * モデル
     *
     * @var string
     */
    private $model = false;

    /**
     * リクエストファクトリ
     */
    public static function factory($type)
    {
        $type = ucfirst(strtolower($type));
        $class = __NAMESPACE__.'\\Request\\'.$type.'Request';
        $req = new $class( );
        return $req;
    }


    /**
     * 処理ハンドラをセットする
     *
     * @param Handler
     */
    public function setHandler(Handler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * 処理タイプをセットする
     *
     * @param string
     */
    public function setType($type)
    {
        $this->type = strtoupper($type);
    }

    /**
     * 処理タイプを取得する
     *
     * @param string
     */
    public function getType( )
    {
        return $this->type;
    }


    /**
     * ターゲットをセットする
     *
     * @param string
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * ターゲットを取得する
     *
     * @return string
     */
    public function getTarget( )
    {
        return $this->target;
    }

    /**
     * ターゲットをセットする
     *
     * @param string
     */
    public function setTargetTable($targetTable)
    {
        $this->targetTable = $targetTable;
    }

    /**
     * ターゲットを取得する
     *
     * @return string
     */
    public function getTargetTable( )
    {
        return $this->targetTable;
    }

    /**
     * リクエスト本文を取得する
     *
     * @return string
     */
    public function getBody ( )
    {
        return $this->body;
    }

    /**
     * リクエストパラメタを取得する
     *
     * @return string
     */
    public function getParams ( )
    {
        return $this->params;
    }

    /**
     * リクエストオプションを取得する
     *
     * @return string
     */
    public function getOptions ( )
    {
        return $this->options;
    }


    /**
     * キャッシュの有効期間を取り出す
     *
     * @return int
     */
    public function getCacheExpires ( )
    {
        return $this->expires;
    }

    /**
     * キャッシュを使うか判定
     */
    public function isAllowCache( )
    {
        return $this->isAllowCache;
    }

    /**
     * スキーマを取得する
     *
     * @param Schema
     */
    public function getSchema( )
    {
        if ($this->schema == false && $this->model) {
            $class = $this->model;
            return $class::schema();
        }
        return $this->schema;
    }

    /**
     * モデルを設定
     *
     * @param string
     */
    public function setModel($model)
    {
        return $this->model = $model;
    }

    //--------------------------------------
    //
    //--------------------------------------

    /**
     * スキーマを設定する
     *
     * @param Schema
     */
    public function schema (Schema $schema)
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * キャッシュタイムを設定する
     */
    public function cache ($expires)
    {
        $this->useCache();
        $this->expires = $expires;
        return $this;
    }

    /**
     * キャッシュを使用許可する
     */
    public function useCache ($flag = true)
    {
        $this->isAllowCache = $flag;
    }

    /**
     * リクエスト本文を記述する
     */
    public function body ($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * パラメタを設定する
     */
    public function param ($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k=>$v) $this->param($k, $v);
            return $this;
        }
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * オプションを設定する
     */
    public function option ($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k=>$v) $this->option($k, $v);
            return $this;
        }
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * リクエストを実行する
     */
    public function execute ( )
    {
        $result =  $this->handler->execute($this);
        $result->setClass($this->model);
        return $result;
    }

    /**
     * リクエストのハッシュキーを取得
     */
    public function getHash( )
    {
        return sha1(
            $this->target .
            $this->body .
            print_r($this->params, true)
        );
    }
}
