<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logger\Writer;

use Seaf\Exception;
use Seaf\Data\Container;
use Seaf\Kernel\Kernel;
use Seaf\Logger\Level;
use Seaf\Pattern;

/**
 * ログライタ
 */
abstract class Base
{
    use Pattern\Configure;

    protected $level = Level::ALL;

    /**
     * ファクトリメソッド
     *
     * @param array $config
     * @return Base
     */
    public static function factory ($config)
    {
        $c        = new Container\ArrayContainer($config);
        $type     = $c('type', 'echo');
        $class    = __NAMESPACE__.'\\'.ucfirst($type).'Writer';
        $instance = new $class($config);
        $instance->configure($config, false, true, array('type'));
        return $instance;
    }

    /**
     * レベルをセットする
     *
     * @param string|int
     */
    public function configLevel ($level)
    {
        if (is_int($level)) {
            $this->level = $level;
        } else {
            $this->configLevel(Level::parse($level));
        }
    }

    /**
     * ロガーから送られてくるデータ
     *
     * @param int $level
     * @param array $params
     * @param array $opts
     * @param string $tag
     * @param array $trace
     */
    public function post ($level, $params, $opts, $tag, $trace) 
    {
        if (0 === ($this->level & $level)) {
            return;
        }

        // 時間を取得
        $time = time();

        // レベルを名前に変換
        $level = Level::$map[$level];

        // メッセージを作成する
        $message = array_shift($params);
        if (is_array($message)) {
            $message = vsprintf(array_shift($message), $message);
        }
        foreach ($params as $k=>$p) {
            $message.= sprintf(' #ARG%02d %s', $k+1, print_r($p, true));
        }

        $this->_post($this->makeMessage(compact('time','level','message','tag','trace')));
    }

    abstract public function _post($message);


    /**
     * メッセージを作成する
     */
    protected function makeMessage($context)
    {
        extract($context);
        $log = sprintf('%s [%-9s] [%s] %s',
            date('Y-m-d G-i-s', $time),
            $level,
            substr($tag,0,15),
            $message
        );
        return $log;
    }

}
