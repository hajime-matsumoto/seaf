<?php
namespace Seaf\DI\Exception;

class DefinitionInvalid extends \Exception {

    public function __construct ( ) {
        $context = func_get_args();

        parent::__construct($this->parseContext($context));
    }

    private function parseContext ($context) {
        foreach ($context as $k=>$value) {
            if (is_callable($value)) {
                $context[$k] = '{closure}';
            }

            if (is_object($value)) {
                $context[$k] = $value;
                continue;
            }

            if (is_array($value)) {
                $context[$k] = $this->parseContext($value);
            }
        }

        return var_export($context, true);
    }
}
