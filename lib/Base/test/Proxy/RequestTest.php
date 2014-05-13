<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Proxy;

class TestHandler implements ProxyHandlerIF
{
    public function __proxyRequestGet(ProxyRequestIF $request, $name)
    {
        // Getされたらモジュール名をセット
        $next_request = clone $request;
        $next_request->addParam('module', $name);
        return $next_request;
    }

    public function __proxyRequestCall(ProxyRequestIF $request, $name, $params)
    {
        $result = new ProxyResult();
        $result->set(implode('/',$request->getParam('module')).':'.$name.':'.$params[0]);
        return $result;
    }
}

/**
 * プロキシリクエストのテスト
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testSet ( )
    {
        $handler = new TestHandler( );
        $request = new Request ( );
        $request->setHandler($handler);

        $aaa = $request->aaa;
        $bbb = $aaa->bbb;
        $ccc = $bbb->ccc;

        var_dump($aaa->test('1'));
        var_dump($bbb->test('1'));
        var_dump($ccc->test('1'));
        var_dump($ccc->dddd->eee->ffff->test('1'));
        var_dump($ccc->dddd->test('1'));
        var_dump($ccc->dddd->test('1'));
        var_dump($ccc->dddd->test('1'));
        var_dump($ccc->dddd->test('1'));
    }

}
