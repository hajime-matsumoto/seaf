<?php

namespace SeafTest;

use Seaf\Application\Web\Base;


class Web extends Base
{
    /**
     * 継承したメソッド用に初期化メソッドを残す
     */
    public function initApplication( )
    {
        // Viewを使う
        $this->view( )->enable();

        // レイアウトを使う
        $this->view( )->layout();
    }

    /**
     * @-SeafEvent before.dispatch-loop
     */
    public function onStart($req, $res) 
    {
        if ($req->uri == '/') {
            $req->uri = '/home';
        }
    }

    /**
     * @SeafRoute /
     */
    public function index($req, $res) 
    {
        echo 'INDEX';
    }
}
