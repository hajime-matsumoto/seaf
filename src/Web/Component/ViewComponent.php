<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web\Component;

use Seaf\App;
use Seaf\Core\Environment;
use Seaf\Templator\Templator;

/**
 * WEBアプリケーション
 * Viewコンポーネント
 * ===========================
 */
class ViewComponent
{
    private $env;

    public function initComponent(Environment $env)
    {
        $this->env = $env;
    }
    /**
     * Viewを有効化
     */
    public function enable()
    {
        $this->env->on('before.run', function(){
            ob_start();
        });
        $this->env->on('after.run', function($req, $res, $app){
            echo $res->getBodyClean();
            $contents = ob_get_clean();
            $params = $res->getParams();
            $params['contents'] = $contents;
            $template = $app->get('template');
            $type = substr($template,strrpos($template,'.')+1);

            $templator = Templator::factory(array(
                'type' => $type,
                'dirs' => array('/views')
            ));

            $res
                ->status(200)
                ->write($templator->render($template, $params))
                ->send();
        },true);
    }
}
