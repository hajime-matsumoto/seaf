<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * モジュール
 */
namespace Seaf\Base\Module;

use Seaf\Util\Util;
use Seaf\Base\Proxy;

/**
 * モジュールコンテナオーナ
 */
trait ModuleContainerTrait
    {
        private $moduleContainer;

        /**
         * コンテナの取得
         */
        protected function moduleContainer()
        {
            if (!$this->moduleContainer) {
                $this->moduleContainer = Util::Dictionary([
                    'instance' => [],
                    'define'   => []
                ]);
                $this->moduleContainer->caseSensitive(false);
            }
            return $this->moduleContainer;
        }

        /**
         * モジュールの登録
         */
        public function registerModule($name, $class = null, $args = [])
        {
            if (is_array($name)) {
                foreach ($name as $k => $v) {
                    if(is_array($v)) {
                        $class = $v[0];
                        $args = isset($v[1]) ? $v[1]: [];
                    }else{
                        $class = $v;
                        $args = [];
                    }
                    $this->registerModule($k, $class, $args);
                }
                return $this;
            }

            // 先頭に自分を追加
            array_unshift($args, $this);
            $this->debug("Module Registered >>> $name = $class <<<");
            $this->moduleContainer()->dict('define')->set($name, [$class,$args]);
            return $this;
        }

        /**
         * モジュール一覧を表示する
         */
        public function showModuleList( )
        {
            printf("\n".'*** [ Module List ] >>> %s <<<'."\n", $this->getObjectName());
            printf("-------------------------------------\n");
            foreach ($this->moduleContainer()->dict('define') as $k=>$v)
            {
                printf('>>> [ %s ] : %s <<<'."\n", $k, $v[0]);
            }
            printf("-------------------------------------\n");
        }

        /**
         * モジュールがあるか
         */
        public function hasModule($name)
        {
            $ins = $this->moduleContainer()->dict('instance');
            $def = $this->moduleContainer()->dict('define');

            if ($ins->has($name) || $def->has($name)) {
                return true;
            }
            return false;
        }

        /**
         * モジュールを呼び出す
         */
        protected function loadModule($input_name)
        {
            $name = $input_name;
            $ins = $this->moduleContainer()->dict('instance');
            $def = $this->moduleContainer()->dict('define');

            if ($ins->has($name)) return $ins->get($name);
            if (!$def->has($name)) {
                $this->warn("Can't Load Module >>> $input_name <<<");
                $this->showModuleList();
                return false;
            }

            list($class, $args) = $def->get($name);
            $module = Util::ClassName($class)->newInstanceArgs($args);
            $module->tag('object_name', $input_name);

            $this->info(["Module Loaded >>> %s <<<", $input_name]);

            $ins->set($name, $module);

            return $module;
        }

    }
