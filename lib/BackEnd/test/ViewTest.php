<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

/**
 * Viewモジュールのテスト
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testView ( )
    {
        $m = Manager::getSingleton();
        $m->registry->phpRegister();

        $m->showModuleList();

        $view = $m->view;
        $view->help();

        $view->addPath(__DIR__.'/views');

        $this->assertEquals(
            '<h1>seaf</h1>', trim($view->render('index', ['yield'=>'seaf']))
        );

    }

}
