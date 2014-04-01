<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Session;

use Seaf;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testDSN ( )
    {
    }

    public function testSession ( )
    {
        $session = Handler::factory([
            'type' => 'fileSystem',
            'path' => '/tmp/seaf.session'
        ]);

        $id = $session->start(1);
        $this->assertEquals(1, $id);
        $session['test'] = 1;

        $session->sessionStore();
        $newid = $session->regenerateID();

        $file = Seaf::FileSystem('/tmp/seaf.session/'.$newid);

        $this->assertTrue(
            $file->isExists()
        );

        $this->assertTrue(
            1 == $session['test']
        );
    }
}
