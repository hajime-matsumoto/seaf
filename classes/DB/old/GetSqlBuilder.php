<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf\Exception;

/**
 * DBハンドラを所有する
 */
trait GetSqlBuilder
    {
        /**
         * SqlBuilderを取得する
         *
         * @param SqlBuilder
         */
        public function getSqlBuilder ($sql = null)
        {
            $builder = new SqlBuilder($sql);

            // ハンドラを継承させる
            if ($this instanceof HaveHandlerIF && $this->haveHandler()) {
                $builder->setHandler($this->getHandler());
            }

            // デクリエーションを継承させる
            if ($this instanceof TableDecleationIF) {
                $builder->table($this->getTableName());
                foreach($this->getColumnDecleations() as $k=>$v) {
                    $builder->declearColumn(":$k", $v['type']);
                }
            }
            return $builder;
        }
    }
