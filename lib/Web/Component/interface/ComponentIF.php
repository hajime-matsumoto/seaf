<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web\Component;

use Seaf\Web;

/**
 * コンポーネントIF
 */
interface ComponentIF
{
    public function setupWebComponent(Web\WebController $Ctrl);
}
