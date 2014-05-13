<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * ベース
 */
namespace Seaf\Base;

use Seaf\Util\Util;

/**
 * コンフィグ
 */
trait ConfigureTrait
    {
        public function configs($name = null)
        {
            if ($name == null) {
                if (!$this->configs) {
                    $this->configs = Util::Dictionary();
                }
                return $this->configs;
            }else{
                return $this->configs()->get($name);
            }
        }

        public function configure($configs, callback $callback = null)
        {
            $this->configs()->init($configs);

            if (!$callback) {
                call_user_func($callback);
            }
        }
    }
