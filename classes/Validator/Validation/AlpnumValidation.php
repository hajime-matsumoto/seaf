<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator\Validation;

/**
 * 数字とアルファベットの複合
 */
class AlpnumValidation extends Base
{
    private $validations = [];

    /**
     * @param Value
     */
    protected function _valid ($value)
    {
        if (
            preg_match('/[1-9]/', $value) && preg_match('/[a-zA-Z]/', $value)
        ) {
            return true;
        }
        return false;
    }
}
