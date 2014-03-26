<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\P18n;

use Seaf;

/**
 * P18nのテスト
 */
class P18nTest extends \PHPUnit_Framework_TestCase
{
    /**
     * スタートアップ
     */
    protected function setUp()
    {
        Seaf::enmod('p18n');
    }

    /**
     * シャットダウン
     */
    protected function tearDown()
    {
    }

    public function testStandard ( )
    {
        $t = Seaf::p18n()->getHelper();

        $this->assertEquals(
            'Seaf -フレームワーク-',
            $t('site.title')
        );

        $this->assertEquals(
            'English Only Word Here',
            $t('english.only.word.here')
        );

        $this->assertEquals(
            '私の名前はHajimeです',
            $t('myname', 'Hajime')
        );

        $this->assertEquals(
            'なし',
            $t('count_pl', 0)
        );

        $this->assertEquals(
            'ひとつ',
            $t('count_pl', 1)
        );

        $this->assertEquals(
            '2個',
            $t('count_pl', 2)
        );

        $this->assertEquals(
            '3個',
            $t('count_pl', 3)
        );

        $this->assertEquals(
            '[[word.not.exists]]',
            $t('word.not.exists')
        );

        $this->assertEquals(
            'a,b,c',
            $t('list')
        );

        // 言語固定でヘルパを取得
        $en = Seaf::P18n()->getHelper('en');

        $this->assertEquals(
            'Seaf -FrameWork-',
            $en('site.title')
        );

        // グローバルヘルパに登録する
        foreach(Seaf::Helper()->getKeys() as $k) {
            $$k = Seaf::Helper()->get($k);
        }
        $this->assertEquals(
            $t('site.title'),
            $T('site.title')
        );
    }
}
