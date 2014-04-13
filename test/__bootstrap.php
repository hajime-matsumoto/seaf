<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

if (!defined('SEAF_PROJECT_ROOT')) define('SEAF_PROJECT_ROOT',__DIR__);
if (!defined('SEAF_ENV'))  define('SEAF_ENV', 'development');

require_once __DIR__.'/../__bootstrap.php';

$Seaf = Seaf\Core\Seaf::init(SEAF_PROJECT_ROOT, $env = SEAF_ENV);
