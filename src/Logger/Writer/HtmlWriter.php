<?php
namespace Seaf\Logger\Writer;

/**
 * ログをHTMLで書きこむクラス
 */
class HtmlWriter extends EchoWriter
{
    private $logs = array();


    public function _post($context, $level)
    {
        $log = $this->logs[] = $this->_makeMessage($context, $level);
    }

    public function display( )
    {
        echo implode("<br />", $this->logs);
    }

    public function shutdownWriter( )
    {
        $this->display();
    }
}
