<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Container;

use Seaf\Util\Util;

class ArrayContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     */
    public function testCreateArrayContainer ( )
    {
        $c = new ArrayContainer( );

        $this->assertInstanceOf(
            'Seaf\Base\Container\ArrayContainer',
            $c
        );
    }

    /**
     * 
     */
    public function testSetAndGet ( )
    {
        $c = new ArrayContainer( );

        $c->set('hoge', 'huga');
        $c->set(['hogehoge'=>'hugahuga']);
        $huga = 'b';
        $c->set(['hogeref'=>&$huga]);

        $this->assertEquals('huga',$c->get('hoge'));
        $this->assertEquals('hugahuga',$c->get('hogehoge'));
        $this->assertEquals('empty',$c->get('hogehogehoge', 'empty'));

        $c->set('hogeref','abcde');
        $this->assertEquals('abcde',$huga);
    }

    /**
     * 
     */
    public function testHas ( )
    {
        $c = new ArrayContainer( );

        $c->set([
            'hoge' => 'huga',
            'ref' => &$huga
        ]);

        $this->assertTrue($c->has('hoge'));
        $this->assertTrue($c->has('ref'));
        $this->assertFalse($c->has('hogehoge'));
    }

    public function testIsEmpty ( )
    {
        $c = new ArrayContainer( );

        $c->set([
            'hoge' => 'huga',
            'ref' => &$huga
        ]);

        $this->assertFalse($c->isEmpty('hoge'));
        $this->assertTrue($c->isEmpty('ref'));
        $this->assertTrue($c->isEmpty('hogehoge'));
    }

    public function testMagicMethod ( )
    {
        $c = new ArrayContainer( );

        $c->hoge = 'huga';
        $this->assertEquals('huga',$c->get('hoge'));
        $this->assertEquals('huga',$c->hoge);
        $this->assertEquals('huga',$c('hoge'));
    }

    public function testCaceSensitive ( )
    {
        $c = new ArrayContainer(null, false);

        $c->HoGe = 'huga';
        $this->assertEquals('huga',$c->get('hOge'));
        $this->assertEquals('huga',$c->hogE);
        $this->assertEquals('huga',$c('HOGE'));
    }

    public function testToArray ( )
    {
        $c = new ArrayContainer(['a'=>'b']);
        $this->assertEquals(['a'=>'b'], $c->toArray());
    }

    public function testClear ( )
    {
        $c = new ArrayContainer(['a'=>'b']);
        $c->clear('a');
        $this->assertFalse($c->has('a'));
    }

    public function testAppend ( )
    {
        $c = new ArrayContainer(['a'=>'b']);
        $c->add('b', 1);
        $c->add('a', 1);
        $c->add('c', 1);
        $c->add('c', 2);
        $c->add('c', 3, true);
        $c->add('c', 4, true);

        $this->assertTrue(is_array($c->get('a')));
        $this->assertTrue(is_array($c->get('b')));
        $this->assertEquals(
            1,
            current($c->get('b'))
        );

        $this->assertEquals(
            '4,3,1,2',
            implode(',',$c->get('c'))
        );
    }

    public function testDump ( )
    {
        $c = new ArrayContainer();
        $c->set('Rec1', $c);
        $c->set('Rec2', $c);
        $c->set('Rec3', $c);
        $c->set(
            [
                'a'=>'b',
                'c'=>[
                    'd'=>'e',
                    'f'=>'g'
                ],
                'closure'=>function($a){
                    return "AAA";
                }
            ]
        );

        $return = $c->dump(true, 2);
        ob_start();
        $c->dump(false, 2);
        $this->assertEquals(
            $return, trim(ob_get_clean())
        );
    }
}
