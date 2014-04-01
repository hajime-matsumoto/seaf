<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Model;

use Seaf\Module\Model\Base;

/**
 * @SeafTable user_login
 */
class UserLogin extends Base
{
    /**
     * ユーザID
     *
     * @SeafPrimary true
     * @SeafDataName uid
     * @SeafDataType integer
     */
    private $uid;

    /**
     * 登録日時
     *
     * @SeafDataName reg_date
     * @SeafDataType timestamp
     */
    private $regDate;

    /**
     * 登録ホスト
     *
     * @SeafDataName reg_host
     * @SeafDataType varchar(100)
     */
    private $regHost;

    /**
     * ログインID
     *
     * @SeafDataName login
     * @SeafDataType varchar(100)
     */
    private $login;

    /**
     * ログインパスワード
     *
     * @SeafDataName passwd
     * @SeafDataType varchar(100)
     */
    private $passwd;

    /**
     * ステータス
     *
     * @SeafDataName status
     * @SeafDataType enum(0,1,2) # 0:有効 1:無効 2:退会
     */
    private $status = 0;
}
