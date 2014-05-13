<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Command;

class TestHandler extends RequestHandler
{
    public function recieve(RequestIF $request)
    {
        if ($request('method') == 'doError')
        {
            return $request->result()->error('SOME_ERROR');
        }

        $result = $request->result();

        $result->addReturnValue('h');
        $result->addReturnValue('e');
        $result->addReturnValue('l');
        $result->addReturnValue('l');
        $result->addReturnValue('o');
        $result->addReturnValue('hello');
    }
}


class RequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     */
    public function testMakeRequest ( )
    {
        $h = new TestHandler();
        $req = $h->makeRequest();

        $this->assertInstanceOf(
            'Seaf\Base\Command\Request',$req
        );
    }

    /**
     * 
     */
    public function testMakeRequestExecute ( )
    {
        $h = new TestHandler();
        $res = $h->test->execute('sayHello');
        $this->assertFalse($res->isError());

        $res = $h->test->execute('doError');
        $this->assertTrue($res->isError());
        $this->assertEquals(
            'SOME_ERROR',
            $res->error[0]['code']
        );
    }

    /**
     * 
     */
    public function testMakeRequestAsProxyCommand ( )
    {
        $h = new TestHandler();
        $ret = $h->test->sayHello();
        $this->assertEquals('hello', $ret);
    }
}
