<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\DI;

use Seaf\Helper\ArrayHelper;

class Factory extends ArrayHelper
{
    /**
     * オートロード
     */
    public $autoload_list = array();

    public function __construct ()
    {
    }

    /**
     * 登録する
     */
    public function register ($name, $definition, $opts = null, $callback = null)
    {
        $this->set($name, Definition::factory(compact('definition','opts','callback')));
    }

    /**
     * オートロードを登録する
     */
    public function addAutoLoad ($prefix, $suffix)
    {
        $this->autoload_list[] = array($prefix, $suffix);
    }

    /**
     * hasをオーバーライド
     */
    public function has ($name)
    {
        if (parent::has($name)) return true;

        foreach($this->autoload_list as $autoload) {
            list($prefix, $suffix) = $autoload;

            $class = sprintf('%s%s%s',$prefix,ucfirst($name),$suffix);
            if (class_exists($class)) {
                $this->register($name, $class);
                return true;
            }
        }
        return false;
    }
}
