<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource;

use Seaf;
use Seaf\Base;

class ComponentContainer extends DI\Container
{
    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        parent::__construct();

        // initXXXファクトリを使う
        $this->addFactory(new DI\Factory\MethodBaseFactory($this));

        // ネームスペースベースファクトリを使う
        $this->addFactory(new DI\Factory\NamespaceBaseFactory(__NAMESPACE__.'\\Component'));
    }
}
