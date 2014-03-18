<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Environment;

use Seaf\Helper;
use Seaf\Pattern;
use Seaf\Kernel\Kernel;

class Base extends Helper\ArrayHelper
{
    use Pattern\DynamicMethod;

    /**
     * @var DI\Di
     */
    private $di;

    /**
     * Cunstruct
     */
    public function __construct ( )
    {
        // -----------------------------
        // DIを作成
        // -----------------------------
        $this->di = $di = DI\Container::factory(
            array(
                'name'     => 'Environment::DI',
                'owner'    => $this,
                'parent'   => Kernel::DI(),
                'autoload' => array(
                    'prefix' => __NAMESPACE__.'\\Component\\',
                    'suffix' => ''
                )
            )
        );

        // -----------------------------
        // マップ
        // -----------------------------
        $this->bind($this->event(), array(
            'on' => 'on',
            'off' => 'off',
            'trigger' => 'trigger'
        ));
    }

    /**
     * __call出来なかった時によばれるメソッド
     *
     * @param string $name
     * @param array $params
     * @return mixed
     */
    protected function callFallBack ($name, $params)
    {
        return $this->di->call($name, $params);
    }
}
