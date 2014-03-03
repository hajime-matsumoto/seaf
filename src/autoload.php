<?php
/**
 * Seaf Auto Loader
 */
use Seaf\Core\Autoloader;

require_once dirname(__FILE__).'/Seaf/Core/Autoloader.php';
require_once realpath(dirname(__FILE__).'/../vendor').'/autoload.php';

Autoloader::init();
