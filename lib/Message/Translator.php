<?php // vim: set ft=php ts=4 sts=4 sw=4 et: 
namespace Seaf\Message;

use Seaf\Base;
use Seaf\Cache;
use Seaf\Event;

class Translator
{
    use Base\SingletonTrait;
    use Cache\CacheUserTrait;
    use Event\ObservableTrait;

    private $locale = 'ja';
    private $defaultLocale = 'en';
    private $messageDir;
    private $useCache = false;

    public static function who  ( )
    {
        return __CLASS__;
    }

    public function __invoke ($code)
    {
        return call_user_func_array([$this,'translate'], func_get_args());
    }

    /**
     * キャッシュを使う
     */
    public function useCache($flg = true)
    {
        $this->useCache = $flg;
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
        if (!$data = $this->find($code, $this->getLocale())) {
            return '[['.$code.']]';
        }

        if (func_num_args() > 1) {
            if (is_string($data)) {
                return vsprintf($data, array_slice(func_get_args(),1));
            }elseif(is_array($data)) {
                $key = func_get_arg(1);
                $format = isset($data[$key]) ? $data[$key]: current($data);
                return sprintf($format, $key);
            }
        }

        return $data;
    }

    /**
     * コードに紐づくメッセージを検索する
     *
     * @param string
     * @param string
     * @return mixed
     */
    public function find ($code, $locale)
    {
        $translation = false;
        $fallbacked = false;

        if ($this->getMessageContainer($locale)->hasTranslation($code)) {
            $translation = $this->getMessageContainer($locale)->getTranslation($code);
        }elseif ($this->getDefaultContainer( )->hasTranslation($code)) {
            $translation = $this->getDefaultContainer( )->getTranslation($code);
            $fallbacked = true;
        }

        if ($fallbacked == true || $translation == false) {
            $this->trigger('translation.notfound', [
                'locale' => $locale,
                'code'   => $code,
                'translation' => &$translation
            ]);
        }

        return $translation;
    }

    /**
     * セクションを取得する
     */
    public function section($prefix)
    {
        return new TranslatorSection($this, $prefix);
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
            $this->messageContainers[$locale] = $this->getCacheHandler()
                ->useCacheIf(
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
