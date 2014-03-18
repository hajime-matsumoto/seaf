<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Environment\Component;

use Seaf\Event\Base;
use Seaf\Environment;

class Event extends Base
{
    private $env;

    /**
     * Environmentを受け入れる
     *
     * @param Environment\Base
     */
    public function acceptEnvironment (Environment\Base $env)
    {
        $this->env = $env;
    }

    /**
     * ハンドラを加工する
     *
     * @param mixed $handler コールバック
     * @return mixed
     */
    protected function fixHandler ($handler)
    {
        if (is_string($handler)) {
            return array($this->env, $handler);
        }
        return parent::fixHandler($handler);
    }
}
