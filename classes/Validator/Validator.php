<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator;

/**
 * バリデータ
 */
class Validator
{
    private $validators = [];

    /**
     * バリデーションを作成する
     */
    public function createNewValidator ($config = [])
    {
        $validator = new $this();
        return $validator;
    }

    /**
     * バリデーションを追加する
     */
    public function addValidation ($target, $validations, $value = [])
    {
        if (is_array($validations)) {
            foreach ($validations as $k=>$config) {
                $this->validators[$target][] = $this->makeValidation($k, $config);
            }
        }else{
            $this->validators[$target][] = $this->makeValidation($validations, $value);
        }
    }

    /**
     * バリデーションを作成する
     */
    public function makeValidation ($type, $config)
    {
        $class = __NAMESPACE__.'\\Validation\\'.ucfirst($type).'Validation';
        return new $class($config, $this);
    }

    /**
     * バリデートする
     */
    public function validate ($target, $value, &$message = null)
    {
        $message = [];
        if (isset($this->validators[$target])) {
            foreach ($this->validators[$target] as $validator) {
                if (!$validator->valid($value)) {
                    $message[$target][] = $validator->getMessageCode();
                }
            }
        }
        return empty($message) ? true: false;
    }
}
