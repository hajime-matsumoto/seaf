<?php

namespace App;

use Seaf\Application\Base;

class Admin extends Base
{
    public function initApplication ( )
    {
    }

    /**
     * @SeafRoute /index
     */
    public function index($req, $res)
    {
        $res->param('admin','yes');
        echo '!!!!ADMIN!!!!';
    }
}
