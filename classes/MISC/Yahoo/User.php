<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\MISC\Yahoo;

use Seaf\Net\HTTP;
use Seaf\DOM\HTML;
use Seaf\Base;

/**
 * Yahooログインユーザ
 */
class User
{
    use Base\SeafAccessTrait;
    use Base\CacheTrait;

    const LOGIN_URL = "https://login.yahoo.co.jp/config/login?.lg=jp&.intl=jp&.src=auc&.done=http://auctions.yahoo.co.jp/jp";
    const DUMMY_USER_AGENT  = 'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20100101 Firefox/12.0';

    /**
     * @var string
     */
    public $account;

    /**
     * @var string
     */
    public $passwd;

    /**
     * @var API
     */
    private $api;

    /**
     * @var string
     */
    private $curl_cookie;

    /**
     * @var string
     */
    private $cookie_file;

    /**
     * コンストラクタ
     *
     * @param string
     * @param string
     */
    public function __construct ($account, $passwd, API $api)
    {
        $this->account = $account;
        $this->passwd = $passwd;
        $this->api = $api;
    }

    /**
     * ユーザ用クライアントを取得
     *
     * @return HTTP\Client
     */
    private function client( )
    {
        // クライアントを生成
        $client = HTTP\Client::factory([
            'agent' => self::DUMMY_USER_AGENT,
            'cookie' => [&$this->cookie_file, $this->getCurlCookieData( )],
            'followLocation' => true
        ]);
        return $client;
    }


    /**
     * ログイン状態を取得
     *
     * @return bool
     */
    public function isLogin ( )
    {
        // ログインのチェック
        $res = $this->client( )->get('http://auctions.yahoo.co.jp/jp');
        $html = mb_convert_encoding($res, 'utf8', 'eucjp');
        return (bool) preg_match('/ようこそ、\<strong\>(.+)\<\/strong\> さん/', $html, $m);
    }

    /**
     * ユーザをログインさせる
     *
     * @return bool
     */
    public function login( )
    {
        // クライアントを取得
        $client = $this->client();
        $client->get(self::LOGIN_URL);
        sleep(1); // Yahooに人のアクセスだと思わせるハック?

        $res = $client->init()->get(self::LOGIN_URL);

        // 取得したソースから秘密のキーワードを抜き出す
        if (preg_match('/\("\.albatross"\)\[0\]\.value = "(.{56})"/i', $res, $m)) {
            $albatross = $m[1];
        }

        // スクレイピングしてLoginURLとパラメタを取得する
        $html = HTML\Parser::parse($res);
        $form = $html->find('form[id=login_form]', 0);
        $login_url = $form->action;

        // LoginFormのinputを全取得
        foreach ($form->find('input') as $input) {
            if ($input->type == 'submit') continue;
            $params[$input->name] = $input->value;
        }
        // パラメタを上書き
        $params = array_merge($params, [
            '.albatross'  => $albatross, // 秘密のキーワードを上書き
            '.persistent' => 'y', // ログイン状態を保存
            'login'       => $this->account,
            'passwd'      => $this->passwd
        ]);

        sleep(3); // Yahooに人のアクセスだと思わせるハック?

        // ログインアクセス
        $res = $client->init( )->post($login_url, $params);

        // window.location.replace があればログイン成功
        if (preg_match('/window\.location\.replace/', $res)) {
            // クッキーを保存する
            $this->saveCurlCookieData(file_get_contents($this->cookie_file));
            return true;
        }

        // 失敗
        return false;
    }


    /**
     * クッキー情報を取得する
     *
     * @return string
     */
    public function getCurlCookieData ( )
    {
        if (empty($this->curl_cookie)) {
            $this->getCache($this->account);
        }else{
            return $this->curl_cookie;
        }
    }

    /**
     * クッキー情報を保存する
     *
     * @param string
     */
    public function saveCurlCookieData ($data)
    {
        $this->saveCache($this->account, $data);
        $this->curl_cookie = $data;
    }
}
