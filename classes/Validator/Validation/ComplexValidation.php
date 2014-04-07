<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator\Validation;

/**
 * 複合
 */
class ComplexValidation extends Base
{
    private $validations = [];

    /**
     * バリデーションを作成する
     */
    public function __construct ($config, $validator)
    {
        parent::__construct($config, $validator);

        foreach ($config['valid'] as $k=>$v)
        {
            $this->validations[] = $validator->makeValidation($k, $v);
        }
    }

    /**
     * @param Value
     */
    protected function _valid ($value)
    {
        foreach ($this->validations as $v) {
            if(!$v->valid($value)) return false;
        }
        return true;
    }
}
