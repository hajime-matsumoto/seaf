<?php
namespace Seaf\Console;

use Seaf\App\Application as BaseApplication;
use Seaf\Core\Kernel;

/**
 * コンソールアプリケーションクラス
 */
class Application extends BaseApplication
{
    private $descs = array();

    public function initApplication ( )
    {
        parent::initApplication();

        $argv = Kernel::rg()->get('argv');

        $this->request()->init(array(
            'method'=>'GET',
            'uri'=> isset($argv[1]) ? $argv[1]: 'usage'
        ));

        Kernel::ReflectionClass($this)->mapAnnotation(function($method, $anots){
            if (isset($anots['route'])) {
                $this->route($anots['route'], $method->getClosure($this));
            }
            if (isset($anots['desc'])) {
                $this->descs[$anots['route']] = $anots['desc'];
            }
            if (isset($anots['event'])) {
                $this->on($anots['event'], $method->getClosure($this));
            }
        });
    }

    public function exec($cmd, $buf = false)
    {
        $this->sys()->out('Exec Command: '.$cmd."\n");
        $return = $this->sys()->execute($cmd, $buf);
        $this->sys()->out("\n");
        return $return;
    }

    public function out ($body) 
    {
        $this->sys()->out($body."\n");
    }

    public function in ($body, $default) 
    {
        $body .= '['.$default.']';
        $this->sys()->out($body);
        $return =  $this->sys()->in();
        if (empty($return)) {
            return $default;
        }
    }


    public function _notfound( )
    {
        $argv = Kernel::rg()->get('argv');
        printf("Usage\n");
        printf("==============================\n");
        printf("%s\n", $argv[0]);
        foreach($this->descs as $k=>$v) {
            printf("\t%15s\t%s\n", $k, $v);
        }
    }
}
