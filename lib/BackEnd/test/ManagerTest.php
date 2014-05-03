<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

use Seaf\Base\Command;

class A
{
    public function B ( )
    {
        return 'hogehoge';
    }
}

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     */
    public function testManagerGetSingleton ( )
    {
        $m = Manager::getSingleton( );

        $this->assertInstanceOf(
            'Seaf\BackEnd\Manager',
            $m
        );

    }

    public function testNewRequest ( )
    {
        $m = Manager::getSingleton( );

        $req = $m->newRequest( )
            ->scope('cache')
            ->name('cacheCreate')
            ->param(['cache_key', 'aaaaaa', $expires = 10]);

        $value = $req->execute();

        $this->assertEquals(
            $value->getReturnValue(),
            $m->cache->cacheCreate('cache_key','aaaaaa', $expires)
        );
    }
    public function testOther ( )
    {
        $m = Manager::getSingleton( );

        $c = $m->a->b->c;

        // エラー時の処理
        $m->on('before.returnResult', function($e) {
            if ($e->result->isError()) {
                $e->result->returnValue('えらーでしたｗ');
            }
        });

        var_dump($m->A->B());
        $m->on('before.loadHandler', function($e) {
            if ($e->className == 'A') {
                $e->className = 'Seaf\BackEnd\A';
            }
        });
        var_dump($m->A->B());

        var_dump($m->toppo->B());
        var_dump($m->toppo->help());
        /*

        var_dump($c->d());
        var_dump($c->ore->toppo->yoro->d->help());
        var_dump($c->web->help());
        var_dump($c->web->help());
        var_dump($c->web->help());
        var_dump($c->web->help());
        var_dump($m->A->B());
        var_dump($m->toppo->B());
         */
        //var_dump($m->seaf->backEnd->A->B());
    }

}
