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
        private $configs;

        public function configs($name = null)
        {
            if ($name == null) {
                if (!$this->configs) {
                    $this->configs = Util::Dictionary();
                }
                return $this->configs;
            }else{
                return $this->configs( )->get($name);
            }
        }

        /**
         * @param array
         * @param array 初期値
         * @param callback 設定後に呼ばれる
         */
        public function configure($configs, $default = [], $callback = null)
        {
            $configs = array_merge($default, $configs);

            $this->configs()->init($configs);

            foreach ($configs as $k=>$v) {
                if (method_exists($this, $method = '_config'.$k)) {
                    call_user_func([$this, $method], $v);
                }
            }

            if ($callback) {
                call_user_func($callback);
            }
        }

        public function showConfigList( )
        {
            echo Util::dump($this->configs()->__toArray());
        }
    }


