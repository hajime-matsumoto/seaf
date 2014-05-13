<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * ロギングモジュール
 */
namespace Seaf\Logging\Writer;

use Seaf\Logging;
use Seaf\Util\Util;
use Seaf\Base\Module;
use Seaf\Base\ConfigureTrait;

/**
 * モジュールファサード
 */
class SocketWriter
{
    use ConfigureTrait;

    private $handler;
    private $soc;
    private $filter;
    private $formatter;

    /**
     * コンストラクタ
     */
    public function __construct (Logging\LogHandler $handler, array $setting)
    {
        $this->handler = $handler;

        $this->configure($setting, [
            'address' => '/tmp/log',
            'filter' => [],
            'formatter' => []
        ]);

        $this->soc = socket_create(AF_UNIX, SOCK_DGRAM, 0);
        $this->sendText('[ START ]');
    }

    public function filter( )
    {
        if (!$this->filter) {
            foreach ($this->configs()->filter as $type => $setting) {
                $class = Util::ClassName('Seaf\Logging\Filter', $type.'Filter');
                if ($this->filter) {
                    $this->filter->add($class->newInstance($setting));
                }else{
                    $this->filter = $class->newInstance($setting);
                }
            }

            if (!$this->filter) {
                $this->filter = new Logging\Filter\NoopFilter([]);
            }
        }
        return $this->filter;
    }

    public function formatter( )
    {
        if (!$this->formatter) {
            $type = 'Default';
            $class = Util::ClassName('Seaf\Logging\Formatter', $type.'Formatter');
            $this->formatter = $class->newInstance($this->configs()->formatter);
        }
        return $this->formatter;
    }

    public function sendText($text)
    {
        $texts = explode("\n", $text);
        foreach($texts as $text) {
            socket_sendto(
                $this->soc, $text."\n", MSG_EOF, 1024, $this->configs()->address
            );
        }
    }
    public function logPost($log)
    {
        if (!$this->filter()->filter($log)) {
            return false;
        }

        $text = $this->formatter()->format($log);

        $this->sendText($text);
    }

    public function __destruct( )
    {
        $this->sendText('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
        socket_close($this->soc);
    }

}
