<?php
/**
 * Executer
 */
require_once '../app.php';

$app = new App('development');

#$app->exten('web')->request->base = '/app';

$app->run();
