<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View;

use Seaf\Event;

/**
 * View
 */
class View
{
    use Event\ObservableTrait;

    /**
     * @var string
     */
    private $layout = false;

    /**
     * @var dirs
     */
    private $dirs = [];

    /**
     * @var ViewModel
     */
    private $viewModel;

    /**
     * @var ViewModel
     */
    private $defaultViewModel;

    /**
     * デフォルトのエクステンション
     * @var string
     */
    private $defaultTemplateExtension = 'php';

    /**
     * デフォルトのメソッド
     * @var string
     */
    private $defaultTemplateMethod = 'php';

    /**
     * コンストラクタ
     */
    public function __construct ($cfg = [])
    {
        $cfg = seaf_container($cfg);


        // ----------------------------------------
        // コンフィグを元にセットアップする
        // ----------------------------------------

        // デフォルトテンプレートメソッド
        $cfg->mapVar('defaultTemplateExtension', function ($extension) {
            $this->setDefaultTemplateExtension($extension);
        });

        // デフォルトテンプレートメソッド
        $cfg->mapVar('defaultTemplateMethod', function ($method) {
            $this->setDefaultTemplateMethod($method);
        });

        // テンプレートディレクトリ
        $cfg->mapVar('dirs', function ($dir) {
            $this->addViewDir($dir);
        });

        // ----------------------------------------
        // Viewメソッドを指定する
        // ----------------------------------------
        $this->addViewMethod([
            'php' => [
                'class'      => __NAMESPACE__.'\\ViewMethod\\PHPMethod',
                'extentions' => ['php', 'phtml']
            ],
            'twig' => [
                'class'      => __NAMESPACE__.'\\ViewMethod\\TwigMethod',
                'extentions' => ['twig']
            ]
        ]);
    }

    /**
     * ViewMethodを追加する
     *
     * @param string
     * @param array
     * @return View
     */
    public function addViewMethod ($name, $config = false)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->addViewMethod($k, $v);
            return $this;
        }
        $cfg = seaf_container($config);
        $this->methods[$name] = $cfg('class');

        $cfg->mapVar('extentions', function ($ext) use($name) {
            $this->extentionMap[$ext] = $name;
        });
        return $this;
    }

    /**
     * ViewMethodを取得する
     *
     * @param string
     * @return ViewMethod
     */
    public function getViewMethod ($name)
    {
        if (!isset($this->extentionMap[$name])) {
            $method = $this->defaultTemplateMethod;
        }

        $method_name = $this->extentionMap[$name];

        if (!isset($this->methods[$method_name])) {
            throw new Exception\ViewMethodNotFound($name);
        }

        $method = $this->methods[$method_name];

        if (!is_object($method)) {
            $method = $this->methods[$method_name] = $this->buildViewMethod($method);
        }

        return $method;
    }

    /**
     * ViewMethodを作成する
     */
    protected function buildViewMethod($method)
    {
        $class = $method;
        $Method = new $class($this);
        return $Method;
    }

    /**
     * デフォルトテンプレートメソッドをセットする
     *
     * @param string
     * @return $this
     */
    public function setDefaultTemplateExtension ($extension)
    {
        $this->defaultTemplateExtension = $extension;
        return $this;
    }

    /**
     * デフォルトテンプレートメソッドをセットする
     *
     * @param string
     * @return $this
     */
    public function setDefaultTemplateMethod ($method)
    {
        $this->defaultTemplateMethod = $method;
        return $this;
    }

    /**
     * Viewディレクトリを追加する
     *
     * @param string
     * @return View
     */
    public function addViewDir($dir)
    {
        $this->dirs[] = $dir;
        return $this;
    }

    /**
     * ViewModelを作成する
     *
     * @return ViewModel
     */
    public function createViewModel ( )
    {
        if (!$this->defaultViewModel) {
            $vm = new ViewModel();
        } else {
            $vm = clone $this->defaultViewModel;
        }

        $vm->setMethod([
            'searchViewFilePath' => [$this, 'searchViewFilePath'],
            'getViewFileDirs' => [$this, 'getViewFileDirs']
        ]);

        $this->trigger('viewModel.crate', [
            'ViewModel' => $vm
        ]);
        return $vm;
    }

    /**
     * ViewFileDirectoryのリストを取得する
     */
    public function getViewFileDirs ( )
    {
        return $this->dirs;
    }

    /**
     * ViewModelをセットする
     *
     * @param ViewModel
     * @return View
     */
    public function setViewModel(ViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        return $this;
    }

    /**
     * ViewModelを取得する
     *
     * @return ViewModel
     */
    public function getViewModel ( )
    {
        if (isset($this->viewModel)) {
            return $this->viewModel;
        }
        return $this->createViewModel();
    }

    /**
     * DefaultViewModelを取得する
     *
     * @return ViewModel
     */
    public function getDefaultViewModel ( )
    {
        if (!isset($this->defaultViewModel)) {
            $this->defaultViewModel = new ViewModel();
        }
        return $this->defaultViewModel;
    }

    /**
     * ファイルパスを取得する
     */
    public function searchViewFilePath($file, &$path = null)
    {
        foreach ($this->dirs as $dir) {
            $file_path = $dir.'/'.$file;
            if (file_exists($file_path)) {
                $path = $file_path;
                return true;
            }
        }
        return false;
    }

    /**
     * ディスプレイ
     */
    public function display ($template, $vars)
    {
        echo $this->render($template, $vars);
    }

    /**
     * レンダー
     */
    public function render ($template, $vars = [], $noLayout = false)
    {
        if ($noLayout == false && $this->layout) {
            $viewContents = $this->render($template, $vars, true);

            $template = $this->layout;
            $vars['viewContents'] = $viewContents;
        }

        if (false === $p = strrpos($template, '.')) {
            $extension = $this->defaultTemplateExtension;
        }else{
            $extension = substr($template, $p+1);
            $template = substr($template, 0, $p);
        }

        // 検索対象のファイル
        $file = $template.'.'.$extension;

        // メソッドを取得する
        $Method = $this->getViewMethod($extension);
        return $Method->render($file, $this->getViewModel()->mergeVar($vars));
    }

    /**
     * レイアウト
     */
    public function layout ($name)
    {
        $this->layout = $name;
        return $this;
    }
}
