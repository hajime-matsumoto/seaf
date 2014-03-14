<?php
namespace Seaf\Logger;

use Seaf\Pattern\Configure;

/**
 * Loggerクラス
 */
class Logger
{
    use Configure;

    /**
     * Loggerの名前
     * @var string
     */
    public $name;

    /**
     * Logライターのリスト
     * @var array
     */
    protected $writers = array();

    /**
     * __construct
     *
     * @param array
     */
    public function __construct ($config = array())
    {
        $this->configure($config);
    }

    /**
     * 名前を変えたロガーを返す
     */
    public function __invoke ($name = null)
    {
        if ($name == null) return $this;
        return $this->get($name);
    }

    public function get($name)
    {
        static $list = array();

        if (!isset($list[$name])) {
            $list[$name] =  clone($this);
            $list[$name]->name = $name;
        }
        return $list[$name];
    }

    /**
     * post
     *
     * @param $context, $level
     * @return void
     */
    public function post ($context, $level, $tag = false)
    {
        $message = array_shift($context);
        $vars    = $context;
        $time    = time();
        $tag     = $tag === false ? $this->name: $tag;

        // 特定のレベルだったらデバッグトレースを渡す
        if ((Level::DEBUG | Level::ERROR | Level::EMERGENCY) & $level) {
            $trace   = array_slice(debug_backtrace(),1);
        }else{
            $trace = null;
        }

        if (is_array($message)) {
            $message = vsprintf(array_shift($message), $message);
        }

        foreach ($this->writers as $writer)
        {
            $writer->post(compact('message','tag', 'vars','time', 'trace'), $level);
        }
    }

    /**
     * debug
     *
     * @param $message
     * @return void
     */
    public function debug ($message)
    {
        $args = func_get_args();
        $this->post($args, Level::DEBUG);
    }

    /**
     * info
     *
     * @param $message
     * @return void
     */
    public function info ($message)
    {
        $args = func_get_args();
        $this->post($args, Level::INFO);
    }

    /**
     * Error
     *
     * @param $message
     * @return void
     */
    public function error ($message)
    {
        $args = func_get_args();
        $this->post($args, Level::ERROR);
    }
    /**
     * Warning
     *
     * @param $message
     * @return void
     */
    public function warning ($message)
    {
        $args = func_get_args();
        $this->post($args, Level::WARNING);
    }

    /**
     * 複数のライターをセットする
     * @param array
     */
    public function setWriters ($writers)
    {
        foreach ($writers as $name => $writer) {
            $this->setWriter($name, $writer);
        }
    }
    /**
     * ライターをセットする
     *
     * @param string
     * @param array
     */
    public function setWriter ($name, $writer)
    {
        // オブジェクトへ変換する
        $writer = Writer::factory($writer);
        $this->writers[$name] = $writer;
    }

    /**
     * register
     *
     * @param 
     * @return void
     */
    public function register ()
    {
        // PHP エラーを補足する
        set_error_handler(array($this, 'phpErrorHandler'));
        set_exception_handler(array($this, 'phpExceptionHandler'));
        return $this;
    }

    public function phpErrorHandler ($no, $mesg, $file, $line, $context)
    {
        $context = array($mesg." ".substr($file, -30).' '.$line);
        $level = Level::$php_error_map[$no];
        $this->post($context, $level, 'PHP');
    }

    public function phpExceptionHandler (\Exception $e)
    {
        $context = array((string) $e);
        $level = Level::EMERGENCY;
        $this->post($context, $level, 'PHP');
    }
}
