<?php
namespace Seaf\View;

use Seaf\Kernel\Kernel;
use Seaf\Data\Container;

/**
 * Viewコンポーネント
 */
class View extends Container\Base
{
    /**
     * Viewディレクトリ
     * @var array
     */
    protected $view_dir = array();

    /**
     * デフォルトテンプレータ名
     * @var string
     */
    protected $templator = 'php';

    /**
     * ヘルパーコンテナ
     * @var HelperContainer
     */
    protected $helprContainer;

    /**
     * レイアウトを使うか
     */
    protected $layout = false;

    public function __construct ( )
    {
        $this->initView( );
    }

    /**
     * Viewを初期化する
     */
    public function initView( )
    {
        // ヘルパーコンテナを初期化する
        $this->helprContainer = new HelperContainer($this);
    }

    /**
     * レイアウトを有効にする
     *
     * @param string
     * @return void
     */
    public function layout ($layout = 'layout')
    {
        $this->layout = $layout;
    }

    /**
     * Templatorを取得する
     *
     * @param string
     * @return Templator\Templator
     */
    private function getTemplator ($name = null)
    {
        if ($name == null) {
            $name = $this->templator;
        }

        return Templator\Templator::factory(array(
            'engine' => $name,
            'dirs'   => $this->view_dir
        ));
    }


    /**
     * Viewディレクトリを追加する
     *
     * @param $path
     * @return void
     */
    public function addViewDir ($path)
    {
        $this->view_dir[] = Kernel::FileSystem($path);
    }

    /**
     * 描画データを取得する
     *
     * @param string
     * @param array
     * @return string
     */
    public function render ($tpl, $params = array())
    {
        $params['helper'] = $this->helprContainer;
        if ($this->helprContainer->has('p18n')) {
            $params['t'] = $this->helprContainer->get('p18n');
        }

        $contents =  $this->getTemplator( )->render($tpl, $params);

        if ($this->layout) {
            $params['contents'] = $contents;
            $contents =  $this->getTemplator( )->render($this->layout, $params);
        }

        return $contents;
    }
}

