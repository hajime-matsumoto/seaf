<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

use Seaf\Component;
use Seaf\Container;
use Seaf\Base;
use Seaf\Cache;
use Seaf\Data\KeyValueStore;
use Seaf\Logging;
use Seaf\Event;
use Seaf\Registry;
use Seaf\CLI;
use Seaf\Web;
use Seaf\Loader;
use Seaf\Wrapper;
use Seaf\Message;

/**
 * Seafとコンポーネント取得のショートハンド
 *
 * @param string
 * @return mixed
 */
function Seaf($component_name = null) {
    if ($component_name == null) {
        return Seaf::getSingleton();
    }
    return Seaf::getSingleton( )->getComponent($component_name);
}

function seaf_container ($data)
{
    if ($data instanceof Controller\ArrayContainer) {
        return $data;
    }
    return new Container\ArrayContainer($data);
}
function seaf_closure ($data)
{
    return Wrapper\Closure::create($data);
}

function seaf_registry_get ($name, $default =null)
{
    return Registry\Registry::getSingleton( )->getVar($name, $default);
}

/**
 * フレームワークベースコントローラ
 */
class Seaf
{
    use Base\SingletonTrait;
    use Component\ComponentCompositeTrait;
    use Event\ObservableTrait;
    use Logging\LoggingTrait;

    /**
     * シングルトン用のクラス名取得メソッド
     *
     * @return string
     */
    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * スタティックな呼び出しはコンポーネント取得とみなす
     *
     * @param string
     * @param array
     * @return mixed
     */
    public static function __callStatic($name, $params)
    {
        $component = static::getSingleton( )->getComponent($name);
        return $component;
    }

    /**
     * コンストラクタ
     */
    public function __construct( )
    {
        $this->setupComponentLoader();
    }

    /**
     * コンポーネントローダのセットアップ
     */
    public function setupComponentLoader ( )
    {
        // コンポーネントローダを追加
        $this->addComponentLoader(
            new Component\Loader\InitMethodLoader(
                $this
            )
        );

        $this->addComponentLoader(
            new Component\Loader\NamespaceLoader(
                'Seaf\Core\Component'
            )
        );

        // コンポーネント作成時の処理を追加
        $this->on('component.create', function ($e) {
            $instance = $e->getVar('component');

            if ($instance instanceof Seaf\Core\ComponentIF) {
                $instance->initSeafComponent($this);
            }
        });
    }

    /**
     * 定義されていないメソッドの呼び出しはコンポーネント取得とみなす
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function __call($name, $params)
    {
        $component = $this->getComponent($name);
        return $component;
    }

    /**
     * 初期化処理:ベースフレームワークの初期処理
     *
     * @param string
     * @param string
     * @param array
     * @return Seaf
     */
    public function init ($project_root, $env = 'development', $options = [])
    {
        // スタートアップ時の設定をする
        $this->Registry( )
            ->setVar($options)
            ->setVar([
                'project_root' => $project_root,
                'kvs_file_dir' => $project_root.'/var/store',
                'cache_dir'    => $project_root.'/var/cache',
                'env'          => $env
            ]);


        // 設定を読み込む
        $c = $this->Config( )->loadConfigFiles($project_root.'/configs');

        // コンポーネント群の設定を伝搬する
        $this->loadComponentConfig($c('component', []));

        // PHPを設定する
        mb_internal_encoding($c('setting.encoding', 'utf-8'));
        mb_language($c('setting.lang', 'ja'));
        date_default_timezone_set($c('setting.timezone', 'Asia/Tokyo'));

        // グローバルのロガーをセットアップする
        $logger = Logging\LogHandler::getSingleton( );
        $logger->setName('Seaf');
        $logger->setup($c('logging', []));
        $logger->register();
        $logger->swapSingleton(); // 昇格

        // 開始メッセージを送出する
        $logger->info('START', null, ['SEAF','SYSTEM']);

        $storeLogFunction =  function ($e) {
            $this->Registry( )->appendVar('logs', $e->getVar('log'));
        };


        $this->Registry->on('enable.debug', function ($e) use($logger, $storeLogFunction) {
            $logger->debug('Debug On');
            // デバッグ中はログをレジストリに保存する
            $logger->on('log.post', $storeLogFunction);
        });
        $this->Registry->on('disable.debug', function ($e) use($logger, $storeLogFunction) {
            $logger->debug('Debug Off');
            $logger->off('log.post', $storeLogFunction);
        });
        // ロガーをコンポーネントに登録する
        $this->setComponent('Logger', $logger);

        if ($env == 'development') {
            $this->Registry->enableDebug();
        }
        return $this;
    }

    public function getLogsClear ( )
    {
        return $this->Registry( )->getVarClear('logs');
    }

    //------------------------------------------
    // コンポーネント初期化用のメソッド
    //------------------------------------------

    /**
     * クラスローダの作成
     *
     * @param array
     * @return Loader\ClassLoader
     */
    public function initClassLoader($cfg)
    {
        $loader = new Loader\ClassLoader( );
        $loader->register();
        return $loader;
    }

    /**
     * レジストリの作成
     *
     * @return Container\ArrayContainer
     */
    public function initRegistry ( )
    {
        $data = Registry\Registry::getSingleton();
        return $data;
    }

    /**
     * KVSハンドラの作成
     *
     * @param array
     * @return KeyValueStore\KVSHandler
     */
    public function initKeyValueStore($cfg)
    {
        $kvs = KeyValueStore\KVSHandler::factory($cfg);
        $kvs->swapSingleton();
        return $kvs;
    }

    /**
     * キャッシュハンドラの作成
     *
     * @param array
     * @return Cache\CacheHandler
     */
    public function initCache($cfg)
    {
        $this->getComponent('KeyValueStore');
        $cache = new Cache\CacheHandler('seaf');
        $cache->swapSingleton();
        return $cache;
    }

    /**
     * CLIコントローラの作成
     *
     * @param array
     * @return CLI\CLIController
     */
    public function initCLIController($cfg)
    {
        return new CLI\CLIController( );
    }

    /**
     * Webコントローラの作成
     *
     * @param array
     * @return Web\WebController
     */
    public function initWebController($cfg)
    {
        return new Web\WebController( );
    }


    /**
     * メッセージトランスレータの作成
     *
     * @param array
     * @return Loader\ClassLoader
     */
    public function initMessage($cfg)
    {
        $translator = Message\Translator::getSingleton( );
        $translator->setDefaultLocale($this->Config()->getConfig('setting.lang'));
        $translator->setCacheKey($cfg('cacheKey', 'message'));
        $translator->setMessageDir($cfg('dir', __DIR__.'/var/lang'));
        $translator->on('translation.notfound', function ($e) {
            $e->setVar('translation', 'notfound:code('.$e->getVar('code').')');
            $this->debug([
                'Translation NotFound Locale(%s) Code(%s)',
                    $e->getVar('locale'),
                    $e->getVar('code')
            ]);
        });
        return $translator;
    }

    /**
     * PHPファンクションラッパの作成
     */
    public function initPHPFunction( )
    {
        return Wrapper\PHPFunction::getSingleton();
    }

    /**
     * PHPスーパーグローバルラッパの作成
     */
    public function initSuperGlobalVars( )
    {
        return Wrapper\SuperGlobalVars::getSingleton();
    }
}
