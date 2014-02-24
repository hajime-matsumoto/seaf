<?php
/**
 * Executer
 */
require_once '../app.php';

mb_language("japanese");
mb_internal_encoding("UTF-8");

$app = new App('development');

/* ----- Config ---------*/
$app->set('admin.mail', "mail@hazime.org");

/* ----- Run ---------*/
$app->run();
