<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;
use Seaf\Exception;

/**
 * モデル
 */
class Model implements HaveHandlerIF,TableDecleationIF
{
    use HaveHandler;
    use TableDecleation;
    use GetSqlBuilder;

    /**
     * @var array
     */
    private $params = [];


    /**
     * @var array
     */
    private $firstParams = [];

    /**
     * @var bool
     */
    private $isNew = true;


    /**
     * @param array 初期値
     */
    public function __construct ($params = [], $isNew = true)
    {
        $schema = self::schema();
        $schema->implementTableScheme($this);

        if ($isNew) {
            $this->onNew();
        }
    }

    /**
     * 新規作成時の処理
     */
    public function onNew()
    {
    }

    public function setParams($params = [])
    {
        if (empty($params)) return $this;

        foreach($params as $k=>$v) {
            $this->__set($k, $v);
        }

        return $this;
    }

    public function getParams( )
    {
        return $this->params;
    }

    /**
     * データを書き込む
     *
     * @param string
     * @param mixed
     */
    public function __set ($name, $value)
    {
        if ($alias = $this->getAlias($name)) {
            $real_name = $alias;
        } else {
            $real_name = $name;
        }

        if (isset($this->columns[$real_name])) {
            $this->params[$real_name] = $value;
        } else {
            /**
             * 無ければ無視
            throw new Exception\Exception ([
                '%s->%sは未定義です',
                get_class($this),
                $name
            ]);
             */
        }
    }

    /**
     * データを取得
     *
     * @param string
     * @return mixed
     */
    public function __get ($name)
    {
        if ($alias = $this->getAlias($name)) {
            $real_name = $alias;
        } else {
            $real_name = $name;
        }

        if (isset($this->columns[$real_name])) {
            return $this->params[$real_name];
        } else {
            throw new Exception\Exception ([
                '%s->%sは未定義です',
                get_class($this),
                $name
            ]);
        }
    }

    /**
     * スタート時のデータを保存する
     */
    public function initFirstParams ( )
    {
        $this->firstParams = $this->params;
    }

    /**
     * データを保存する
     */
    public function save ( )
    {
        if ($this->isNew()) {
            $this->put();
        } else {
            $this->post();
        }
    }

    /**
     * 新規かどうか
     */
    public function isNew ($new = null)
    {
        if ($new === null) {
            return $this->isNew;
        }
        $this->isNew = $new;

        return $this;
    }

    /**
     * 新規保存
     */
    public function put ( )
    {
        $sqlBuilder = $this->getSqlBuilder();
        $sqlBuilder->type('INSERT');
        foreach ($this->params as $k => $v) {
            $sqlBuilder->fields($k);
            $sqlBuilder->values(":".$k);
            $sqlBuilder->bindValue(":$k", $v);
        }
        $sqlBuilder->execute();
        $this->isNew(false);
        $this->initFirstParams();
    }

    /**
     * 更新保存
     */
    public function post ( )
    {
        $sqlBuilder = $this->getSqlBuilder();
        $sqlBuilder->type('UPDATE');
        $sqlBuilder->eq($this->primary_key, $this->getPrimaryKey());

        // 変更があった部分だけ
        $cnt = 0;
        foreach ($this->params as $k => $v)
        {
            if (!isset($this->firstParams[$k]) || $v != $this->firstParams[$k]) {
                $cnt++;
                $sqlBuilder->fields($k);
                $sqlBuilder->values(":$k");
                $sqlBuilder->bindValue(":$k", $this->params[$k]);
            }
        }
        if ($cnt !== 0) {
            $sqlBuilder->execute();
            $this->initFirstParams();
        }
    }

    /**
     * プライマリキーのデータを取得する
     *
     * @return mixed
     */
    public function getPrimaryKey ( )
    {
        return $this->params[$this->primary_key];
    }

    /**
     * 遅延束縛用
     */
    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * モデルのスキーマを取得する
     *
     * @return array
     */
    public static function schema ( )
    {
        static $schemas = [];

        $class = static::who();
        if (isset($schemas[$class])) {
            return $schemas[$class];
        }

        $schema = array();

        return $schemas[$class] = new Schema(static::who());

    }

}
