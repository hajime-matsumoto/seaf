<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

use Seaf\Wrapper;

/**
 * Webコントローラ
 */
class WebControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWebControllerInstance( )
    {
        $Controller = new WebController( );
        $this->assertInstanceOf(
            'Seaf\Web\WebController',
            $Controller
        );
    }

    /**
     * スーパーグローバルからリクエストが設定されているか
     */
    public function testSetupRequest( )
    {
        $Web = new WebController( );

        $g = Wrapper\SuperGlobalVars::getSingleton();
        $g->setVar('_SERVER.REQUEST_METHOD', 'GET');
        $g->setVar('_SERVER.REQUEST_URI', '/');
        $g->setVar('_REQUEST', [
            'param1' => 1,
            'param2' => 2
        ]);

        $this->assertEquals(
            'GET',
            $Web->Request()->getMethod()
        );

        $this->assertEquals(
            1,
            $Web->Request()->getParam('param1')
        );
    }

    /**
     * Viewをテストする
     */
    public function testView( )
    {
        $Web = new WebController( );
        $Web->loadComponentConfig([
            'View' => [
                'defaultTemplateMethod' => 'php',
                'dirs' => [__DIR__.'/views']
            ]
        ]);

        // Viewを有効化
        $Web->view( )->enable();

        $this->assertEquals(
            '<h1>SEAF</h1>',
            $Web->render('index', ['title'=>'SEAF'])
        );

        // レイアウトを有効化
        $Web->view( )->layout('layout.php');

        $this->assertEquals(
            '<html><h1>SEAF</h1></html>',
            $Web->render('index', ['title'=>'SEAF'])
        );
    }

    /**
     * TwigViewをテストする
     */
    public function testTwigView( )
    {
        $Web = new WebController( );
        $Web->loadComponentConfig([
            'View' => [
                'defaultTemplateMethod' => 'twig',
                'defaultTemplateExtension' => 'twig',
                'dirs' => [__DIR__.'/views']
            ]
        ]);

        // Viewを有効化
        $Web->view( )->enable();

        $this->assertEquals(
            '<html><h1>SEAF</h1></html>',
            trim(preg_replace('/\n/', '', $Web->render('index', ['title'=>'SEAF'])))
        );
    }
}
