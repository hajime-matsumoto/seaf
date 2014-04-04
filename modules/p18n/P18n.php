<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\P18n;

use Seaf;
use Seaf\Util;
use Seaf\DB;
use Seaf\Exception;
use Seaf\Pattern;
use Seaf\Data\Container\ArrayContainer;

class P18n
{
    use Pattern\Configure;

    protected $default_lang = 'en';
    protected $lang = 'ja';
    protected $lang_dir;
    protected $lang_enables = array();

    protected $db;

    //----------------------------
    // 設定

    /**
     * デフォルト言語を設定
     *
     * @param string
     */
    public function configDefaultLang ($lang)
    {
        $this->default_lang = $lang;
    }

    /**
     * 言語を設定
     *
     * @param string
     */
    public function configLang ($lang)
    {
        $this->locale($lang);
    }
    /**
     * 現在のデフォルトのロケールを設定/取得する
     *
     * @param string
     */
    public function defaultLocale ($lang = null)
    {
        if ($default_lang == null) return $this->default_lang;
        $this->default_lang = $default_lang;
    }

    /**
     * 現在のロケールを設定/取得する
     *
     * @param string
     */
    public function locale ($lang = null)
    {
        if ($lang == null) return $this->lang;
        $this->lang = $lang;
    }


    /**
     * 言語ファイルディレクトリ
     *
     * @param string
     */
    public function configLangDir ($dir)
    {
        $this->lang_dir = Seaf::FileSystem($dir);
    }

    /**
     * P18nを作成する
     *
     * @param array
     */
    public static function factory ($config = array())
    {
        $c = Util\ArrayHelper::container(
            Seaf::Config('p18n', array()) + $config
        );

        $p18n = new self();

        // バックエンドのデータソース
        if ($c('dsn')) {
            $p18n->db = DB\Handler::factory([
                'connectMap'=>[
                    'default' => $c('dsn')
                ]
            ]);
        }

        return $p18n;
    }

    /**
     * インストール
     */
    public function install ( )
    {
        $this->db->getTable('translation')->newRequest('command')->param([
            'drop' => true,
            'createIndex'=>['lang'=>1,'key'=>1 ]
        ])->execute();
    }

    /**
     * インポート
     */
    public function import ($lang, $prefix, $data )
    {
        $c = 0;
        foreach ($data as $k=>$v)
        {
            $c++;
            $key = $prefix.'.'.$k;
            $translation = $v;
            $this->getTable( )
                ->update()
                ->option(['upsert'=>true])
                ->where(compact('lang','key'))
                ->param(compact('lang','key','translation'))
                ->execute();
        }
        return $c;
    }

    public function getTable( )
    {
        return $this->db->getTable('translation');
    }

    /**
     * トランスレーションを追加する
     *
     * @param array
     */
    public function addTranslation ($lang, $key, $translation)
    {
        $res = $this->db->translation
            ->update( )
            ->option(['upsert'=>true])
            ->where(compact('lang','key'))
            ->param(compact('lang','key','translation'))
            ->execute();
    }

    /**
     * トランスレーションを取得する
     *
     * @param array
     */
    public function getTranslation ($key)
    {
        return new Translation($key, $this);
    }

    /**
     * 初期処理
     */
    public function initP18n ( )
    {
        foreach ($this->lang_dir as $file) {
            $lang = $file->basename();
            $this->lang_enables[$lang] = $file;
        }
        /**
         * Seafのヘルパに登録する
         */
        Seaf::Helper( )->register('t', array($this,'getHelper'));
    }

    /**
     * 言語コンテナを取得する
     *
     * @param string $lang
     */
    protected function langContainer($lang) {
        if (isset($this->locals[$lang])) return $this->locals[$lang];

        if (!array_key_exists($lang, $this->lang_enables)) {
            if ($lang == $this->default_lang) {
                throw new Exception\Exception($lang." 指定できない言語を指定しています。");
            }
            return $this->langContainer($this->default_lang);
        }

        return $this->locals[$lang] = new LanguageContainer($this->lang_enables[$lang]);
    }

    /**
     * 翻訳する
     *
     * @param string $word
     * @param mixed $v,,,
     */
    public function translate ($word)
    {
        // ------------------------------------------
        // 現在の言語から検索して見つからなければ
        // デフォルトの言語から検索する
        //
        $result = $this->_translate($this->lang, $word);
        if (false === $result) {
            $result = $this->_translate($this->default_lang, $word);
        }

        // ------------------------------------------
        // 引数があった場合は多機能な処理をする
        //
        if (func_num_args() > 1) {
            if (false === $p = strrpos($word, '_')) {
                $type = null;
            } else {
                $type = substr($word, $p+1);
            }

            switch ($type) {
            case 'pl':
                $param = func_get_arg(1);
                if (!is_array($result)) {
                    throw \RuntimeException('plタイプは配列でなければいけません');
                }

                if (array_key_exists($param, $result)) {
                    $tpl = $result[$param];
                } else {
                    $tpl = isset($result['']) ? $result['']: "%s";
                }
                $result = vsprintf($tpl, array_slice(func_get_args(),1));
                break;
            default:
                $result = vsprintf($result, array_slice(func_get_args(),1));
            }
        }

        // ------------------------------------------
        // 最終的な処理
        //
        if (false == $result) {
            return '[['.$word.']]';
        }

        if (is_array($result)) {
            return implode(',',$result);
        }

        return $result;
    }

    /**
     * 翻訳する
     *
     * @param string $lang
     * @param string $word
     * @return string|false
     */
    protected function _translate ($lang, $word)
    {
        $lc = $this->langContainer($lang);
        if ($lc->has($word)) {
            return $lc->get($word);
        }
        return false;
    }

    /**
     * ヘルパを取得する
     * 第一引数が指定された場合はlocaleを変更した
     * p18nのクローンからヘルパを取得する
     *
     * @param string ロケール
     * @return Closure
     */
    public function getHelper ($lang = null)
    {
        if ($lang == null) {
            return Seaf::ReflectionMethod($this, 'translate')->getClosure($this);
        } else {
            $p18n = clone $this;
            $p18n->locale($lang);
            return $p18n->getHelper();
        }
    }
}
