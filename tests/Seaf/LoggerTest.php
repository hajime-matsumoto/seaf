<?php
namespace Seaf\Tests;

use Seaf\Seaf;

class LoggerTest extends \PHPUnit_Framework_TestCase
{

    public function testLogger()
    {
        Seaf::logger()->addHandler(
            array(
                'type'=>'buffer',
                'level'=>'debug'
            )
        );

        Seaf::logger()->fatal( 'fatal message' );
        Seaf::logger()->err( 'err message' );
        Seaf::logger()->warn( 'warn message' );
        Seaf::logger()->info( 'info message' );
        Seaf::logger()->debug( 'debug message' );
    }

}
