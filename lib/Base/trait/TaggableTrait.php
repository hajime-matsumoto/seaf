<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * ベース
 */
namespace Seaf\Base;

use Seaf\Util\Util;

/**
 * タグ付けパターン
 */
trait TaggableTrait
    {
        private $tags;

        private function tags ( )
        {
            if (!$this->tags) {
                $this->tags = Util::Dictionary();
            }
            return $this->tags;
        }

        public function tag ($name, $value)
        {
            $this->tags( )->set($name, $value);
            return $this;
        }

        public function sprintTags($format = '%s:%s', $sep = ',')
        {
            $texts = [];
            foreach($this->tags() as $k=>$v)
            {
                if (is_array($v)) {
                    $v = implode($sep, $v);
                }
                $texts[] = sprintf($format, $k, $v);
            }
            return implode("\n", $texts);
        }

        public function hasTag($name)
        {
            return $this->tags()->has($name);
        }

        public function getTag($name, $default = null)
        {
            $data = $this->tags()->get($name, $default);
            if (!is_array($data)) {
                return $data;
            }
            return current($data);
        }

        public function isTag($name, $value)
        {
            if(!$this->hasTag($name)) {
                return false;
            }
            $v = $this->tags()->get($name);

            if (is_array($v)) {
                return array_search($value, $v) ? true: false;
            }

            return $v == $value;

        }
    }
