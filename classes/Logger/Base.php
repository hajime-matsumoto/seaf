<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logger;

use Seaf;
use Seaf\Pattern;
use Seaf\Exception;

/**
 * ロガー
 * ------------------------------------------
 *
 * ロガーに対してライターは複数存在する
 */
class Base
{
    use Pattern\Factory;

    public $name = 'Default';

    /**
     * ライターの保持
     * @var array
     */
    protected $writers = array();

    /**
     * 子ハンドラの保持
     * @var array
     */
    protected $handlers = array();

    /**
     * 許可するメソッドのマップ
     * @var array
     */
    protected static $map = array(
       'emerg' => Level::EMERGENCY ,
       'alert' => Level::ALERT     ,
       'crit'  => Level::CRITICAL  ,
       'err'   => Level::ERROR     ,
       'warn'  => Level::WARNING   ,
       'info'  => Level::INFO      ,
       'debug' => Level::DEBUG
   );

    // -------------------------------
    // コンフィグ
    // -------------------------------

    public function configWriters($writers)
    {
        foreach ($writers as $name => $writer) {
            $this->addWriter($name, $writer);
        }
    }

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
    }

    /**
     * ::mapにある名称のメソッドであれば
     * エラーレベルを設定してポストする
     *
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function __call ($name, $params)
    {
        if (array_key_exists($name, self::$map)) {
            $level = self::$map[$name];
            $this->post($level, $params);
        } else {
            throw new Exception\InvalidCall($name, $this);
        }
    }

    /**
     * ログをポストする
     *
     * 登録されているライターのpostを呼び出す
     *
     * @param int $level メッセージのレベル
     * @param array $params POSTに渡ってきた時の引数
     * @param array $opts メッセージのオプション
     * @param string $tag
     * @param array $trace
     */
    protected function post ($level, $params, $opts = array(), $tag = null, $trace = array())
    {
        if (empty($trace)) {
            $trace = debug_backtrace(false);
            $call = array_shift($trace);
            $call = array_shift($trace);
        }
        if (empty($tag)) $tag = $this->name;

        $this->_post($level, $params, $opts, $tag, $trace);
    }

    /**
     * ログをポストする (実体)
     *
     * 登録されているライターのpostを呼び出す
     *
     * @param int $level メッセージのレベル
     * @param array $params POSTに渡ってきた時の引数
     * @param array $opts メッセージのオプション
     * @param string $tag
     * @param array $trace
     */
    protected function _post ($level, $params, $opts = array(), $tag = null, $trace = array())
    {

        foreach ($this->writers as $writer) {
            $writer->post($level, $params, $opts, $tag, $trace);
        }
    }

    /**
     * ログライターを登録する
     *
     * @param Writer|array ライタオブジェクトか設定
     * @exception Exception\InvalitArguments
     */
    public function addWriter ($name, $writer = false)
    {
        if ($writer == false) {
            $writer = $name;
            $name = 'default';
        }

        if ($writer instanceof Writer\Base) {
            $this->writers[$name] = $writer;
        } elseif (is_array($writer)) {
            $this->writers[$name] = Writer\Base::factory($writer);
        } else {
            throw new Exception\InvalidArguments("$writerは配列でもSeaf\Logger\Writerありません。");
        }
    }

    /**
     * 名前付きハンドラを取得する
     *
     * @param string $name
     * @return Handler
     */
    public function getHandler ($name)
    {
        if (isset($this->handlers[$name])) {
            return $this->handlers[$name];
        } else {
            $this->handlers[$name] = new Handler($name, $this);
            return $this->handlers[$name];
        }
    }

    /**
     * register
     * @return void
     */
    public function register ()
    {
        // PHP エラーを補足する
        set_error_handler(array($this, 'phpErrorHandler'));
        set_exception_handler(array($this, 'phpExceptionHandler'));
        // 終了処理を設定する
        register_shutdown_function(array($this, 'phpShutdownFunction'));
        return $this;
    }

    /**
     * PHPエラーのハンドラ
     *
     * @param int
     * @param string
     * @param string
     * @param string
     */
    public function phpErrorHandler ($no, $mesg, $file, $line)
    {
        $context = array($mesg." ".substr($file, -30).' '.$line);
        $level = Level::$php_error_map[$no];
        $this->post(
            $level,
            array($mesg." ".substr($file, -30).' '.$line),
            'PHP'
        );
    }

    /**
     * PHP例外のはんどら
     *
     * @param \Exception
     */
    public function phpExceptionHandler (\Exception $e)
    {
        $context = array((string) $e);
        $level = Level::EMERGENCY;
        $this->post($level, $context, 'PHPException');
    }

    /**
     * 終了処理
     */
    public function phpShutdownFunction ( )
    {
        if (!is_null($e = error_get_last())) {
            $this->phpErrorHandler(
                $e['type'],
                $e['message'],
                $e['file'],
                $e['line'],
                null
            );
            $this->debug("エラー終了しました");
        } else {
            $this->debug("正常終了しました");
        }

        // 全ハンドラをシャットダウンする
        foreach ($this->writers as $key => $writer) {
            $writer->shutdown();
            unset($this->writers[$key]);
        }
    }
}
