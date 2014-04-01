<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

interface TableDecleationIF
{
    /**
     * テーブル名をセットする
     *
     * @param string
     */
    public function setTableName ($name);

    /**
     * テーブル名を取得する
     *
     * @return string
     */
    public function getTableName ( );

    /**
     * カラムを定義する
     *
     * @param string
     * @param string
     */
    public function declearColumn ($name, $type, $size = null);

    /**
     * プライマリキーをセットする
     *
     * @param string
     */
    public function declearPrimaryKey ($name);

    /**
     * カラム定義を取得
     */
    public function getColumnDecleations();
}
