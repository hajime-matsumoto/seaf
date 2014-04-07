<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator\Validation;

/**
 * E-mail
 */
class PatternValidation extends Base
{
    /**
     * 正規表現
     */
    private $pattern;

    /**
     * バリデーションを作成する
     */
    public function __construct ($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * 実行
     */
    protected function _valid ($value)
    {
        if (empty($value)) return true;

        return preg_match(
            $this->pattern,
            $value
        ) ? true: false;
    }
}
