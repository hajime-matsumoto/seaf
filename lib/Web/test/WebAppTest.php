<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

use Seaf\Web;

class TestWeb extends App\Base
{
    protected static $object_name = 'TestWeb';

    public function setupApplication( )
    {
        parent::setupApplication();

        $this->setupByAnnotation();

        $this->loadComponent('view')
            ->enable()
            ->addPath(__DIR__.'/views');
    }

    /**
     * @SeafRoute /index
     * @SeafRoute /a
     * @SeafRoute /b
     * @SeafRoute /c
     * @SeafRoute /
     */
    public function index ($req, $res)
    {
        $res->write('aaa');
        $this->render('index');
    }
}

/**
 * WEBアプリケーションのテスト
 */
class WebAppTest extends \PHPUnit_Framework_TestCase
{
    public function testRequest ( )
    {
        BackEnd('util')->phpFunction->set('exit', function ($body) use(&$out){
            $out = $body;
        });
        BackEnd('system')->superGlobals->runAll('set',[
            ['_SERVER.REQUEST_URI','/home/index?aa=bbb&cc=ddd'],
            ['_SERVER.REQUEST_METHOD', 'GET']
        ]);

        $web = BackEnd('web');

        $web->loadComponent('view')
            ->enable()
            ->addPath(__DIR__.'/views');

        $web->route('/home/index', function ($request, $response, $app) {
            $response->write("はははは");
            $app->render('index');
        });

        $web->run();

        $this->assertEquals(
            '<h1>はははは</h1>', trim($out));

        // マウント系のテスト
        foreach(['index','a','b','c'] as $p) {
            $web->loadComponent('request')->url( )->initPath('/test/'.$p);
            $web->mount('/test/', 'Seaf\Web\TestWeb');
            $web->run();
            $this->assertEquals(
                '<h1>aaa</h1>', trim($out));
        }
    }

}
