<?php
/**
 * Seaf Project
 */

define('APP_ROOT', realpath(dirname(__FILE__).'/../'));

/**
 * Load Application
 */
require APP_ROOT.'/app.php';

$app = new App( Seaf::ENV_DEVELOPMENT );

$app->run();
