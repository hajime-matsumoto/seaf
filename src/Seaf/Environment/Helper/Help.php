<?php
/**
 * 環境ヘルパ
 */
namespace Seaf\Environment\Helper;

/**
 * Helpが使えるようにします
 * ----
 */
class HelpHelper {

    private $target;

    public function __construct ( $target  = null ) {
        $this->target  = $target;
    }
    public function __invoke($target = null) {
        return new HelpHelper($target);
    }

    public function __toString( )
    {
        if ($this->target instanceof \Closure) {
            return \ReflectionFunction::export($this->target, true);
        }
    }

    public function __call ($name, $params) {
        if (method_exists($this,"_".$name)) {
            call_user_func_array(array($this,"_".$name), $params);
            return $this;
        }
        throw new \Exception('Invalid Method '.$name);
    }
}
