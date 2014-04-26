<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\CLI;

use Seaf\Controller;
use Seaf\Component;

/**
 * コントローラ
 */
class CLIController extends Controller\Controller
{
    /**
     * コントローラをイニシャライズする
     */
    protected function setupController ( )
    {
        parent::setupController ( );
    }

    /**
     * コンポーネントローダをセットアップする
     */
    protected function setupComponentLoader( )
    {
        parent::setupComponentLoader ( );

        $this->addComponentLoader(
            new Component\Loader\NamespaceLoader(
                __NAMESPACE__.'\\Component'
            )
        );
    }
}
