<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;
use Seaf\Exception;
use Seaf\Util;

/**
 * テーブルスキーマ
 */
class Schema
{
    public $table;
    public $fields = [];
    public $indexes = [];
    public $primary = [];
    public $autoIncrement;

    /**
     * データモデルから完全なスキーマを作成する
     */
    public static function createByModel($class)
    {
        if (!class_exists($class)) {
            throw new Exception\Exception([
                "クラス%sは存在しません",
                $class
            ]);
        }
        $schema = new self();
        $class = Seaf::ReflectionClass($class);

        // クラスファイル
        $file = Seaf::FileSystem($class->getFileName());

        // キャッシュキー
        $cache_key = (string) $file;

        // クラスファイルの更新時刻
        $cache_until = $file->mtime();
        if (Seaf::Cache()->has($cache_key, $cache_until)) {
            return Seaf::Cache()->getCachedData($cache_key);
        }


        // テーブル定義を取得
        $anotation_getter = ['table','primary','index'=>['type'=>'multi']];
        $anot = $class->getClassAnnotation($anotation_getter,'SeafData');

        $schema->table($anot['table']);
        if (isset($anot['primary'])) {
            $schema->primary($anot['primary']);
        }
        if (isset($anot['index'])) {
            $schema->index($anot['index']);
        }

        // フィールド定義を取得
        $anot = $class->getPropAnnotation([
            'name',
            'type',
            'size',
            'default',
            'nullable'=>['type'=>'bool'],
            'option'=>['type'=>'multi']
        ],'SeafData');

        foreach ($anot as $key=>$field) 
        {
            $f = Util\ArrayHelper::container($field);
            $schema->field(
                $f('name',$key),
                $f('type','str'),
                $f('size',null),
                $f('default',null),
                $f('nullable',true),
                $f('option', []),
                $key
            );
        }

        Seaf::Cache()->put($cache_key, 0, $schema);

        return $schema;
    }

    /**
     * テーブル名を追加する
     *
     * @param string
     */
    public function table($name)
    {
        $this->table = $name;
        return $this;
    }

    /**
     * フィールドを追加する
     *
     * @param string
     * @param string
     * @param int
     * @param string
     * @param bool
     */
    public function field(
        $name,
        $type,
        $size = null,
        $default = null,
        $nullable = true,
        $options = [],
        $alias = false
    )
    {
        $this->fields_alias[($alias) ? $alias: $name] = $name;
        $this->fields[$name] = compact('type','size','default', 'nullable', 'options', 'alias');
        return $this;
    }

    /**
     * インデックスを追加する
     *
     * @param string
     * @param string
     * @param bool
     */
    public function index($name, $field = null, $uniq = false)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) {
                if (is_int($k)) {
                    $this->index($v);
                }else{
                    $this->index($k, $v);
                }
            }
            return $this;
        }
        if ($field == null) {
            $field = $name;
            $name = $field."_idx";
        }

        $this->indexes[$name] = compact('field','uniq');
        return $this;
    }

    /**
     * プライマリキーを追加する
     *
     * @param string
     */
    public function primary($field)
    {
        $this->primary = $field;
        return $this;
    }

    /**
     * オートインクリメントを使用する
     *
     * @param string
     */
    public function autoIncrement($flg = true)
    {
        $this->autoIncrement = $flg;
        return $this;
    }
}
