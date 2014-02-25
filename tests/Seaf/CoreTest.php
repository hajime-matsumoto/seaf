<?php
namespace Seaf\Tests;

use Seaf\Seaf;

class SeafTest extends \PHPUnit_Framework_TestCase
{
    public function testReport() 
    {
        $instance = Seaf::getInstance( );
        $this->assertInstanceOf('Seaf\Seaf', $instance);

        ob_start();
        Seaf::report();
        $report = ob_get_clean();

        // echo $report;
        $this->assertStringStartsWith('===', trim($report));
    }


    public function testExtension() 
    {
        $instance = Seaf::getInstance( );
        $this->assertInstanceOf('Seaf\Seaf', $instance);

        // WEBエクステンションを有効にする
        Seaf::useExtension('web');

        // WEBエクステンションの機能を使ってみる

        // リクエストURIを取得
        $this->assertEquals('/', Seaf::getComponent('webRequest')->url);

        // filterを登録
        Seaf::before('webStart', function( $params, &$output ){
            echo '<html>';
        });
        Seaf::after('webStart', function( $params, &$output ){
            echo '</html>';
        });
        // routeを登録
        Seaf::webRoute('/', function(){
             echo 'hello world';
        });

        // 実行
        Seaf::webStart();

    }
}
