<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * レジストリモジュール
 */
namespace Seaf\Registry;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\BackEnd;
use Seaf\Logging;

use Seaf\Base\Module;

/**
 * モジュールファサード
 */
class RegistryFacade implements Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;

    protected static $object_name = 'Registry';

    public function __construct(Module\ModuleIF $module, $datas = [])
    {
        if (!empty($module)) {
            $this->setParentModule($module);
        }

        $this->data = Util::Dictionary($datas);
    }

    /**
     * 値を取得する
     *
     * @param string
     * @param mixed optional
     * @return mixed optional
     */
    protected function get($name, $default = null)
    {
        $this->debug(["GET %s",$name]);

        return $this->data->get($name, $default);
    }

    /**
     * 値が存在すればTrue
     *
     * @param string
     */
    protected function has($name)
    {
        return $this->data->has($name);
    }

    /**
     * 値が格納する
     *
     * @param string
     * @param mixed
     */
    protected function set($name, $value)
    {
        $this->debug(["SET %s=%s",$name, $value]);
        $this->data->set($name, $value);
        return $this;
    }

    /**
     * デバッグフラグをOnにする
     */
    protected function debugOn( )
    {
        $this->info("Debug On");
        $this->data->set('debug_flg', true);
    }

    /**
     * デバッグフラグをOffにする
     */
    protected function debugOff( )
    {
        $this->info("Debug Off");
        $this->data->set('debug_flg', false);
    }

    /**
     * インターナルエンコーディングを設定する
     *
     * @param string 文字コード(utf-8)など
     */
    protected function internalEncoding($encode) {
        $this->info(["internalEncoding %s",$encode]);
        mb_internal_encoding($encode);
    }

    /**
     * 現在のロケールを設定する
     *
     * @param string ロケール(ja|enなど)
     */
    protected function language($locale) {
        $this->info(["language %s",$locale]);
        mb_language($locale);
    }

    /**
     * タイムゾーンを設定する
     *
     * @param string Asia/Tokyo など
     */
    protected function timezone($zone) {
        $this->info(["timezone %s",$zone]);
        date_default_timezone_set($zone);
    }

    /**
     * @param string CLI|WEB
     */
    public function getSapiNAme( ) {
        if (!$this->data->has('sapi_name')) {
            $this->data->set('sapi_name', php_sapi_name());
        }
        return $this->data->get('sapi_name');
    }
    /**
     * デバッグ中か
     *
     * @return bool
     */
    protected function isDebug()
    {
        return $this->data->get('debug_flg', false) ? true: false;
    }

    // =======================================
    // PHPハンドラ系の処理
    // =======================================
    protected function phpRegister( )
    {
        $this->info("Setup register_shutdown_function, set_error_handler, set_exception_handler");

        // PHPのハンドラを置き換える
        register_shutdown_function([$this,'phpShutdownFunction']);
        set_error_handler([$this,'phpErrorHandler']);
        set_exception_handler([$this,'phpExceptionHandler']);

        return $this;
    }
    /**
     * PHPEceptionHandler
     */
    public function phpExceptionHandler ( $e )
    {
        $this->crit((string)$e, 'PHP|EXCEPTION');
    }

    /**
     * PHPErrorHandler
     */
    public function phpErrorHandler( $eno, $msg, $file, $line)
    {
        $level = Logging\Level::convertPHPErrorCode($eno, $name);

        $log = new Logging\Log(
            $level,
            $msg." File $file($line)",
            ['PHP',Logging\Level::convertLevelToString($level)]
        );

        $this->fireEvent('log', ['log'=>$log]);
    }

    /**
     * PHPShutdownFunction
     */
    public function phpShutdownFunction( )
    {
        if ($err = error_get_last()) {
            $this->phpErrorHandler(
                $err['type'],
                $err['message'],
                $err['file'],
                $err['line']
            );
        }

        $this->fireEvent('shutdown');
    }

}
