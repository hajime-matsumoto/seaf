<?php
/**
 * 環境
 */
namespace Seaf\Environment\Component;

use Seaf\Environment\Environment;
use Seaf;

/**
 * システムコンポーネント
 */
class SystemComponent {

    const HALT_MESSAGE = 'システムを停止しています';

    /**
     * @var bool
     */
    private $isFake = false;

    /**
     * @var Environment
     */
    private $env;

    public function __construct (Environment $env) {
        $this->env = $env;
    }

    /**
     * haltをダミーにする
     */
    public function useFake( ) {
        $this->isFake = true;
    }

    public function header($header) 
    {
        Seaf::debug('Header-Sent: '.$header);
        header($header);
    }

    public function halt ($body = null) {
        if (!$this->isFake) exit($body);

        echo $body;

        echo "\n".self::HALT_MESSAGE;
        if ($this->isFake) echo "[FAKE]";
    }

    public function out ($body)
    {
        $fp = fopen('php://output','w');
        fwrite($fp, $body);
        fclose($fp);
    }

    public function in()
    {
        $fp = fopen('php://input','r');
        $value = fread($fp,1024);
        fclose($fp);
        return $value;
    }

    public function execute($cmd, $buf = false)
    {
        if ($buf == true) {
            ob_start();
            system($cmd);
            return ob_get_clean();
        }else{
            system($cmd);
        }
    }
}
