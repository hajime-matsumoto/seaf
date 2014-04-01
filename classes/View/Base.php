<?php
namespace Seaf\View;

use Seaf;
use Seaf\Pattern;
use Seaf\Data\Container;

/**
 * Viewコンポーネント
 */
class Base extends Container\ArrayContainer
{
    use Pattern\Environment;

    /**
     * レイアウトを使う場合はレイアウト名
     *
     * @var string|bool
     */
    protected $layout = false;

    /**
     * Viewエンジンインスタンスをキャッシュする配列
     *
     * @var array
     */
    protected $engines = array();

    /**
     * Viewのパスリスト
     *
     * @var array
     */
    protected $paths = array();

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->initEnvironment();
    }

    /**
     * Viewパスを追加
     *
     * @param string|array
     * @return Base
     */
    public function addPath ($path)
    {
        if (is_array($path)) {
            foreach ($path as $v) {
                $this->addPath($v);
            }
            return $this;
        }

        array_unshift($this->paths, $path);
        return $this;
    }

    /**
     * Viewのデータを取得する
     *
     * @return array
     */
    public function getParams ( )
    {
        return $this->data;
    }

    /**
     * Engineを取得する
     *
     * @param string {php|twig|smarty}など
     */
    public function getEngine ($name)
    {
        if (isset($this->engines[$name])) {
            return $this->engines[$name];
        }

        $class = __NAMESPACE__.'\\Engine\\'.ucfirst($name);
        return $this->engines[$name] = new $class($this);
    }

    /**
     * レイアウトを有効にする
     *
     * @param string|bool レイアウト名かbool
     * @return $this
     */
    public function layout($name = false)
    {
        if ($name == false) {
            $this->layout = false;
        } elseif ($name === true) {
            $this->layout = 'layout';
        } else {
            $this->layout = $name;
        }
        return $this;
    }

    /**
     * 実行結果を表示する
     *
     * @param string $tpl
     * @param array $datas
     */
    public function display ($tpl, $datas)
    {
        echo $this->render($tpl, $datas);
    }

    /**
     * 実行結果を取得する
     *
     * @param string
     * @param array
     * @return string
     */
    public function render ($tpl, $datas)
    {
        if ($this->layout) {
            $contents = $this->_render($tpl, $datas);
            $datas['contents'] = $contents;
            return $this->_render($this->layout, $datas);
        } else {
            return $this->_render($tpl, $datas);
        }
    }

    /**
     * 実行結果を取得する
     *
     * @param string
     * @param array
     * @return string
     */
    protected function _render ($tpl, $datas)
    {
        $tpl = Seaf::fileSystem($tpl);
        $ext = $tpl->ext();

        if ($ext === false) {
            $ext = 'php';
        }
        $datas = array_merge($this->toArray(), $datas);

        return $this->getEngine($ext)->render($tpl, $datas);
    }
}
