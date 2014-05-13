<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Registry;

use Seaf\Base\Module;
use Seaf\Base\Command;
use Seaf\Util\Util;
use Seaf\Logging;

/**
 * 
 */
class RegistryFacade extends Module\Facade
{
    public function __construct ($config = [])
    {
        $this->data = Util::ArrayContainer($config);

        register_shutdown_function([$this,'phpShutdownFunction']);
        set_error_handler([$this,'phpErrorHandler']);
        set_exception_handler([$this,'phpExceptionHandler']);
    }

    public function isDebug( )
    {
        return $this->data->get('debugFlg');
    }

    /**
     * Seaf Shutdown
     */
    public function shutdown ( )
    {
        $this->fireEvent('shutdown');
    }

    /**
     * PHPEceptionHandler
     */
    public function phpExceptionHandler ( $e )
    {
        $this->crit(
            'PHP_EXCEPTION', $e->getMessage()
        );
    }

    /**
     * PHPErrorHandler
     */
    public function phpErrorHandler( $eno, $msg, $file, $line)
    {
        $level = Logging\Level::convertPHPErrorCode($eno, $name);

        $log = new Logging\Log(
            $level,
            'PHP_ERROR',
            $msg." File $file($line)",
            $tags = ['php'],
            [
                'file' => $file,
                'line' => $line
            ]
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

        $this->shutdown( );
    }
}
