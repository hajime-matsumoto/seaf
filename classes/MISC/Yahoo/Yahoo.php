<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\MISC\Yahoo;

/**
 * Yahooログインユーザ
 */
class Yahoo
{
    /**
     * @var string
     */
    public $account;

    /**
     * @var string
     */
    public $passwd;

    /**
     * コンストラクタ
     *
     * @param string
     * @param string
     */
    public function __construct ($account, $passwd)
    {
        $this->account = $account;
        $this->passwd = $passwd;
    }
}
