<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator\Validation;

/**
 * 最大レンジ
 */
class MaxLengthValidation extends Base
{

    /**
     * @param Value
     */
    protected function _valid ($value)
    {
        if (empty($value)) return true;

        if (strlen($value) < $this->params[0]) {
            return true;
        }
        return false;
    }
}
