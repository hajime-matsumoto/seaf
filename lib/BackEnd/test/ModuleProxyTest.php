<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

use Seaf\Database;

/**
 * プロキシリクエストのテスト
 */
class ModuleProxyTest extends \PHPUnit_Framework_TestCase
{
    public function testRequest ( )
    {
        $m = Manager::getSingleton();

        // レジストリモジュール
        $reg = $m->registry;

        $this->assertTrue($reg->get('debug_flg'));
        $this->assertTrue($reg->isDebug());
    }

}
