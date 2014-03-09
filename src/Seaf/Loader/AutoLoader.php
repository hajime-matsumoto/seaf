<?php
/**
 * オートローダ PSR-0
 */
namespace Seaf\Loader;

/**
 * オートローダ
 */
class AutoLoader {

    const INVALID_NAMESPACE      = "ネームスペースが不正です。";
    const INVALID_NAMESPACE_PATH = "自動読み込みパスが不正です";

    const NAMESPACE_SEPARATOR    = '\\';
    const DIRECTORY_SEPARATOR    = '/';

    /**
     * @var array
     */
    private $nsList = array();

    /**
     * Usage
     * ================================
     *
     * <code>
     *  AutoLoader::factory(array(
     *      "namespaces" => array(
     *          "Seaf" =>  __DIR__.'/library/Seaf'
     *      ),
     *  ));
     * </code>
     *
     * @param array config
     */
    public static function factory ($config = array()) {
        $loader = new AutoLoader();

        // ネームスペースを登録する
        if (is_array($config['namespaces'])) foreach($config['namespaces'] as $ns => $path) {
            $loader->registerNamespace($ns, $path);
        }

        return $loader;
    }

    public function __construct ( ) {
    }

    /**
     * 登録する
     */
    public function register ( )
    {
        spl_autoload_register(array($this,'loadNamespace'));
    }

    /**
     * @param array $ns_config
     */
    public function registerNamespace ($ns, $path) {
        if (empty($ns) || !is_string($ns)) {
            throw new \Exception(self::INVALID_NAMESPACE);
        }

        if (empty($ns) || !is_string($path)) {
            throw new \Exception(self::INVALID_NAMESPACE_PATH);
        }

        $this->nsList[$ns] = $path;
    }

    /**
     * Namespaceベースの読み込み
     */
    public function loadNamespace ($class) {
        $list = $this->nsList;

        foreach ($list as $ns => $path) {

            if (0 === strpos($class,$ns)) {
                $filename = $this->transClassToFileName(substr($class,strlen($ns)));

                $file_path = $path.$filename.'.php';


                if (file_exists($file_path)) {
                    require_once $file_path;
                    return true;
                }
            }
        }
    }

    /**
     * Class名をPath名に変換する
     */
    private function transClassToFileName ($class) {
        return str_replace(self::NAMESPACE_SEPARATOR, self::DIRECTORY_SEPARATOR, $class);
    }
}
