<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

trait TableDecleation
    {
        /**
         * @var string
         */
        private $table_name;

        /**
         * @var array
         */
        private $columns = [];

        /**
         * @var array
         */
        private $alias = [];

        /**
         * @var string
         */
        private $primary_key;

        /**
         * テーブル名をセットする
         *
         * @param string
         */
        public function setTableName ($name)
        {
            $this->table_name = $name;
        }

        /**
         * テーブル名を取得する
         *
         * @return string
         */
        public function getTableName ( )
        {
            return $this->table_name;
        }

        /**
         * カラムを定義する
         *
         * @param string
         * @param string
         * @param int
         */
        public function declearColumn ($name, $type, $size = null)
        {
            $this->columns[$name] = [
                'type' => $type,
                'size' => $size
            ];
        }

        /**
         * プライマリキーをセットする
         *
         * @param string
         */
        public function declearPrimaryKey ($name)
        {
            $this->primary_key = $name;
        }

        /**
         * カラム定義を取得
         */
        public function getColumnDecleations()
        {
            return $this->columns;
        }

        public function setAlias ($alias, $name)
        {
            $this->alias[$alias] = $name;
        }

        public function getAlias ($alias)
        {
            if (isset($this->alias[$alias])) {
                return $this->alias[$alias];
            }
            return false;
        }
    }
