<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

require_once __DIR__.'/__Bootstrap.php';

use Seaf\Logging;

Seaf('ClassLoader')->addLibrary(__DIR__.'/lib');

Seaf('Logger')->on('log.post', function ($e) {
    echo $e->getVar('log')->toString()."\n";
});

// CLIControllerの取得
$cli = Seaf('CLIController');

// ログハンドラの作成
$logHandler = new Logging\LogHandler('Local');

// 通常のログを記述する
$writer = Logging\Writer::factory([
    'type'      => 'FileSystem',
    'fileName'  => '/tmp/seaf.cli.log',
    'writeMode' => 'a'
])
->addLevelFilter('ALL')
->attach($logHandler);

$cli->setLogHandler($logHandler);


// 標準出力先を設定
$cli->setStdout('php://stdout');

// コマンドをセットアップ
$cli
    ->mount('test', 'SeafCLI\App\Test')
    ->mount('install', 'SeafCLI\App\Installer')
    ->mount('secure', 'SeafCLI\App\Secure')
    ->on('notfound', function ($e) {
        $Ctrl = $e->getVar('Ctrl');
        $Req = $e->getVar('Request');
        $Ctrl->stdout('Command '.$Req->getPath().' Not Found'."\n");
    });

$cli->run();
