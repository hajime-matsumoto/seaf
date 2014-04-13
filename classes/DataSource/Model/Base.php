<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource\Model;

use Seaf;
use Seaf\Validator\Validator;
use Seaf\DataSource;
use Seaf\Exception;

/**
 * データベースモデルのベースクラス
 */
class Base
{
    private $baseParams = [];
    private $isNewFlag  = true;

    /**
     * 遅延束縛
     *
     * @return string
     */
    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * スキーマを取得
     *
     * @return Schema
     */
    public static function schema ( )
    {
        return DataSource\Schema::createByModel(static::who());
    }

    /**
     * モデルを新規作成する
     *
     * @return Base
     */
    public static function create ($params = null)
    {
        $class = static::who( );
        $model = new $class($params);
        $model->onCreate( );
        return $model;
    }

    /**
     * 新規作成時に呼ばれる
     */
    protected function onCreate ( )
    {
    }

    /**
     * モデルを配列に変換する
     */
    public function toArray( )
    {
        $params = [];
        // パラメタを取得する
        foreach (self::schema( )->fields_alias as $k=>$v) {
            $params[$v] = $this->$k;
        }
        return $params;
    }

    /**
     * モデルをプライマリキーで取得する
     *
     * @return Base
     */
    public static function getOne ($pkey, $expire = 0)
    {
        if (empty($pkey)) return false;
        $table = static::table( );
        return $table->find( )
            ->where([static::schema()->primary => $pkey])
            ->cache($expire)
            ->execute()->fetch( );
    }


    /**
     * 初期値を設定
     *
     */
    public function __construct ($params = null)
    {
        if (is_array($params)) {
            foreach ($params as $k=>$v) {
                $this->set($k, $v);
            }
        }
    }

    /**
     * モデルにセッターをつける
     *
     * @param string
     * @param string
     */
    public function set ($name, $value = null)
    {
        if (is_array($name)) {
            foreach($name as $k=>$v) $this->set($k,$v);
            return;
        }

        $prop_name =  $this->validFieldName($name, $field_info, false);
        if ($prop_name == false) return;

        // セッターメソッドが存在すればコールする
        if (method_exists($this, $method = 'set'.ucfirst($prop_name))) {
            $this->$prop_name = $this->$method($value);
        } else {
            $this->$prop_name = $value;
        }
    }

    public function __set ($name, $value) 
    {
        $this->set($name, $value);
    }

    /**
     * モデルにゲッターをつける
     *
     * @param string
     * @return string
     */
    public function get ($name)
    {
        $prop_name =  $this->validFieldName($name);

        // ゲッターメソッドが存在すればコールする
        if (method_exists($this, $method = 'get'.ucfirst($prop_name))) {
            return $this->$method( );
        } else {
            return $this->$prop_name;
        }
    }

    public function __get ($name) {
        return $this->get($name);
    }
    /**
     * アトリビュート名を取得する
     */
    private function validFieldName($name, &$field_info = null, $strict = true)
    {
        $schema = self::schema( );

        // エイリアスを処理してフィールド名へ変更
        if (isset($schema->fields_alias[$name])) {
            $field_name = $schema->fields_alias[$name];
        }else{
            $field_name = $name;
        }

        // スキーマでの存在確認
        if (!isset($schema->fields[$field_name])) {
            if ($strict) {
                throw new Exception\Exception([
                    "%sに%sは存在しません",
                    get_class($this),
                    $name
                ]);
            }else {
                return false;
            }
        }
        $field_info = $schema->fields[$field_name];
        $prop_name = $field_info['alias'];

        return $prop_name;
    }

    /**
     * 現在のデータ状態を初期値として保存する
     */
    public function rebaseParams() 
    {
        $this->baseParams = [];

        // リベースされたら新規フラグを落とす
        $this->isNewFlag = false;

        foreach (self::schema( )->fields_alias as $k=>$v) {
            $this->baseParams[$k] = $this->$k;
        }
    }

    /**
     * 変更があったデータのみを取得する
     *
     * @return array
     */
    public function modifiedParams() 
    {
        $ret = [];
        foreach (self::schema( )->fields_alias as $k=>$v) {
            if (!isset($this->baseParams[$k]) || $this->baseParams[$k] !== $this->$k) {
                $ret[$v] = $this->$k;
            }
        }
        return $ret;
    }

    /**
     * モデルを保存する
     */
    public function save ( )
    {
        if ($this->isNew()) {
            $this->put( );
        } else {
            $this->post( );
        }
    }

    /**
     * モデルが新規か判定
     */
    public function isNew ( )
    {
        return $this->isNewFlag;
    }

    /**
     * モデルを新規保存する
     */
    protected function put ( )
    {
        $params = [];
        // パラメタを取得する
        foreach (self::schema( )->fields_alias as $k=>$v) {
            $params[$v] = $this->$k;
        }
        $this->rebaseParams();

        // オートインクリメント使用時は
        // プライマリキーをインサートしない。
        if (self::schema()->autoIncrement) {
            unset($params[self::schema()->primary]);
        }

        // データベースへ保存
        $result = self::table()
            ->insert( )
            ->param($params)
            ->execute();

        // ラストインサートIDを取得する
        if ($id = $result->lastInsertId()) {
            $this->set(static::schema()->primary, $id);
        }
    }

    /**
     * モデルを更新保存する
     */
    protected function post ( )
    {
        $params = [];
        // パラメタを取得する
        foreach ($this->modifiedParams() as $k=>$v) {
            $params[$k] = $this->$k;
        }

        if (empty($params)) { // 更新対象がなければ更新しない
            return false;
        }

        // データベースへ保存
        $primary = self::schema()->primary;

        return static::table( )
            ->update( )
            ->param($params)
            ->where([
                $primary => $this->get($primary)
            ])
            ->execute();
    }

    /**
     * アクティブレコード風
     */
    public static function select ( )
    {
        return static::table( )->find( );
    }

    public static function table ( )
    {
        return Seaf::DataSource()->getTable(static::schema()->table)->model(static::who());
    }

    /**
     * バリデーション
     */
    public function getValidator( )
    {
        $validator = new Validator( );
        $anot = Seaf::ReflectionClass(static::who())->getPropAnnotation(
            ['type'=>['type'=>'multi'],'message'], 'SeafValid'
        );
        foreach ($anot as $k=>$v) {
            foreach ($v['type'] as $type) {
                $value = [];

                if (($p=strpos($type, ' '))==false) {
                    $value = [];
                }else{
                    $part = substr($type, 0, $p);
                    parse_str(trim(substr($type,$p)), $value);
                    $type = $part;
                }
                if (isset($v['message'])) {
                    $value['message'] = $v['message'];
                }
                $validator->addValidation($k, $type, $value);
            }
        }
        $anot = Seaf::ReflectionClass(static::who())->getMethodAnnotation(
            ['function', 'target', 'message'], 'SeafValid'
        );
        foreach ($anot as $k=>$v) {
            $validator->addValidation($v['target'], 'callback', ['method'=>[$this,$k]]+$v);
        }
        return $validator;
    }

    public function validate(&$errors = null)
    {
        $validator = $this->getValidator( );
        $map = array_flip(static::schema()->fields_alias);
        $all_errors = [];
        foreach ($this->modifiedParams() as $k=>$v) {
            $field = $map[$k];
            if (!$validator->validate($field, $v, $errors)) {
                // キーを差し替える
                $all_errors[$k] = current($errors);
            }
        }
        $errors = $all_errors;
        return empty($errors) ? true: false;
    }

    public function getPrimaryKey( )
    {
        return $this->get(static::schema()->primary);
    }
}
