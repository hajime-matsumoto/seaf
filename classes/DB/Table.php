<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;

/**
 * テーブルオブジェクト
 */
class Table implements HaveHandlerIF, TableDecleationIF
{
    use HaveHandler;
    use TableDecleation;

    /**
     * @var string
     */
    private $model_class = false;


    /**
     * コンストラクタ
     */
    public function __construct ($model_class = null)
    {
        if ($model_class !== null) $this->useModel($model_class);
    }

    // ------------------------------------
    // 操作
    // ------------------------------------

    public function select ($sql = null)
    {
        $builder = new SqlBuilder($sql);
        $builder->setHandler($this->getHandler());
        $builder->setModelClass($this->model_class);
        return $builder->type('select')->table($this->table_name);
    }

    public function useModel ($class)
    {
        $this->model_class = $class;

        // スキーマから設定する
        $schema = $class::schema( );
        $schema->implementTableScheme($this);
    }


    /**
     * プライマリキーでデータを取得する
     */
    public function get ($key)
    {
        $result = $this
            ->select()
            ->fields('*')
            ->eq($this->primary_key, $key)
            ->first();
        if ($this->model_class) {
            $model = $this->create($result, false);
            $model->isNew(false);
            $model->initFirstParams();
            return $model;
        }else{
            return $result;
        }
    }


    /**
     * データモデルを取得する
     */
    public function create ($params = [], $isNew = true)
    {
        if ($this->model_class == __NAMESPACE__.'\\Model') {
            $model = new Model( );
            $model->setTableName($this->table_name);
            $model->declearPrimaryKey($this->primary_key);
            $model->declearPrimaryKey($this->primary_key);
            foreach ($this->columns as $k => $v) {
                $model->declearColumn($k, $v['type'], $v['size']);
            }
        } else {
            $model = Seaf::ReflectionClass($this->model_class)->newInstance($params, $isNew);
        }
        $model->setHandler($this->handler);
        $model->setParams($params);
        return $model;
    }
}
