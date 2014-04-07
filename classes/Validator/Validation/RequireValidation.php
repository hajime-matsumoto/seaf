<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator\Validation;

/**
 * 必須チェック
 */
class RequireValidation extends Base
{
    /**
     * @param Value
     */
    protected function _valid ($value)
    {
        if (empty($value)) {
            return false;
        }
        return true;
    }
}
