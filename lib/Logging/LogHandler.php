<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging;


class LogHandler 
{
    use LoggingTrait;

    public function register ( )
    {
        set_error_handler([$this, 'phpErrorHandler']);
        set_exception_handler([$this, 'phpExceptionHandler']);
        register_shutdown_function([$this, 'phpShutdownFunction']);
    }

    /**
     * PHPEceptionHandler
     */
    public function phpExceptionHandler ( $e )
    {
        $this->logPost(new Log(
            Code\LogLevel::CRITICAL,
            $e->getMessage(), $params = [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ));
    }

    /**
     * PHPErrorHandler
     */
    public function phpErrorHandler( $eno, $msg, $file, $line)
    {
        $code = Code\LogLevel::convertPHPErrorCode($eno);
        $this->logPost(new Log($code, $msg, $params = [
            'file' => $file,
            'line' => $line
        ], $tags = ['php']));
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
    }
}
