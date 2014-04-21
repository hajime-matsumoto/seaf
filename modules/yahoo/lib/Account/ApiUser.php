<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Yahoo\Account;

/**
 * Yahoo APIユーザ
 */
class ApiUser
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $secret;

    /**
     * コンストラクタ
     *
     * @param string
     * @param string
     */
    public function __construct ($id, $secret)
    {
        $this->id = $id;
        $this->secret = $secret;
    }
}
