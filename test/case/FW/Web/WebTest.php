<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Web;

use Seaf\Core\Seaf;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple ( )
    {
        Seaf::System()->map('halt', function ($body) {
            echo $body;
        });

        $c = new Controller( );
        $c->route('/', function() {
            echo 'test';
        });
        $c->run( );
    }

}
