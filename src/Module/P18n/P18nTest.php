<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-12 at 20:31:23.
 */
class P18nTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var P18n
     */
    protected $object;

    public function setup() 
    {
        Seaf::enmod('p18n');
    }

    /**
     */
    public function testBoot()
    {
        $this->assertInstanceOf(
            'Seaf\Module\P18n\P18n',
            Seaf::p18n()
        );
    }

    /**
     */
    public function testGet()
    {
        Seaf::p18n()->setLang('ja');
        $this->assertEquals(
            'Seaf -フレームワーク-',
            (string) Seaf::p18n()->site->title
        );

        Seaf::p18n()->setLang('en');
        $this->assertEquals(
            'Seaf -FrameWork-',
            (string) Seaf::p18n()->site->title
        );
    }

    /**
     * 代替
     */
    public function testGetFromDefaultLang()
    {
        Seaf::p18n()->setLang('ja');
        $this->assertEquals(
            '日本',
            (string) Seaf::p18n()->onlyjp
        );
        Seaf::p18n()->setLang('en');
        $this->assertEquals(
            '日本',
            (string) Seaf::p18n()->onlyjp
        );
    }

    /**
     * フォーマット文字列
     */
    public function testFormatter()
    {
        $this->assertEquals(
            '私の名前ははじめです',
            (string) Seaf::p18n()->myname('はじめ')
        );
    }
    /**
     * カウント
     */
    public function testCount()
    {
        $this->assertEquals(
            'ひとつ',
            Seaf::p18n()->count_pl(1)
        );
        $this->assertEquals(
            '2個',
            Seaf::p18n()->count_pl(2)
        );
        $this->assertEquals(
            'なし',
            Seaf::p18n()->count_pl(0)
        );
    }

    /**
     * ヘルパー
     */
    public function testHelper()
    {
        $env = new Seaf\Environment\Environment();
        $env->p18n()->importHelper($env);

        $this->assertEquals(
            'Seaf -FrameWork-',
            $env->t()->site->title
        );
    }
}