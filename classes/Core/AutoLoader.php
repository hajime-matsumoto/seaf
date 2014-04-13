<?php
namespace Seaf\Core;

/**
 * オートローダ
 * ----------------------
 */
class AutoLoader
{
    const NAMESPACE_SEPARATOR    = '\\';
    const DIRECTORY_SEPARATOR    = '/';

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
     * factory
     */
    public static function factory ($config)
    {
        $loader = new self();
        if (isset($config['namespaces'])) foreach ($config['namespaces'] as $ns=>$path) {
            $loader->namespaces[$ns][] = $path;
        }

        if (isset($config['libraries'])) foreach ($config['libraries'] as $path) {
            $loader->libraries[] = $path;
        }
        return $loader;
    }

    /**
     * レジスタ
     */
    public function register ()
    {
        spl_autoload_register(array($this,'loadNamespace'));
        spl_autoload_register(array($this,'library'));
        return $this;
    }

    /**
     * addNamespace
     *
     * @param $namespace, $path
     * @return void
     */
    public function addLibrary ($path)
    {
        if (is_array($path)) {
            foreach ($path as $v) {
                $this->libraries($v);
            }
            return $this;
        }

        $this->libraries[] = $path;
        return $this;
    }

    /**
     * addNamespace
     *
     * @param $namespace, $path
     * @return void
     */
    public function addNamespace ($namespace, $path)
    {
        if (is_array($namespace)) {
            foreach ($namespace as $k=>$v) {
                $this->addNamespace($k, $v);
            }
            return $this;
        }

        $this->namespaces[$namespace][] = $path;
        return $this;
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
                    if (file_exists($file_path)) {
                        require_once $file_path;
                        return;
                    }

                    // Traitの場合はTrait/もチェックする
                    if (substr($filename,-5) == 'Trait') {
                        $file_path = $path.dirname($filename).'/Trait/'.basename($filename).'.php';
                        if (file_exists($file_path)) {
                            require_once $file_path;
                            return;
                        }
                    }
                }
            }
        }
    }

    /**
     * library
     *
     * @param $class
     * @return void
     */
    public function library ($class)
    {
        $filename = $this->transClassToFileName($class);

        foreach ($this->libraries as $path) {
            $file_path = $path.'/'.$filename.".php";
            if (file_exists($file_path)) {
                require_once $file_path;
                return;
            }
        }
    }


    /**
     * Class名をPath名に変換する
     */
    private function transClassToFileName ($class) {
        $ns = self::NAMESPACE_SEPARATOR;
        $ds = self::DIRECTORY_SEPARATOR;

        foreach (explode($ns, $class) as $v) {
            $class_name_parts[] = ucfirst($v);
        }
        $class = implode($ds, $class_name_parts);

        //return str_replace($ns, $ds, $class);
        return $class;
    }
}
