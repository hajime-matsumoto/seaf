<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database;

use Seaf\Base\Command;
use Seaf\Base\Event;
use Seaf\Logging;

/**
 * テーブル定義リクエスト
 */
class DefineRequest extends Command\Request
{
    public function field($name, $type, $size = -1)
    {
        $this->add(
            'fields',
            [
                'name' => $name,
                'type' => $type,
                'size' => $size
            ]
        );

        return $this;
    }

    public function index ($name)
    {
        $this->add(
            'indexes', [
                'name' => $name
            ]
        );
        return $this;
    }

    public function primary_index ($name)
    {
        $this->set('primary_index', $name);
        return $this;
    }

    public function option ($name, $value)
    {
        $this->dict('options')->set($name, $value);
        return $this;
    }
}
