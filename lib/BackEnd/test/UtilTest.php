<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

use Seaf\Util\Util;

/**
 * Utilモジュールのテスト
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
    public function testUtil ( )
    {
        $m = Manager::getSingleton();
        $m->registry->phpRegister();

        // Utilモジュール
        $util = $m->util;
        $util->showModuleList( );

        // PHPFunctionのテスト
        $this->assertEquals('aaa', $util->phpFunction->sprintf("aaa"));
        $util->phpFunction->help();
        $util->phpFunction->set('sprintf', function () {
            return 'xyz';
        });
        $util->phpFunction->set('exit', function ($body) {
            return $body;
        });
        $this->assertEquals('xyz', $util->phpFunction->sprintf("aaa"));
        $this->assertEquals('aaa', $util->phpFunction->exit('aaa'));

        // Anotationのテスト
        $anot = $m->util->annotation;
        $anot->help();
        $this->assertInstanceOf(
            'Seaf\Util\Annotation\AnnotationContainer',
            $anot->build($m)
        );
    }

}
