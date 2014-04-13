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
    public $last_info;

    private $cookie_file_path;

    private $default;

    /**
     * Proxy情報
     *
     * @var string
     */
    private $proxy;

    /**
     * ファクトリ
     *
     * @param array 
     */
    public static function factory ($default = [])
    {
        $client = new Client($default);
        $client->setDefault($default);
        return $client;
    }

    private function setDefault ($default = [])
    {
        $this->default = $default;
    }

    /**
     * コンストラクタ
     */
    public function __construct ($default = [])
    {
        $this->setDefault($default);
        $this->init();
    }

    /**
     * イニシャライザ
     */
    public function init ( )
    {
        $this->curl = curl_init( );

        // デフォルトを処理する
        if (!empty($this->default)) {
            foreach ($this->default as $method => $params)
            {
                if (is_array($params)) {
                    call_user_func_array([$this, $method], $params);
                }else{
                    call_user_func([$this, $method], $params);
                }
            }
        }

        // 結果を文字列で取得する
        $this->setopt(CURLOPT_RETURNTRANSFER,1);
        $this->setopt(CURLOPT_TIMEOUT,2);

        $this->setopt(CURLOPT_HTTPHEADER, [
            'Accept: */*',
            'Connection: Keep-Alive'
        ]);
        $this->setopt(CURLOPT_HEADER, 0);

        $this->setopt(CURLOPT_SSL_VERIFYPEER, false);
        $this->setopt(CURLOPT_SSL_VERIFYHOST, 0);
        return $this;
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
            return $this;
        }
        curl_setopt($this->curl, $key, $value);
        return $this;
    }

    /**
     * タイムアウト
     *
     * @param int
     */
    public function timeout($int = 2)
    {
        $this->setopt(CURLOPT_TIMEOUT, $int);
        return $this;
    }

    /**
     * ユーザエージェント
     *
     * @param string
     */
    public function agent ($agent)
    {
        $this->setopt(CURLOPT_USERAGENT, $agent);
        return $this;
    }

    /**
     * クッキーを使う
     *
     * @param string ファイル名
     * @param string 復元用のクッキーデータ
     */
    public function cookie(&$cookie = null, $data = null)
    {
        if ($cookie == null) {
            $cookie = '/tmp/cookie-'.sha1(uniqid(mt_rand(), true));
            touch($cookie);
            // デストラクタ用にプロパティにしておく
            $this->cookie_file_path = $cookie;
        }
        if ($data != null) {
            file_put_contents($cookie, $data);
        }

        $this->setopt(CURLOPT_COOKIEFILE, $cookie);
        $this->setopt(CURLOPT_COOKIEJAR, $cookie);

        return $this;
    }

    public function __destruct( )
    {
        if (file_exists($this->cookie_file_path)) {
            unlink($this->cookie_file_path);
        }
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
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data)
        ]);
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
        ]);
        $res = $this->submit( );
        return $res;
    }



    /**
     * リクエストを送信する
     */
    public function submit ( )
    {
        $res = curl_exec($this->curl);
        $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        $this->last_info = curl_getinfo($this->curl);
        if (preg_match("/^(404|403|500)$/", $status)) {
            return false;
        }
        curl_close($this->curl);
        return $res;
    }
}
