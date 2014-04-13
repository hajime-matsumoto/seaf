<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core;

use Seaf\DI;
use Seaf\Container;
use Seaf\Config;
use Seaf\Util;
use Seaf\Util\FileSystem;
use Seaf\Event;

class ComponentContainer extends DI\Container
{
    protected static function ns ( )
    {
        return __NAMESPACE__;
    }

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        parent::__construct();

        // initXXXファクトリを使う
        $this->addFactory(new DI\Factory\MethodBaseFactory($this));

        // ネームスペースベースファクトリを使う
        $this->addFactory(new DI\Factory\NamespaceBaseFactory(static::ns().'\\Component'));
    }

    /**
     * コンフィグコンポーネント
     */
    public function initConfig( )
    {
        $reg = $this->get('reg');
        return Config\Config::factory([
            'dir' => 'config'
        ]);
    }

    /**
     * ファイルシステムコンポーネント
     */
    public function initFileLoader( )
    {
        $fs = new FileSystem\Loader( );
        $fs->addPath($this->get('reg')->get('root'));
        return $fs;
    }

    /**
     * レジストリ
     */
    public function initReg( )
    {
        return new Container\ArrayContainer();
    }

    /**
     * イベントコンポーネント
     */
    public function initEvent( )
    {
        return new Event\Observable($this);
    }

    /**
     * Web
     */
    public function initWeb( )
    {
        return new \Seaf\FW\Web\Controller( );
    }

}
