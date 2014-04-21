<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Yahoo;

use Seaf\DI;

/**
 * Yahooモジュール用のコンポーネントコンテナ
 */
class ComponentContainer extends DI\Container
{
    private $Env;

    protected static function ns ( )
    {
        return __NAMESPACE__;
    }

    /**
     * コンストラクタ
     *
     * @param Environment $env
     */
    public function __construct (Environment $Env)
    {
        $this->Env = $Env;

        parent::__construct();

        // initXXXファクトリを使う
        $this->addFactory(new DI\Factory\MethodBaseFactory($this));

        // ネームスペースベースファクトリを使う
        $this->addFactory(new DI\Factory\NamespaceBaseFactory(static::ns().'\\Component'));

        $this->on('create', [$this, 'onCreate']);
    }

    /**
     * コンポーネントがacceptyahooenvironmentを
     * 実装していれば呼ぶ
     *
     * @param Event\Event
     */
    public function onCreate ($e) 
    {
        $instance = $e->instance;

        if (method_exists($instance, 'acceptYahooEnvironment')) 
        {
            $instance->acceptYahooEnvironment($this->Env);
        }
    }

    /**
     * オークション
     */
    public function initAuction ( )
    {
        return new Auction\Auction( );
    }
}
