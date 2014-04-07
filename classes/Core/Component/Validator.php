<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Exception;
use Seaf\Validator\Validator as Base;

/**
 * バリデータ
 */
class Validator extends Base
{
    /**
     * 作成するメソッド
     *
     * @param array
     */
    public static function componentFactory ( )
    {
        return new self();
    }

    public function helper($config = null)
    {
        if ($config == null) return $this;
        return $this->createNewValidator($config);
    }

}
