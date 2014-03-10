<?php
namespace Seaf\Core;

/**
 * カーネルクラス
 * =============================
 *
 * 役割
 * -----------------------------
 *
 * 1. 唯一であるものを定義する
 * 2. 環境変数をラップする
 * 3. ファイルシステムをラップする
 * 4. クラスのオートローディング
 *
 *
 * コンポーネントの呼び出し
 * -----------------------------
 * Kernel::fileSystem();  # ファイルシステムの取得
 * Kernel::fS();          # ファイルシステムの取得 (ショートハンド)
 * Kernel::classLoader(); # クラスローダの取得
 * Kernel::cl();          # クラスローダの取得 (ショートハンド)
 * Kernel::rgistry();     # レジストリの取得
 * Kernel::rg();          # レジストリの取得 (ショートハンド)
 * Kernel::system();      # システムコンポーネントの取得
 *
 * 特殊なメソッド
 * -----------------------------
 * Kernel::invokeArgs($callback, $params)   # コールバックの実行
 * Kernel::newInstanceArgs($class, $params) # クラスのインスタンス作成
 * Kernel::ReflectionClass($class) # クラスのインスタンス作成
 */
class Kernel
{
    /**
     * @var Registry
     */
    public $registry;

    /**
     * @var FileSystem
     */
    public $fileSystem;

    /**
     * @var ClassLoader
     */
    public $classLoader;

    /**
     * @var SystemComponent
     */
    public $system;

    /**
     * カーネルコンポーネントの初期化
     */
    private function __construct()
    {
        $this->registry    = new Registry($this);
        $this->fileSystem  = new FileSystem($this);
        $this->classLoader = new ClassLoader($this);
        $this->system      = new SystemComponent($this);
    }

    /**
     * レジストリの取得
     *
     * @return FileSystem
     */
    public static function registry ()
    {
        return self::singleton()->registry;
    }
    public static function rg()
    {
        return self::registry();
    }

    /**
     * クラスローダの取得
     *
     * @return FileSystem
     */
    public static function classLoader ()
    {
        return self::singleton()->classLoader;
    }
    public static function cl() {
        return self::classLoader();
    }

    /**
     * ファイルシステムの取得
     *
     * @return FileSystem
     */
    public static function fileSystem ()
    {
        return self::singleton()->fileSystem;
    }
    public static function fs () {
        return self::fileSystem();
    }

    /**
     * system
     *
     * @return SystemComponent
     */
    public static function system ()
    {
        return self::singleton()->system;
    }

    /**
     * シングルトンインターフェイス
     *
     * @return void
     */
    public static function singleton ()
    {
        static $instance = false;

        if (true == $instance) {
            return $instance;
        }
        return $instance = new self();
    }

    /**
     * public static 
     *
     * @param $config = array()
     * @return void
     */
    public static function init ($config = array())
    {
        global $_SERVER, $_SESSION, $_COOKIE, $_POST, $_GET, $_REQUEST, $argv, $argc;

        static $initialized = false;

        if ($initialized == true && empty($config)) return self::singleton();

        if (empty($config)) {
            $config = array(
                'vars' => array(
                    'SERVER'  => isset($_SERVER) ? $_SERVER: array(),
                    'SESSION' => isset($_SESSION) ? $_SESSION: array(),
                    'COOKIE'  => isset($_COOKIE) ? $_COOKIE: array(),
                    'POST'    => isset($_POST) ? $_POST: array(),
                    'GET'     => isset($_GET) ? $_GET: array(),
                    'REQUEST' => isset($_REQUEST) ? $_REQUEST: array(),
                    'argv'    => isset($argv) ? $argv: array(),
                    'argc'    => isset($argc) ? $argc: 0
                ),
                'fileSystem' => array(
                    '/lib' => __DIR__
                ),
                'classLoader' => array(
                    'namespaces' => array(
                        'Seaf' => '/lib'
                    )
                )
            );
            return self::init($config);
        }

        // 初期化処理
        if (isset($config['vars'])) {
            self::singleton()->registry->init($config['vars']);
        }

        if (isset($config['fileSystem'])) {
            self::singleton()->fileSystem->init($config['fileSystem']);
        }

        if (isset($config['classLoader'])) {
            self::singleton()->classLoader->init($config['classLoader']);
        }

        $initialized = true;
        return self::singleton();
    }

    /**
     * クラスを作成する
     */
    public static function newInstanceArgs($class, $args)
    {
        $rc = new \ReflectionClass($class);
        return $rc->newInstanceArgs($args);
    }

    /**
     * メソッドを実行する
     */
    public static function invokeArgs($method, $args)
    {
        return call_user_func_array($method, $args);
    }

    /**
     * カスタマイズしたリフレクションクラスを取得する
     */
    public static function ReflectionClass($class)
    {
        return new ReflectionClass($class);
    }

    /**
     * ヘッダを送信する
     *
     * @param string $header
     * @param bool $replace
     * @param int $code
     */
    public static function header( $header, $replace = true,  $code = false )
    {
        self::system()->header($header, $replace, $code);
    }
}

/**
 * カーネルコンポーネント
 */
class Component
{
    protected $kernel;

    public function __construct (Kernel $kernel)
    {
        $this->kernel = $kernel;
    }
}


/**
 * レジストリ
 */
class Registry extends Component
{
    /**
     * init
     *
     * @param $config
     * @return void
     */
    public function init ($config)
    {
        $this->vars = $config;
    }

    /**
     * set
     *
     * @param $name, $value
     * @return void
     */
    public function set ($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * get
     *
     * @param $name, $default = false
     * @return void
     */
    public function get ($name, $default = false)
    {
        return isset($this->vars[$name]) ? $this->vars[$name]: $default;
    }

    /**
     * __get
     *
     * @param $key
     * @return void
     */
    public function __get ($key)
    {
        return $this->vars[$key];
    }
}

/**
 * クラスローダー
 */
class ClassLoader extends Component
{
    const NAMESPACE_SEPARATOR    = '\\';
    const DIRECTORY_SEPARATOR    = '/';

    /**
     * ネームスーペースのリスト
     * @var $array
     */
    public $namespaces = array();

    /**
     * init
     *
     * @param $config
     * @return void
     */
    public function init ($config)
    {
        foreach ($config['namespaces'] as $ns=>$path) {
            $this->namespaces[$ns][] = $path;
        }

        spl_autoload_register(array($this,'loadNamespace'));
    }

    /**
     * addNamespace
     *
     * @param $namespace, $path
     * @return void
     */
    public function addNamespace ($namespace, $path)
    {
        $this->namespaces[$namespace][] = $path;
    }

    /**
     * Namespaceベースの読み込み
     *
     * @param string;
     */
    public function loadNamespace ($class) 
    {
        $list = $this->namespaces;

        foreach ($list as $ns => $paths) {
            foreach ($paths as $path) {
                if (0 === strpos($class,$ns)) {
                    $filename = $this->transClassToFileName(substr($class,strlen($ns)));

                    $file_path = $path.$filename.'.php';
                    $fs = $this->kernel->fileSystem;


                    if ($fs->fileExists($file_path)) {
                        $fs->requireOnce($file_path);
                    }
                }
            }
        }
    }

    /**
     * Class名をPath名に変換する
     */
    private function transClassToFileName ($class) {
        $ns = self::NAMESPACE_SEPARATOR;
        $ds = self::DIRECTORY_SEPARATOR;

        return str_replace($ns, $ds, $class);
    }
}

/**
 * ファイルシステム
 */
class FileSystem extends Component
{
    /**
     * init
     *
     * @param $config
     * @return void
     */
    public function init ($config)
    {
        foreach ($config as $alias=>$path) {
            $this->addFilePath($alias, $path);
        }
    }

    /**
     * addFilePath
     *
     * @param string $name
     * @param string $path
     * @return void
     */
    public function addFilePath ($name, $path = false)
    {
        if (is_array($name)) foreach($name as $k => $v) {
            $this->addFilePath($k, $v);
            return $this;
        }
        $this->files[$name] = $path;
        return $this;
    }

    /**
     * 要求されたファイルを読み込む
     *
     * @param $file
     * @return void
     */
    public function requireOnce ($file)
    {
        $path = $this->transRealPath($file);
        require_once $path;
    }

    /**
     * 要求されたファイルを実行
     *
     * @param $file
     * @return void
     */
    public function includeFile ($file, $vars = array())
    {
        extract($vars);
        $path = $this->transRealPath($file);
        include $path;
    }

    /**
     * 要求されたファイルの存在を確認する
     *
     * @param $file
     * @return bool
     */
    public function fileExists ($file)
    {
        $path = $this->transRealPath($file);
        if (file_exists($path)) {
            return true;
        }
        return false;
    }

    /**
     * getContents
     *
     * @param $file
     * @return void
     */
    public function getContents ($file)
    {
        $path = $this->transRealPath($file);
        return file_get_contents($path);
    }

    public function transRealPath($file)
    {
        if ($file{0} != '/') $file = '/'.$file;
        $realpath = false;
        foreach ($this->files as $k => $v) {
            if (0 === strpos($file,$k)) {
                $realpath = rtrim($v,'/').'/'.ltrim(substr($file,strlen($k)),'/');
                if (file_exists($realpath)) return $realpath;
            }
        }
        return $realpath;
    }
}

/**
 * システムハンドラ
 */
class SystemComponent extends Component
{
    private $maps = array();

    public function __construct( )
    {
        $this->map('halt', array($this, '_halt'));
        $this->map('header', array($this, '_header'));
    }

    /**
     * スクリプトを終了させます
     *
     * @param $message = null
     * @return void
     */
    public function shutdown ($message = null)
    {
        exit($message);
    }

    /**
     * ヘッダを送信する
     *
     * @param string $header
     * @param bool $replace
     * @param int $code
     */
    public static function _header( $header, $replace = true,  $code = false )
    {
        if( $code !== false ) {
            header( $header, $replace, $code );
        } else {
            header( $header, $replace );
        }
    }

    public function _halt ($message = null)
    {
        $this->shutdown($message);
    }

    public function out ($body)
    {
        $fp = fopen('php://output','w');
        fwrite($fp, $body);
        fclose($fp);
    }

    public function in()
    {
        $fp = fopen('php://input','r');
        $value = fread($fp,1024);
        fclose($fp);
        return $value;
    }

    public function execute($cmd, $buf = false)
    {
        if ($buf == true) {
            ob_start();
            system($cmd);
            return ob_get_clean();
        }else{
            system($cmd);
        }
    }

    public function map ($name, $action)
    {
        $this->maps[$name] = $action;
    }

    public function __call($name, $params)
    {
        if (array_key_exists($name, $this->maps)) {
            return call_user_func_array($this->maps[$name], $params);
        }
        throw new Exception(array("%sは登録されていません。", $name));
    }
}

class ReflectionClass extends \ReflectionClass
{
    public function mapAnnotation($callback)
    {
        foreach($this->getMethods() as $method){
            $anots = array();
            if ($method->getDeclaringClass()->getName() == $this->getName()) {

                $comment = $method->getDocComment();
                $line = preg_split("/\n/", $comment);
                for ($i=1;$i<(count($line)-1);$i++) {
                    if (preg_match('#[^@]+@Seaf([^\s]+)\s+(.+)#',$line[$i],$m)) {
                        $anots[lcfirst($m[1])] = $m[2];
                    }
                }
                $callback($method, $anots);
            }
        }
    }
}
