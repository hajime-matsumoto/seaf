<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Types;


/**
 * ディクショナリ型のテスト
 */
class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function setup( )
    {
        $this->dict = new Dictionary([
            'str' => 'abcdefg',
            'array' => ['key1' => 'keyA']
        ]);
    }

    public function testCreate ( )
    {
        $dict = new Dictionary();
    }

    public function testSet ( )
    {
        $dict = new Dictionary();
        $dict->set('key1','value1');
        $this->assertEquals(
            'value1',
            $dict->get('key1')
        );
    }

    public function testGet ( )
    {
        $this->assertEquals(
            'abcdefg',
            $this->dict->get('str')
        );
    }

    public function testClearAndHas ( )
    {
        $this->dict->set('key2', 'v2');

        $this->assertEquals('v2', $this->dict->get('key2'));
        $this->assertTrue($this->dict->has('key2'));
        $this->dict->clear('key2');
        $this->assertFalse($this->dict->has('key2'));
    }

    public function testIsEmpty ( )
    {
        $this->dict->set('key1', 'value');
        $this->dict->set('key2', '');
        $this->dict->set('key3', 0);
        $this->dict->set('key4', []);
        $this->dict->set('key5', false);
        $this->dict->set('key6', null);

        $this->assertFalse($this->dict->isEmpty('key1'));
        $this->assertTrue($this->dict->isEmpty('key2'));
        $this->assertTrue($this->dict->isEmpty('key3'));
        $this->assertTrue($this->dict->isEmpty('key4'));
        $this->assertTrue($this->dict->isEmpty('key5'));
        $this->assertTrue($this->dict->isEmpty('key6'));
    }

    public function testArrayAccess ( )
    {
        $this->assertEquals(
            $this->dict->get('str'),
            $this->dict['str']
        );

        $this->dict['key2'] = 'aaaaaa';
        $this->assertEquals(
            $this->dict->get('key2'),
            $this->dict['key2']
        );
        $this->assertTrue(isset($this->dict['key2']));

        unset($this->dict['key2']);
        $this->assertTrue($this->dict->isEmpty('key2'));
        $this->assertFalse($this->dict->has('key2'));
    }

    public function testIterator ( )
    {
        $data = [];
        foreach ($this->dict as $k=>$v)
        {
            $data[$k] = $v;
        }

        $this->assertEquals(
            [
                'str' => 'abcdefg',
                'array' => ['key1' => 'keyA']
            ],
            $data
        );
    }

    public function testPrepend ( )
    {
        $dict = new Dictionary( );
        $dict->prepend('a',1);
        $dict->prepend('a',2);
        $dict->prepend('a',3);
        $this->assertEquals(3,count($dict->get('a')));

        $dict = new Dictionary( );
        $dict->prepend(1);
        $dict->prepend(2);
        $dict->prepend(3);
        $this->assertEquals(3,$dict[0]);
    }

    public function testDict ( )
    {
        $dict = new Dictionary( );
        $dict->dict('a')->set('b','c');

        $this->assertEquals(['b'=>'c'], $dict->get('a'));
    }
}
