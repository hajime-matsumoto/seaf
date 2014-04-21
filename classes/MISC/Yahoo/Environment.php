<?php

namespace Seaf\MISC\Yahoo;

use Seaf\Net\HTTP;
use Seaf\DOM\HTML;
use Seaf\Base;

class Environment
{
    use Base\ComponentCompositeTrait;

    /**
     * Users
     *
     * @var array
     */
    private $users = [];

    /**
     * API Users
     *
     * @var array
     */
    private $apiUsers = [];

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        // コンポーネントコンテナを設定する
        $this->setComponentContainer('Seaf\MISC\Yahoo\ComponentContainer');
    }

    /**
     * ユーザを追加する
     *
     * @param string
     * @param string
     * @param array
     */
    public function addUser($account, $passwd, $api = [])
    {
        $this->users[$account] = new User($account, $passwd, $api, $this);
    }

    /**
     * APIユーザを追加する
     *
     * @param string
     * @param string
     */
    public function addApiUser($id, $secret)
    {
        $this->apiUsers[$id] = new ApiUser($id, $secret);
    }

    /**
     * APIユーザを取得する
     *
     * @return ApiUser
     */
    public function selectApiUser( )
    {
        return current($this->apiUsers);
    }

    /**
     * ユーザを取得する
     *
     * @return User
     */
    public function selectUser( )
    {
        return current($this->users);
    }

    /**
     * 全ユーザを取得する
     *
     * @return User
     */
    public function getUsers( )
    {
        return $this->users;
    }


    /**
     * コンポーネントコール
     *
     * @param string
     * @param array
     */
    public function __call ($name, $params)
    {
        return $this->componentCall($name, $params);
    }
}
