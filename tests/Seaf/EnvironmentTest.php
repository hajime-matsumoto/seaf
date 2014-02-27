<?php
namespace Seaf\Tests;

use Seaf\Seaf;
use Seaf\Core\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function setup( )
    {
        $this->env = new Environment();
    }

    public function testMappingHelper( )
    {
        $env = $this->env;


        $env->map('firstMethod', function(){ return 'hi'; });

        try
        {
            $env->map('firstMethod', function(){ return 'hi'; });
        }
        catch( \Seaf\Exception\MethodAlreadyExists $e )
        {
            $this->assertTrue( true );
        }

        $this->assertEquals( 'hi', $env->call('firstMethod') );
    }

    public function testMappingWithArgs()
    {
        $env = $this->env;
        $env->map('methodWithArgs', function( $a , $b){ return $a + $b; });
        $this->assertEquals( '5', $env->call('methodWithArgs', 2, 3) );
    }

    public function testUnDefinedCallOverride()
    {
        $env = $this->env;
        $env->remap('unDefinedCall', function( $name ){ return $name; });
        $this->assertEquals( 'undefined', $env->call('undefined') );
    }

    public function testDependencyInjection()
    {
        $env = $this->env;
        $env->initializeEnvironment();

        $env->addFactory('config', function( ){
            return array('id'=>1);
        });

        $config = $env->retrieve('config');
        $this->assertEquals( 1, $config['id']);
    }
}
