<?php
namespace Seaf\View;

use Seaf;
use Seaf\Util\FileSystem;
use Seaf\Base;
use Seaf\Container\ArrayContainer;

/**
 * Viewコンポーネント
 */
class View extends ArrayContainer
{
    use Base\ComponentCompositeTrait;
    use Base\RecurseCallTrait;
    /**
     * レイアウトを使う場合はレイアウト名
     *
     * @var string|bool
     */
    protected $layout = false;


    /**
     * Viewのパスリスト
     *
     * @var array
     */
    protected $paths = array();

    protected $engines = array();
    public $loader;

    /**
     * コンストラクタ
     */
    public function __construct ($cfg = [])
    {
        $this->setComponentContainer('Seaf\View\ComponentContainer');

        $this->loader = new FileSystem\Loader($this->paths);

        if (isset($cfg['path'])) {
            $this->addPath($cfg['path']);
        }
    }

    /**
     * Viewパスを追加
     *
     * @param string|array
     * @return Base
     */
    public function addPath ($path)
    {
        if ($this->recurseCallIfArray($path, __FUNCTION__, false)) return $this;
        $this->loader->addPath($path);
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

        $ext = FileSystem\Helper::getExt($tpl);

        if ($ext === false) {
            $ext = 'php';
        }

        $datas = array_merge($this->toArray(), $datas);

        return $this->getEngine($ext)->render($tpl, $datas);
    }
}
