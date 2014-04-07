<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator\Validation;

/**
 * 最小レンジ
 */
class MinLengthValidation extends Base
{
    /**
     * @param Value
     */
    protected function _valid ($value)
    {
        if (empty($value)) return true;

        if (strlen($value) > $this->config['min']) {
            return true;
        }
        return false;
    }
}
