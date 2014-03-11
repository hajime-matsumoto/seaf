<?php
namespace Seaf\Log;

/**
 * 
 */
class Log
{
    private $name = 'default';

    // 呼び出しを許可するメソッド
    private static $methods = array(
        'emerg'    => Level::EMERGENCY,
        'alert'    => Level::ALERT,
        'critical' => Level::CRITICAL,
        'error'    => Level::ERROR,
        'warn'     => Level::WARNING,
        'info'     => Level::INFO,
        'debug'    => Level::DEBUG
    );

    /**
     * @var array
     */
    public $handlers = array();

    /**
     * __construct
     */
    public function __construct ()
    {
    }

    /**
     * post
     *
     * @param $context, $level = Level::INFO
     * @return void
     */
    public function post ($context, $level = Level::INFO)
    {
        $message = array_shift($context); # メッセージを取得

        $vars = !empty($context) ? $context: array();

        $log_context = array(
            'message' => $message,
            'vars'    => $vars,
            'level'   => Level::$map[$level],
            'time'    => time(),
            'name'    => isset($context['name']) ? $context['name']: $this->name
        );

        array_walk($this->handlers, function($handler) use ($log_context, $level){
            $handler->post($log_context, $level);
        });
    }

    /**
     * atachHandler
     *
     * @param $config
     * @return void
     */
    public function attachHandler ($key, $config)
    {
        $this->handlers[$key] = Handler::factory($config);
    }

    /**
     * detachHandler
     *
     * @param $config
     * @return void
     */
    public function detachHandler ($key)
    {
        unset($this->handlers[$key]);
    }

    public  function __call ($name, $params) 
    {
        if (array_key_exists($name, self::$methods)) {
            $this->post($params,self::$methods[$name]);
        }
    }

    /**
     * PHPエラーのハンドリング
     */
    public function register ( ) 
    {
        $self = $this;
        set_error_handler(function($no, $msg, $file, $line, $context) use ($self){
            $level = Level::$php_error_map[$no];
            $context = array(
                'level'   => Level::$map[$level],
                'message' => $msg.' '.substr($file,-25).' '.$line,
                'context' => $context,
                'time'    => time(),
                'name'    => "PHP",
                'vars'    => array()
            );
            $self->post($context,$level);
        });

        set_exception_handler(function(Exception $e){
            $context = array(
                'level'   => Level::CRITICAL,
                'message' => (string) $e,
                'context' => null,
                'name'    => 'EXCEPTION',
                'vars'    => array()
            );
            $this->post($context, Level::CRITICAL);
        });

        register_shutdown_function(function(){
            $isError = false;
            if ($error = error_get_last()){
                switch($error['type']){
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_CORE_WARNING:
                case E_COMPILE_ERROR:
                case E_COMPILE_WARNING:
                    $isError = true;
                    break;
                }
            }
            if ($isError){
                extract($error);
                $level = Level::$php_error_map[$type];
                $this->post(array(
                    'level' => Level::$map[$level],
                    'message' => $message,
                    'context' => null,
                    'time' => time(),
                    'vars' => array()
                ));
                $this->post($context, $level);
            }
        });
    }
}
