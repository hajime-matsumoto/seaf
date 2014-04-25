<?php // vim: set ft=php ts=4 sts=4 sw=4 et: 
namespace Seaf\Message;

class Translator
{
    private $locale = 'ja';
    private $defaultLocale = 'en';
    private $cache;
    private $messageDir;
    private $useCache = false;

    /**
     * キャッシュを使う
     */
    public function useCache($flg = true)
    {
        $this->useCache = $flg;
    }

    /**
     * キャッシュハンドラをセットする
     *
     * @param Cache\CacheHandler
     */
    public function setCacheHandler($cache)
    {
        $this->cache = $cache;
    }

    /**
     * 言語ファイルディレクトリを設定
     *
     * @param string
     */
    public function setMessageDir($dir)
    {
        $this->messageDir = $dir;
    }

    /**
     * 現在の言語を設定する
     *
     * @param string
     */
    public function setLocale($code)
    {
        $this->locale = $code;
    }

    /**
     * デフォルトの言語を設定する
     *
     * @param string
     */
    public function setDefaultLocale ($code)
    {
        $this->defaultLocale = $code;
    }

    /**
     * キャッシュハンドラを取得する
     *
     * @return Cache\CacheHandler
     */
    public function getCacheHandler ( )
    {
        return $this->cache;
    }

    /**
     * 現在の言語を取得する
     *
     * @return string
     */
    public function getLocale( )
    {
        return $this->locale;
    }

    /**
     * デフォルトの言語を取得する
     *
     * @return string
     */
    public function getDefaultLocale( )
    {
        return $this->defaultLocale;
    }

    /**
     * 言語ファイルディレクトリを取得
     *
     * @return string
     */
    public function getMessageDir( )
    {
        return $this->messageDir;
    }

    /**
     * コードに紐づくメッセージを取得する
     *
     * @param string
     * @return string
     */
    public function translate ($code)
    {
        if (!$data = $this->find($code)) {
            return '[['.$code.']]';
        }
        return $data;
    }

    

    /**
     * コードに紐づくメッセージを検索する
     *
     * @param string
     * @return mixed
     */
    public function find ($code)
    {
        if ($this->getCurrentContainer( )->hasTranslation($code)) {
            return $this->getCurrentContainer( )->getTranslation($code);
        }

        if ($this->getDefaultContainer( )->hasTranslation($code)) {
            return $this->getDefaultContainer( )->getTranslation($code);
        }

        return false;
    }

    /**
     * カレントメッセージコンテナを取得する
     *
     * @return MessageContainer
     */
    protected function getCurrentContainer ( )
    {
        return $this->getMessageContainer($this->getLocale());
    }

    /**
     * デフォルトメッセージコンテナを取得する
     *
     * @return MessageContainer
     */
    protected function getDefaultContainer ( )
    {
        return $this->getMessageContainer($this->getDefaultLocale());
    }

    /**
     * メッセージコンテナを取得する
     *
     * @param string
     * @return MessageContainer
     */
    protected function getMessageContainer ($locale)
    {
        if (!isset($this->messageContainers[$locale])){
            $this->messageContainers[$locale] = $this->getCacheHandler()->useCacheIf(
                $this->useCache,
                'translator.locale.'.$locale,
                function (&$isSuccess) use($locale) {
                    $isSuccess = true;
                    $data = new MessageContainer($locale, $this);
                    return $data;
                },0,0,$status
            );
        }
        return $this->messageContainers[$locale];
    }
}
