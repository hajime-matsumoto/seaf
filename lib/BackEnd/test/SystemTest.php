<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

use Seaf\Util\Util;

/**
 * Systemモジュールのテスト
 */
class SystemTest extends \PHPUnit_Framework_TestCase
{
    public function testSystem ( )
    {
        $m = Manager::getSingleton();
        $m->registry->phpRegister();

        // Configモジュール
        $system = $m->system;

        $system->help();
        $system->showModuleList( );
        $system->superGlobals->set('a','b')->set(['c'=>'d']);

        $this->assertEquals(
            'd',
            $system->superGlobals->get('c')
        );
    }

}
