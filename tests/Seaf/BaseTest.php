<?php
namespace Seaf\Tests;

use Seaf\Core\Base;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->base = new Base();
    }

    public function testMapingMethod()
    {
        $base = $this->base;

        $base->map('helloWorld', function(){
            return 'Hello Wild';
        });

        $this->assertEquals('Hello Wild', $base->helloWorld());
    }

    public function testRegistory()
    {
        $base = $this->base;
        $base->set('name','seaf');
        $this->assertEquals('seaf', $base->get('name')); 
    }

    public function testExtensions()
    {
        $base = $this->base;
        $base->useExtension('err');
        $self = $this;
        $cnt = 0;
        $base->setErrorHandler(function()use(&$cnt){
            $cnt++;
        });
        ob_start();
        echo AAA;
        echo AAA;
        echo AAA;
        echo AAA;
        ob_end_clean();
        $this->assertEquals(4, $cnt);
    }

    public function testReports()
    {
        $base = $this->base;
        $base->useDebugMode();

        $dump = $base->dump();
        $this->assertTrue(is_array($dump['methods']));
    }
}
