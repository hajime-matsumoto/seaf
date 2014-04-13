<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Base;

class Storage extends Seaf\Storage\Storage
{
    use ComponentTrait;
    use Base\SeafAccessTrait;

    public function __construct ( $cfg = [])
    {
        parent::__construct();

        // コンポーネントファクトリにあげる
        $this->component()->loadConfig($cfg);
    }

    protected function _componentHelper ($name, $type)
    {
        return $this->getHandler($name, $type);
    }

}
