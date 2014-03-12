<?php

namespace App;

use Seaf\Application\Base;

class Root extends Base
{
    public function initApplication ( )
    {
        $this->di()->register('am','Seaf\Application\AssetManager\Application');
        $this->di()->register('admin','App\Admin');
    }

    /**
     * @SeafRoute  /index
     */
    public function index ($req, $res)
    {
        $res->param('template','index');
    }
}
