<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator\Validation;

/**
 * E-mail
 */
class EmailValidation extends PatternValidation
{
    /**
     * バリデーションを作成する
     */
    public function __construct ( )
    {
        parent::__construct(
            '/^[a-zA-Z0-9]{1}[^@]*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/'
        );
    }
}
