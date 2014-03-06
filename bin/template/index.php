<?php
/**
 * {{name}} プロジェクト エンドポイント
 *
 * URL: {{domain}}
 */
define('APP_ROOT', dirname(__FILE__).'/../');
require_once APP_ROOT.'/bootstrap.php';

define('APP_ENV', Seaf::ENV_DEVELOPMENT);
Seaf::config()->load(APP_ROOT.'/config/setting.yaml');

$rt = Seaf::web()->router();
$ev = Seaf::web()->event();

$rt->map('/(@page:*)',function ($page, $req, $res, $web) {

    if (empty($page)) {
        $page = 'index';
    }

    // 使用するテンプレートを宣言する
    $web->set('template', $page);
});

$ev->on('after.render', function ($web) {
    $template = $web->get('template');
    $web->render($template);
});

Seaf::web()->run();
