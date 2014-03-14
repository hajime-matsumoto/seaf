<?php

namespace SeafTest;

use Seaf\Application\Web\Base;


class Web extends Base
{
    /**
     * @SeafRoute /
     */
    public function index($req, $res) 
    {
        echo 'INDEX';
    }
}
