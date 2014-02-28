<?php
namespace Seaf\Tests;

use Seaf\Seaf;

class RegistryTest extends \PHPUnit_Framework_TestCase
{

    public function testSetRegistry()
    {
        Seaf::registry()->set('name', 'hajime');
        $this->assertEquals('hajime', Seaf::getRegistry('name') );
    }

}
