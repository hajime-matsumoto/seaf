<?php
namespace Seaf\Loader;

/**
 * クラスローダ
 * ----------------------
 */
class ClassLoader
{
    const NAMESPACE_SEPARATOR = '\\';
    const DIRECTORY_SEPARATOR = '/';
    const TRAIT_DIR_NAME      = 'trait';


    /**
     * ネームスーペースのリスト
     * @var $array
     */
    public $namespaces = array();

    /**
     * ライブラリのリスト
     * @var $array
     */
    public $libraries = array();

    /**
     * 検索したファイルのログ
     * @var array
     */
    public $logs = [];

    /**
     * factory
     */
    public static function factory ($cfg = [])
    {
        $loader = new self();
        if (isset($config['namespaces'])) foreach ($config['namespaces'] as $ns=>$path) {
            $loader->addNamespace($ns, $path);
        }

        if (isset($config['libraries'])) foreach ($config['libraries'] as $path) {
            $loader->addLibrary( $path);
        }
        return $loader;
    }

    /**
     * レジスタ
     */
    public function register ()
    {
        spl_autoload_register(array($this,'loadByNamespace'));
        spl_autoload_register(array($this,'loadFromLibrary'));
        return $this;
    }

    /**
     * ライブラリパスを追加する
     *
     * @param string $path
     * @return ClassLoader
     */
    public function addLibrary ($path)
    {
        $this->libraries[] = $path;
        return $this;
    }

    /**
     * ネームスペースを追加する
     *
     * @param string $namespace
     * @param string $path
     * @return ClassLoader
     */
    public function addNamespace ($namespace, $path)
    {
        $this->namespaces[$namespace][] = $path;
        return $this;
    }

    /**
     * Namespaceベースの読み込み
     *
     * @param string;
     */
    public function loadByNamespace ($class) 
    {
        $this->logs = [];
        $list = $this->namespaces;

        foreach ($list as $ns => $paths) {
            foreach ($paths as $path) {
                if (0 === strpos($class,$ns)) {
                    $filename = $this->transClassToFileName(substr($class,strlen($ns)));

                    if ($this->loadClassFile($filename, $path)) {
                        return true;
                    }
                }
            }
        }
        var_dump($this->logs);
        return false;
    }


    /**
     * ライブラリをロードする
     *
     * @param string $class
     * @return void
     */
    public function loadFromLibrary ($class)
    {
        $this->logs = [];
        $filename = $this->transClassToFileName($class);

        foreach ($this->libraries as $path) {

            if ($this->loadClassFile($filename, $path)) {
                return true;
            }
        }
    }

    /**
     * クラスファイルをロードする
     *
     * @param string $filename
     * @param string $path
     * @return bool
     */
    private function loadClassFile($filename, $path = '')
    {
        $file_path = '/'. trim($path,'/') .'/'. trim($filename,'/') . '.php';

        $this->logs[] = $file_path;

        if (file_exists($file_path)) {
            require_once $file_path;
            return true;
        }

        // Traitの場合はTrait/もチェックする
        if (substr($filename,-5) == 'Trait') {
            $file_path = $path.dirname($filename).'/trait/'.basename($filename).'.php';
            $this->logs[] = $file_path;
            if (file_exists($file_path)) {
                require_once $file_path;
                return true;
            }
        }
    }


    /**
     * Class名をPath名に変換する
     *
     * @param string
     * @return string
     */
    private function transClassToFileName ($class) {
        $ns = self::NAMESPACE_SEPARATOR;
        $ds = self::DIRECTORY_SEPARATOR;

        foreach (explode($ns, $class) as $v) {
            $class_name_parts[] = ucfirst($v);
        }
        $file_name = implode($ds, $class_name_parts);

        return $file_name;
    }
}
