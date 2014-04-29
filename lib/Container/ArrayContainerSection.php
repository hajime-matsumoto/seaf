<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Container;

/**
 * 配列コンテナセクション
 */
class ArrayContainerSection extends ArrayContainer
{
    private $container;
    private $name;

    /**
     * @param ArrayContainer
     * @param string
     */
    public function __construct (ArrayContainer $container, $name)
    {
        $this->container = $container;
        $this->name = $name;
    }

    /**
     * データを取得する
     *
     * @param string
     * @param mixed
     * @return mixed
     */
    public function getVar ($key, $default = null)
    {
        $this->container->getVar($this->prefix($key), $default);
    }

    /**
     * データをセットする
     *
     * @param string
     * @param mixed
     * @return ArrayContainer
     */
    public function setVar ($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k=>$v) {
                $this->setVar($k, $v);
            }
            return $this;
        }
        $this->container->setVar($this->prefix($key), $value);
        return $this;
    }

    /**
     * プレフィックスを掛ける
     *
     * @param string
     * @return string
     */
    public function prefix($key)
    {
        return $this->name.".".$key;
    }
}
