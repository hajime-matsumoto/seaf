<?php

namespace Seaf\MISC\Yahoo;

use Seaf\Net\HTTP;
use Seaf\DOM\HTML;

class API
{
    const AUCTION_API_BASE_URL = "http://auctions.yahooapis.jp/AuctionWebService";
    const AUCTION_API_VERSION  = "V2";
    const SHOP_API_BASE_URL    = "http://shopping.yahooapis.jp/ShoppingWebService";
    const SHOP_API_VERSION     = "V1";

    const DUMMY_USER_AGENT  = 'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20100101 Firefox/12.0';

    /**
     * ログインユーザを取得する
     *
     * @param string
     * @param string
     */
    public function user($account, $passwd)
    {
        $user = new User($account, $passwd, $this);
        return $user;
    }

    /**
     * ユーザ用クライアントを取得
     *
     * @param User
     */
    private function userClient(User $user, &$cookie_file = null)
    {
        // クライアントを生成
        $client = HTTP\Client::factory([
            'agent' => self::DUMMY_USER_AGENT,
            'cookie' => [&$cookie_file, $user->getCurlCookieData( )],
            'followLocation' => true
        ]);

        return $client;
    }
}
