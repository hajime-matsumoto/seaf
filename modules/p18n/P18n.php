<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\P18n;

use Seaf;
use Seaf\Util;
use Seaf\DB;
use Seaf\Exception;
use Seaf\Base;
use Seaf\Container\ArrayContainer;

class P18n
{
    use Base\ComponentCompositeTrait;

    protected $default_locale = 'en';
    protected $locale = 'ja';
    protected $dir;
    protected $lang_enables = array();

    protected $db;

    /**
     * コンストラクタ
     */
    public function __construct ($cfg)
    {
        $cfg = new ArrayContainer($cfg);
        $this
            ->defaultLocale($cfg('default-locale', $this->default_locale))
            ->locale($cfg('locale', $this->locale));
        if ($cfg->has('dir')) {
            $this->dir = $cfg('dir');
        }

        $this->setComponentContainer(__NAMESPACE__.'\\ComponentContainer');
        $this->component()->loadConfig([
            'Datasource' => $cfg('datasource')
        ]);
        $this->db = $this->component('Datasource');
        $this->install();
    }

    //----------------------------
    // 設定

    /**
     * デフォルト言語を設定
     *
     * @param string
     */
    public function defaultLocale ($code)
    {
        $this->default_locale = $code;
        return $this;
    }

    /**
     * 言語を設定
     *
     * @param string
     */
    public function locale ($code)
    {
        $this->locale = $code;
        return $this;
    }

    //----------------------------
    // 取得

    /**
     * 現在のデフォルトのロケールを取得する
     *
     * @return string
     */
    public function getDefaultLocale ($default_lang = null)
    {
        return $this->default_locale;
    }

    /**
     * 現在のロケールを取得する
     *
     * @return string
     */
    public function getLocale ($lang = null)
    {
        return $this->locale;
    }


    /**
     * インストール
     */
    public function install ( )
    {
        // ステータスを取得する
        $res = $this->db->getTable('status')->find( )
            ->where(['id'=>1])
            ->execute();

        $install_status = $res->fetch();
        if ($install_status == null) {
            // インデックスを作成する
            $this->db->getTable('translation')->newRequest('command')->param([
                'drop' => true,
                'createIndex'=>['lang'=>1,'key'=>1 ]
            ])->execute();

            // ステータスを作る
            $status = ['id' => 1];
        }

        foreach(glob($this->dir.'/*.yaml') as $file) {
            $File = new Seaf\Util\FileSystem\File($file);
            if ('yaml' == $File->ext( )) {
                $lang = $File->basenameWithOutExt();

                if (
                    !isset($status[(string)$file]) ||
                    $status[(string)$file] < $File->mtime()
                ) { 
                    $c = $this->import($lang, '', $File->toArray());
                }
            }

        }

        // ステータスを保存
        $this->db->getTable('status')->update( )
            ->option(['upsert'=>true])
            ->where(['id'=>1])
            ->param($install_status)
            ->execute();
    }

    /**
     * インポート
     */
    public function import ($lang, $prefix, $data )
    {
        $c = 0;
        foreach ($data as $k=>$v)
        {
            if (is_array($v)) {
                $c += $this->import(
                    $lang,
                    ($prefix ? $prefix.".": "").$k,
                    $v
                );
                continue;
            }
            $c++;
            $key = ($prefix ? $prefix.'.':'').$k;
            $translation = $v;
            $res = $this->getTable( )
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
    public function getTranslation ($key = '')
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
        $result = $this->_translate($this->locale, $word);
        if (false === $result) {
            $result = $this->_translate($this->default_locale, $word);
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
