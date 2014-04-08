<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Net\HTTP;

use Seaf;

/**
 * curlベースのHTTPクライアント
 *
 * - Cookie
 * - Proxy
 */
class Client
{
    /**
     * Proxy情報
     *
     * @var string
     */
    private $proxy;

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->init();
    }

    /**
     * イニシャライザ
     */
    public function init ( )
    {
        $this->curl = curl_init( );

        // 結果を文字列で取得する
        $this->setopt(CURLOPT_RETURNTRANSFER,1);
    }

    /**
     * オプションのセット
     */
    public function setopt ($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k=>$v) {
                $this->setopt($k, $v);
            }
            return $thie;
        }
        curl_setopt($this->curl, $key, $value);
        return $this;
    }

    /**
     * クッキーを使う
     *
     * @param string ファイル名
     * @param string ファイル名
     */
    public function cookie($input = null, $output = null)
    {
        if ($input == null) {
            $input = '/dev/null';
        }
        if ($output == null) {
            $output = '/tmp/cookie-'.sha1(uniqid(mt_rand(), true));
        }

        // クッキーの管理
        $this->setopt([
            CURLOPT_COOKIEJAR => $input,
            CURLOPT_COOKIEFILE => $output
        ]);
        return $this;
    }


    /**
     * プロクシをセットする
     *
     * @param string host
     * @param int $port
     * @param string $user
     * @param string $pass
     * @return Client
     */
    public function proxy ( $proxy, $port = 8080, $user = null, $pass =null)
    {
        $this->proxy = $proxy;
        $this->setopt([
            CURLOPT_HTTPPROXYTUNNEL => 1,
            CURLOPT_PROXY => $proxy,
            CURLOPT_PROXYPORT => $port
        ]);
        if ($user != null) {
            $this->setopt($curl, CURLOPT_PROXYUSERPWD, "$user:$pass");
        }

        return $this;
    }

    /**
     * Locationヘッダを追跡する
     */
    public function followLocation ($flg = true)
    {
        $this->setopt(CURLOPT_FOLLOWLOCATION, $flg);
        return $this;
    }

    /**
     * POSTリクエスト
     */
    public function post ($url, $data = array())
    {
        $this->setopt([
            CURLOPT_URL => $url,
            CURLOPT_POST => true
            CURLOPT_POSTFIELDS => $data
        ])
        return $this->submit( );
    }

    /**
     * GETリクエスト
     */
    public function get ($url, $data = array())
    {
        $url = !empty($data) ? $url.'?'.http_build_query($data): $url;

        $this->setopt([
            CURLOPT_URL=>$url
        ])
        return $this->submit( );
    }



    /**
     * リクエストを送信する
     */
    public function submit ( )
    {
        $res = curl_exec($this->curl);
        $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        if (preg_match("/^(404|403|500)$/", $status)) {
            return false;
        }
        return $res;
    }
}
