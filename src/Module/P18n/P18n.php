<?php
namespace Seaf\Module\P18n;

use Seaf;


use Seaf\Kernel\Kernel;
use Seaf\Module\ModuleIF;

/**
 * PHP版 i18n
 */
class P18n implements ModuleIF
{
    public $name = 'P18n';

    /**
     * カレント
     * @var string code
     */
    private $locale = 'ja';

    /**
     * デフォルトロケール
     * @var string code
     */
    private $default_locale = 'en';

    /**
     * ロードした言語リスト
     * @var array
     */
    private $langs = array();

    /**
     * デフォルトロケールを設定する
     *
     * @param string $code
     */
    public function defaultLocale ($code = null)
    {
        $this->default_locale = $code;
        return $this;
    }

    public function getDefaultLocale ( ){
        return $this->default_locale;
    }
    public function getLocale ( ){
        return $this->locale;
    }

    /**
     * ロケールを設定する
     *
     * @param string $code
     */
    public function locale ($code = null)
    {
        $this->locale = $code;
        return $this;
    }

    /**
     * 登録時の処理
     */
    public function register ( )
    {
        $this->initP18n();
    }

    /**
     * 初期化処理
     * @param 
     * @return void
     */
    protected function initP18n ()
    {
        $config = Kernel::DI('config');

        // 言語ファイルディレクトリを取得する
        if($config->dirs->p18n->isEmpty()) {
            Kernel::logger('p18n')->warning('言語ファイルディレクトリが設定されてません');
            return;
        }
        $path = $config->dirs->p18n->toString();

        // 言語ファイルを読み込む
        $dir = Kernel::fileSystem($path);
        if (!$dir->exists()) {
            Kernel::logger('p18n')->warning(array(
                '言語ファイルディレクトリ %s が存在しません',
                $dir->toString()
            ));
            return;
        }

        foreach ($dir->find('*.yaml') as $file) {
            $lang = $file->basename($no_ext = true);
            $this->langs[$lang] = $file;
        }

        // グローバルヘルパに登録する
    }

    /**
     * ランゲージコンテナを取り出す
     * @param string
     */
    public function getContainer ($lang)
    {
        if (empty($this->langs[$lang])) {
            Kernel::logger('p18n')->warning(array(
                "%sは対応していない言語です only %s",
                $lang,
                implode(',', $this->langs)

            ));
        }
        return new LangContainer($this->langs[$lang], $lang, $this);
    }

    /**
     * 翻訳する
     * @param string
     */
    public function translate ($key)
    {
        $result = $this->getContainer($this->locale)->translate($key);
        if (empty($result)) {
            return null;
        } elseif (is_string($result)) {
            return $result;
        }
        
        if ($result->isString()) {
            $string = $result->toString();
            if (func_num_args() > 1) {
                $string = vsprintf($string, array_slice(
                    func_get_args(), 1
                ));
            }
            return $string;
        } elseif ($result->isArray()) {

            if (func_num_args() > 1) {
                $num = func_get_arg(1);
                if($result->has($num)) {
                    return $result->get($num)->toString();
                }
                return vsprintf(
                    $result->get('')->toString(),
                    $num
                );
            }
            return $result->toArray();
        }
    }

    /**
     * ヘルパを取得する
     * @return Helper
     */
    public function getHelper()
    {
        return new Helper($this);
    }
}


class Helper
{
    private $p18n;

    public function __construct (P18n $p18n)
    {
        $this->p18n = $p18n;
    }

    public function __invoke ($key)
    {
        return Kernel::Dispatcher(
            array($this->p18n,'translate'),
            func_get_args(),
            $this
        )->dispatch();
    }
}
