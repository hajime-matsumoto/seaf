<?php
use Seaf\Core\Kernel;

Kernel::init();

Kernel::fs()->addFilePath('/seaf',__DIR__);
Kernel::fs()->requireOnce('/seaf/Seaf.php');

Kernel::cl()->addNamespace('Seaf','/seaf');
