<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * データベースモジュール
 */
namespace Seaf\Registry;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\BackEnd;
use Seaf\Logging;

/**
 * モジュールファサード
 */
class Facade extends BackEnd\Module\ModuleFacade
{
    public function __construct($datas = [])
    {
        $this->data = Util::Dictionary($datas);

    }

    public function get($name, $default = null)
    {
        $this->debug('REGISTRY',["GET %s",$name]);
        return $this->data->get($name, $default);
    }

    public function has($name)
    {
        return $this->data->has($name);
    }

    public function set($name, $value)
    {
        $this->debug('REGISTRY',["SET %s=%s",$name, $value]);
        $this->data->set($name, $value);
        return $this;
    }

    public function debugOn( )
    {
        $this->info('REGISTRY',"Debug On");
        $this->data->set('debug_flg', true);
    }

    public function debugOff( )
    {
        $this->info('REGISTRY',"Debug Off");
        $this->data->set('debug_flg', false);
    }

    public function internalEncoding($encode) {
        $this->info('REGISTRY',["internalEncoding %s",$encode]);
        mb_internal_encoding($encode);
    }

    public function language($locale) {
        $this->info('REGISTRY',["language %s",$locale]);
        mb_language($locale);
    }

    public function timezone($zone) {
        $this->info('REGISTRY',["timezone %s",$zone]);
        date_default_timezone_set($zone);
    }

    public function fifo($name, $mode = 0666)
    {
        if ($this->has($name)) {
            return $this->get($name);
        }

        $fifo = Util::Fifo($name, $mode);
        $this->data->set($name, $fifo);
        return $fifo;
    }

    public function isDebug()
    {
        return !$this->data->isEmpty('debug_flg');
    }

    // =======================================
    // PHPハンドラ系の処理
    // =======================================
    public function phpRegister( )
    {
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
        $this->crit('PHP|EXCEPTION', (string)$e);
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
