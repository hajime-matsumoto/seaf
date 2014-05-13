<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

use Seaf\Util\Util;

/**
 * コンフィグモジュールのテスト
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testRequest ( )
    {
        $m = Manager::getSingleton();

        // Configモジュール
        $config = $m->config;
        $config->loadConfigDir(__DIR__.'/configs');

        $config->help();

        $this->assertEquals('ja', $config->setup->getConfig('lang'));
    }

}
