<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Model;

use Seaf;
use Seaf\Exception;
use Seaf\Pattern;
use Seaf\Util\ArrayHelper;

/**
 * モデルのベースクラス
 */
abstract class Model
{
    use ModelTrait;

    /**
     * transactionHandler
     */
    protected $transactionHandler;

    /**
     * 新規
     *
     * @param bool
     */
    private $isNew;

    /**
     * コンストラクタ
     */
    public function __construct ($isNew = true)
    {
        if ($isNew == true) {
            $this->onCreate( );
        }
        $this->isNew = $isNew;
    }


    /**
     * モデルのスキーマを取得する
     *
     * @return array
     */
    public static function schema ( )
    {
        static $schema = array();
        if (!empty($schema)) return $schema;

        // モデルのテーブルを取得
        Seaf::ReflectionClass(static::who())
            ->mapClassAnnotation(
                function($class, $anot) use (&$schema){
                    if (array_key_exists('table', $anot)) {
                        $schema['table'] = $anot['table'][0];
                    }
                }
        );

        // モデルのプロパティスキーマを取得
        Seaf::ReflectionClass(static::who())
            ->mapPropAnnotation(
                function($prop, $anot) use(&$schema) {

                    if (array_key_exists('dataType' ,$anot)) {
                        $type = $anot['dataType'][0];
                        if (false !== $p = strpos($type,'#')) {
                            $type = trim(substr($type,0,$p));
                        }
                        $name = array_key_exists('dataName', $anot) ?
                            $anot['dataName'][0]:
                            $name=$prop->getName();
                        $desc = $anot['desc'];


                        $ret = compact('type','name','desc');
                        $ret['prop'] = $prop->getName();

                        if (array_key_exists('dataDefault', $anot)) {
                            $ret['default'] = $anot['dataDefault'][0];
                        }
                        if (array_key_exists('dataPrimary', $anot)) {
                            $ret['primary'] = true;
                        }
                        $schema['cols'][$name] = $ret;
                    }
                }
        );
        return $schema;
    }

    /**
     * 説明を作成する
     */
    public static function explain ( )
    {
        $schema = static::schema( );
        $w = Seaf::System()->getClosure('printfn');
        $g = ArrayHelper::getClosure('get');

        $w("\n-- Table -------------\n");
        $w("Table名: %s", $schema['table']);
        $w("\n-- Columns -----------\n");

        $t = Seaf::Table( );
        $t->add('Name', 'Type', 'Description', 'Default', 'isPrimary');
        foreach ($schema['cols'] as $k=>$col) {
            $t->add(
                $k,
                $g($col,'type'),
                $g($col,'desc'),
                $g($col,'default','NULL'),
                $g($col,'primary', '')
            );
        }
        $t->display();
    }

    /**
     * モデルを作成する
     */
    public static function create ( )
    {
        return Seaf::ReflectionClass(static::who())->newInstance();
    }

    /**
     * モデルが新規か否か
     *
     * @return bool
     */
    public function isNew ( )
    {
        return $this->isNew;
    }

    /**
     * モデルを保存する
     */
    public function save ( )
    {
        // 新しいモデルでなければアップデートする
        if (!$this->isNew( )) {
            return $this->update ( );
        }
        $this->getTransactionHandler( )->put($this);
    }

    /**
     * モデルを更新する
     */
    public function update ( )
    {
        $this->getTransactionHandler( )->post($this);
    }

    /**
     * モデルを削除する
     */
    public function del ( )
    {
        $this->getTransactionHandler( )->del($this);
    }

    /**
     * データを取得する
     */
    public function get ($key)
    {
        return $this->{$this->getPropName($key)};
    }

    /**
     * データ名からプロパティ名を取得する
     *
     * @param string データ名
     * @return string プロパティ名
     */
    private function getPropName($name)
    {
        $g = ArrayHelper::getClosure('get');
        return $g(static::schema(), 'cols.'.$name.'.prop');
    }

    /**
     * トランザクションハンドラを取得する
     * 設定されてなければシングルトンのトランザクションハンドラを返す
     */
    public function getTransactionHandler ( )
    {
        if ($this->transactionHandler) {
            return $this->transactionHandler;
        }

        return TransactionHandler::singleton( );
    }

    /**
     * トランザクションハンドラを設定する
     */
    public function setTransactionHandler ($transactionHandler)
    {
        return $this->transactionHandler = $transactionHandler;
    }


    /**
     * セッター
     */
    public function __call ($name, $params)
    {
        $g = ArrayHelper::getClosure('get');

        $schema = static::schema();
        if (false == $col = $g($schema, "cols.".$name, false)) {
            static::explain();
            throw new Exception\Exception(array(
                '%sは存在しないフィールドです',
                $name
            ));
        }
        $this->{$col['prop']} = $params[0];
    }
}
