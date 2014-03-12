<?php
namespace Seaf\Module\P18n;

use Seaf\Environment\Environment;
use Seaf;
use Seaf\Core\Pattern\ExtendableMethods;


// @TODO
// use Seaf\Module\ModuleIF;

/**
 * PHP版 i18n
 */
class P18n extends Environment
{
    public $name = 'P18n';

    /**
     * ランゲージファイルディレクトリ
     * @var string
     */
    private $dir;

    private $current_lang = 'en';
    private $default_lang = 'ja';
    private $langs = array();

    public function __get ($key)
    {
        return $this->get($key);
    }

    public function __call ($key, $params)
    {
        if ($this->has($key)) {
            $data = $this->get($key);
            if (empty($params)) {
                return $data;
            }else{
                return call_user_func_array($data, $params);
            }
        }
    }

    /**
     * __construct
     *
     * @param 
     */
    protected function initEnvironment ()
    {
        // 言語ファイルを読み込む
        if (!$dir = $this->di('config')->dirs->p18n) {
            $this->di('logger')->error('p18nディレクトリが設定されていません');
        }
        $this->di('logger')->debug(array('%sをランゲージディレクトリにセットしました',$dir));

        // 対応言語を探す
        $files = Seaf::kernel()->fileSystem()->glob($dir.'/*.yaml');
        foreach ($files as $file)
        {
            $base = basename($file);
            $lang = substr($base,0,-5);
            $this->langs[$lang] = $file;
        }
    }

    /**
     * 言語コンテナを取り出す
     *
     * @param $key
     * @return void
     */
    public function get($key)
    {
        if ($this->getContainer($this->current_lang)->has($key))
        {
            return $this->getContainer($this->current_lang)->get($key);
        } elseif ($this->getContainer($this->default_lang)->has($key)) {
            return $this->getContainer($this->default_lang)->get($key);
        }
        $this->di('logger')->warning(array(
            "言語%sの%sが見つかりません",$this->current_lang,$key
        ));

        return '[[$key]]';
    }

    public function has($key)
    {
        if ($this->getContainer($this->current_lang)->has($key))
        {
            return true;
        } elseif ($this->getContainer($this->default_lang)->has($key)) {
            return true;
        }
        return false;
    }

    public function setLang($lang)
    {
        $this->current_lang = $lang;
        return $this;
    }

    public function setDefaultLang($lang)
    {
        $this->default_lang = $lang;
        return $this;
    }


    public function getContainer($lang)
    {
        static $langs = array();
        if (isset($langs[$lang])) return $langs[$lang];

        return $langs[$lang] = new LangContainer(
            $this->di('kernel')->fileSystem()->loadYaml($this->langs[$lang])
        );
    }

    /**
     * register
     *
     * @param #:argument
     * @return void
     */
    public static function register (Environment $env)
    {
        // グローバルに組み込む
        Seaf::GCM()->register('p18n', __CLASS__);
    }

    public function importHelper(Environment $env)
    {
        $env->map('t',function( ) {
            return $this;
        });
    }
}
