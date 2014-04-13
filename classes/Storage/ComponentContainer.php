<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Storage;

use Seaf\DI;

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

}
