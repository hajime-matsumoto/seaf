<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Message;

use Seaf\Cache;
use Seaf;

class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    private function getTranslator ( )
    {
    }

    /**
     * インスタンス生成可能か
     */
    public function testInitialize ( )
    {
        $t = new Translator( );

        $this->assertInstanceOf(
            'Seaf\Message\Translator',
            $t
        );
    }

    /**
     * ロードテスト
     */
    public function testLoadMessageDir ( )
    {
        $t = new Translator( );
        $t->setCacheHandler(Seaf::Cache( )->section('message'));
        $t->useCache(false);
        $t->setMessageDir(__DIR__.'/lang');

        $this->assertEquals(
            'はじめ',
            $t->translate('site.name')
        );

        $t->setLocale('en');

        $this->assertEquals(
            'Hajime',
            $t->translate('site.name')
        );
    }

}
