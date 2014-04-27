<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\CLI;

use Seaf\Controller;
use Seaf\Component;
use Seaf\Wrapper;
use Seaf\Event;

/**
 * コントローラ
 */
class CLIController extends Controller\Controller
{
    private $stdout;

    /**
     * 標準出力を指定する
     */
    public function setStdout($file)
    {
        $this->stdout = $file;
    }

    /**
     * 出力する
     */
    public function stdout($body)
    {
        $fp = fopen($this->stdout, 'a');
        fwrite($fp, $body);
        fclose($fp);
    }

    /**
     * 出力する
     */
    public function outln($body = null)
    {
        $this->stdout($body."\n");
    }

    /**
     * 出力する
     */
    public function outlines($lines = [], $prefix = '')
    {
        foreach ($lines as $line) {
            $this->outln($prefix.$line);
        }
    }

    /**
     * ShellOut
     */
    public function shellOut($shell, &$output = null, &$return_val = null)
    {
        $this->debug($shell, null, ['SHELL']);
        exec($shell, $output, $return_val);
        $this->debug(implode("\n", $output), null, ['SHELL']);

        $this->outln();
        $this->outln(' < '.$shell);
        $this->outln('----------------------');
        foreach ($output as $line) {
            $this->outln(' > '. $line);
        }
        $this->outln();

        return $return_val;
    }

    /**
     * SuperUserか否か
     */
    public function isSuperUser( )
    {
        if ('root' == $this->whoAmI()) {
            return true;
        }
        return false;
    }

    public function whoAmI( ) 
    {
    }

    /**
     * コントローラをイニシャライズする
     */
    protected function setupController ( )
    {
        parent::setupController ( );
        $this->on('before.mount.run', [$this, '_BeforeMountRun']);
    }

    /**
     * マウントを走らせる前の処理
     */
    protected function _BeforeMountRun(Event\Event $e)
    {
        $Ctrl = $e->getVar('Ctrl');

        // 出力先を共有
        $Ctrl->setStdout($this->stdout);
    }

    /**
     * コンポーネントローダをセットアップする
     */
    protected function setupComponentLoader( )
    {
        parent::setupComponentLoader ( );

        $this->addComponentLoader(
            new Component\Loader\NamespaceLoader(
                __NAMESPACE__.'\\Component'
            )
        );
    }

    /**
     * CLI用のリクエストをセットアップする
     */
    public function initRequest( )
    {
        $Request = parent::initRequest( );
        $g = Wrapper\SuperGlobalVars::getSingleton();
        if($g->hasVar('argv.2')) {
            $Request->importQueryString($g('argv.2'));
        }
        $Request->path($g('argv.1'));
        return $Request;
    }
}
