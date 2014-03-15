<?php
namespace Seaf\Application\Component;

use Seaf\View\View as ViewBase;
use Seaf\Environment\Component\ComponentIF;

use Seaf\Kernel\Kernel;
use Seaf\Environment\Environment;
use Seaf\Application\Base;

/**
 * Viewコンポーネント
 */
class View extends ViewBase implements ComponentIF
{
    /**
     * @var Environment
     */
    private $env;


    public function initComponent(Environment $env)
    {
        $this->env = $env;
    }

    /**
     * 有効化
     */
    public function enable ( )
    {
        // Viewが有効な時は以下のメソッドを
        // オーバーライドする
        //
        // - afterdispatchloop
        // - beforedispatchloop
        $this->env->bind($this, array(
            'beforeDispatchLoop' => 'beforeDispatchLoop',
            'afterDispatchLoop'  => 'afterDispatchLoop'
        ));

        // Viewの設定を取得する
        if ($dir = $this->env->config('dirs.views')) {
            $this->addViewDir($dir);
        }
    }

    /**
     * Before Dispatch Loop
     */
    public function beforeDispatchLoop ($req, $res)
    {
        ob_start();
    }

    /**
     * After Dispatch Loop
     */
    public function afterDispatchLoop ($req, $res, Base $app)
    {
        $contents = ob_get_clean();

        $params =  $this->toArray();

        // コンテンツを入れる
        $params['contents'] = $contents;

        // ヘルパを入れる
        $tpl = $app->get('template', 'index');

        $res
            ->init()
            ->write($this->render($tpl, $params))
            ->send();
    }
}

