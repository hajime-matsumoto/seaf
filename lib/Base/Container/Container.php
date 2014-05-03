<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container;

/**
 * コンテナ
 */
abstract class Container implements ContainerIF
{
    /**
     * コンストラクタ
     */
    public function __construct ($array = [])
    {
        $this->setVars($array);
    }

    /**
     * 値を格納する(複数)
     *
     * @param Traversable
     * @return self
     */
    public function setVars (array $array)
    {
        foreach ($array as $k=>$v) {
            $this->setVar($k, $v);
        }
        return $this;
    }

    /**
     * 値を格納する(単品)
     */
    abstract public function setVar($name, $value);

    /**
     * 値を取得する
     */
    abstract public function getVar($name, $default = null);

    /**
     * 値を削除する
     */
    abstract public function clearVar($name);
}
