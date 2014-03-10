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

    public function setLang ($lang)
    {
        mb_language( $lang );
        mb_internal_encoding( 'utf8' );
        return $this;
    }

    /**
     * haltをダミーにする
     */
    public function useFake( ) {
        $this->isFake = true;
    }

    /**
     * ヘッダを送信する
     *
     * @param string $header
     * @param bool $replace
     * @param int $code
     */
    public function header( $header, $replace = true,  $code = false )
    {
        if( $code !== false )
        {
            header( $header, $replace, $code );
        }
        else
        {
            header( $header, $replace );
        }
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