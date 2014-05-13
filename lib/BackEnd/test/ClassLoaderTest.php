<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

/**
 * レジストリモジュールのテスト
 */
class ClassLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegistry ( )
    {
        $m = Manager::getSingleton();

        // Registryモジュール
        $loader = $m->classLoader;


        $loader->addNamespace('a','/');

        $loader->explain();
    }
}
