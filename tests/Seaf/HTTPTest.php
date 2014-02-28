<?php
namespace Seaf\Tests;

use Seaf\Seaf;

class HTTPTest extends \PHPUnit_Framework_TestCase
{

    public function testHttp()
    {
        // リクエストを立ち上げる
        $request = Seaf::http()->request();

        // レスポンスを立ち上げる
        $response = Seaf::http()->response();

        // ルータを立ち上げる
        $router = Seaf::http()->router();

        // ルートを定義する
        $router->addRoute('/', function(){
            echo 'hi';
        });

        // ルートを定義する
        $router->addRoute('/@page:*', function(){
            echo 'hi';
        });

        $app = new App();
        $app->router()->addRoute('/',function(){
            echo 'App:hi';
            return true;
        });

        $route = $router->route( $request );

        $request->setURL('/app');
        $route = $router->route( $request );
    }

}


class App extends \Seaf\Component\Http
{
}
