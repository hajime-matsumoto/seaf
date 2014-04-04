<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Model;

use Seaf;
use Seaf\DB;

/**
 * ユーザ仮登録データ
 *
 * @SeafDataTable user_pre
 * @SeafDataPrimary reg_key
 * @SeafDataIndex login
 */
class UserPre extends DB\Model\Base
{
    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * 仮登録キー
     *
     * @SeafDataName reg_key
     * @SeafDataType varchar
     * @SeafDataSize 100
     */
    protected $regKey;

    /**
     * 仮登録日時
     *
     * @SeafDataName reg_time
     * @SeafDataType timestamp
     */
    protected $regTime;

    /**
     * 仮登録ホスト
     *
     * @SeafDataName reg_host
     * @SeafDataType varchar
     * @SeafDataSize 100
     */
    protected $regHost;

    /**
     * ログインID
     *
     * @SeafDataName login
     * @SeafDataType varchar
     * @SeafDataSize 100
     */
    protected $login;

    /**
     * ログインパスワード
     *
     * @SeafDataName passwd
     * @SeafDataType varchar
     * @SeafDataSize 100
     */
    protected $passwd;

    /**
     * ステータス
     *
     * 0:有効 1:無効 2:退会
     *
     * @SeafDataName status
     * @SeafDataType enum
     * @SeafDataOption 0
     * @SeafDataOption 1
     * @SeafDataOption 2
     * @SeafDataDefault 0
     */
    protected $status = 0;

    /**
     * 新規作成時の処理
     */
    public function onCreate ( )
    {
        $this->regDate = time();
        $this->regHost = Seaf::Globals('_SERVER.REMOTE_ADDR', '127.0.0.1');
    }

    /**
     * Reg Keyのセッター
     */
    public function setRegKey($value)
    {
        return sha1($value);
    }

    /**
     * Reg Keyのゲッター
     */
    public function getRegKey( )
    {
        return $this->regKey;
    }

    /**
     * Reg Timeのセッター
     */
    public function setRegTime($value)
    {
        return date('Y-m-d G:i:s', $value);
    }
 
}
