<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

use Seaf\FrameWork;
use Seaf\Commander\Command;
use Seaf\Helper\ArrayHelper;

/**
 * Application
 */
class Application extends FrameWork\Application
{
    public function initApplication ( )
    {
        parent::initApplication( );

        $server = ArrayHelper::init($_SERVER);

        // ベースURLを求める

        // パスインフォを使っている場合
        // どっちつかずの場合はどうするか？
        // /index.php で終わってる場合
        if ($server->get('SCRIPT_NAME') == $server->get('REQUEST_URI')) {
            $this->set('base.uri', $server->get('SCRIPT_NAME'));
        } elseif ($server->get('PATH_INFO', false) != false) {
            $this->set('base.uri',$server->get('SCRIPT_NAME'));
        } else {
            $this->set('base.uri',dirname($server->get('SCRIPT_NAME')));
        }
    }

    public function run ( )
    {
        $req = $this->request();
        $res = $this->response();
        $rt = $this->router();

        $executed = false;

        if ($this->getConfig('view.enable') == true) {
            $this->debug('view.enable true');
            $this->on('pre.run',function(){
                ob_start();
            });
            $this->on('post.run',function(){
                $contetns = ob_get_clean();
                $this->render($contents);
            });
        }


        $this->trigger('pre.run', $req, $res, $this);

        while ( $route = $rt->route($req) )
        {
            $isContinue = $route->getCommand()->execute($req, $res, $this);

            if ($isContinue == false) {
                $executed = true;
                break;
            }

            $this->router()->next();
        }
        $this->trigger('post.run', $req, $res, $this);

        if ($executed == false) {
            $this->trigger('notfound', $req, $res, $rt, $this);
            $this->notfound();
        }
    }

    public function render($contents)
    {
        $view = $this->get('template');

        $array = $this->response()->toArray();
        $params = $array['params'];
        $params['contents'] = $contents;

        echo $this->view()->render($view, $params);
    }
}
