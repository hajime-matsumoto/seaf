<?php
/**
 * Bootstrap Form {{name}}
 */
require_once '{{SeafRoot}}/src/autoload.php';

Seaf::di('autoLoader')->addNamespace('{{namespace}}',null,'{{lib}}/{{namespace}}');

Seaf::di('autoLoader')->addLibraryPath('{{lib}}');
