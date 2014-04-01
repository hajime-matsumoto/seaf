<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;
use Seaf\Data\Container\ArrayContainer;

class Schema
{
    public function __construct ($class)
    {
        $schame = array();
        // モデルのテーブルを取得
        Seaf::ReflectionClass($class)
            ->mapClassAnnotation(
                function($class, $anot) use (&$schema){
                    if (array_key_exists('table', $anot)) {
                        $schema['table'] = $anot['table'][0];
                    }
                }
        );

        // モデルのプロパティスキーマを取得
        Seaf::ReflectionClass($class)
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
                        if (array_key_exists('dataOption', $anot)) {
                            $ret['options'] = $anot['dataOption'];
                        }
                        if (array_key_exists('dataSize', $anot)) {
                            $ret['size'] = $anot['dataSize'][0];
                        }
                        $schema['cols'][$name] = $ret;
                    }
                }
        );

        $this->schema = new ArrayContainer($schema);
    }

    public function implementTableScheme($object)
    {
        $schema = $this->schema;

        if (isset($schema['table'])) {
            $object->setTableName($schema['table']);
        }
        if (isset($schema['cols']) && is_array($schema['cols'])) {
            foreach ($schema['cols'] as $k => $col) {
                // タイプを変換
                $type = $this->convertType($col['type']);
                $object->declearColumn($k, $type);
                if (isset($col['primary']) && $col['primary'] == true) {
                    $object->declearPrimaryKey($k);
                }

                if (isset($col['prop'])) {
                    $object->setAlias($col['prop'], $k);
                }
            }
        }
    }

    private function convertType ($type)
    {
        if (false !== $p = strpos($type, '(')) {
            $type = strtolower(substr($type, 0, $p));
        }
        switch ($type) {
        case 'enum':
            $type = DB::DATA_TYPE_INT;
            break;
        default:
            $type = DB::DATA_TYPE_STR;
            break;
        }

        return $type;
    }

    public function __invoke ()
    {
        return call_user_func_array(
            $this->schema,
            func_get_args()
        );
    }


}
