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
        $t->useCache(false);
        $t->setCacheHandler(Seaf::Cache( )->section('message'));
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

        $this->assertEquals(
            'I am hajime',
            $t->translate('person.I_AM', 'hajime')
        );

        $this->assertEquals(
            'nobody',
            $t->translate('person.count', 0)
        );
        $this->assertEquals(
            'a person',
            $t->translate('person.count', 1)
        );
        $this->assertEquals(
            '10 people',
            $t->translate('person.count', 10)
        );
    }

}
