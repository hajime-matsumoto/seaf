<?php
namespace Seaf\Tests;

use Seaf\Seaf;

class SystemTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        Seaf::getInstance();
    }

    public function testSystemComponent( )
    {
        $this->assertTrue(Seaf::di()->get('system') == Seaf::system());
        $this->assertTrue(Seaf::system() == Seaf::di('system'));

        Seaf::system()->fakeExit();

        ob_start();
        Seaf::system()->halt('システム終了');
        $result = ob_get_clean();

        $this->assertEquals('システム終了', $result);
    }

    public function testErrorHandling()
    {
        $emsg = "";
        Seaf::system()->setErrorHandler(function($no,$msg,$file,$line,$context) use (&$emsg){
           $emsg = $msg;
        });
        trigger_error('aaaa');
        $this->assertEquals('aaaa', $emsg);
    }

    public function testWebSetting()
    {
        Seaf::system()
            ->setLang('ja')
            ->errorReporting(E_ALL)
            ->displayErrors(true);
    }

}
