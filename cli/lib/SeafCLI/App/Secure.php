<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace SeafCLI\App;

use Seaf\CLI;
use Seaf\Secure\Util as SecureUtil;

class Secure extends CLI\CLIController
{
    public function setupController ( )
    {
        parent::setupController();
        $this->setupByAnnotation();
    }

    /**
     * ランダムな文字列を取得する
     *
     * @SeafRoute /random
     */
    public function random ($Request)
    {
        $length = $Request->getParam('length', 8);
        $this->outln(
            SecureUtil::randomString($length)
        );
    }


}
