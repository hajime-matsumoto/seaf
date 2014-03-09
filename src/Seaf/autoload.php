<?php
require_once __DIR__.'/Seaf.php';

Seaf\Loader\AutoLoader::factory(array(
    "namespaces" => array(
        "Seaf" => __DIR__
    )
))->register();
