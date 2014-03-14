<?php
namespace Seaf\Application\Web;

use Seaf\Application\Base as ApplicationBase;

/**
 * Webアプリケーション
 */
class Base extends ApplicationBase
{
    /**
     * 初期化処理
     */
    public function initApplication ()
    {
        $this->environment->di()->addComponentNamespace(__CLASS__);
    }
}
