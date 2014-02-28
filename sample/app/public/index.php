<?php
/**
 * Executer
 */
require_once dirname(__FILE__).'/../../../vendor/autoload.php';
use Seaf\Seaf;

Seaf::system()->setLang('ja');
Seaf::logger()->addHandler(array('type'=>'PHPConsole'));

require_once '../app.php';
require_once '../admin.php';
$app = new App( );

/* ----- Config ---------*/
$app->registry()->set('admin.mail', "mail@hazime.org");

/* ----- Run ---------*/
Seaf::http()->router()->mount('/admin', new Admin());
Seaf::http()->router()->mount('/', $app);
Seaf::http()->run();
