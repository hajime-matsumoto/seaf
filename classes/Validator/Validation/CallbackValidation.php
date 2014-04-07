<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator\Validation;

/**
 * コールバックバリデーション
 */
class CallbackValidation extends Base
{
    /**
     * @param Value
     */
    protected function _valid ($value)
    {
        $func = $this->config['method'];
        return $func($value);
    }
}
