<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\View;

use Seaf\Seaf;

/**
 * Viewコンポーネント
 */
class Twig
{
    private $view;
    private $twig;
    private $sufix = ".twig";

    public function setView( View $view )
    {
        $loader = new \Twig_Loader_Filesystem($view->registry()->get('view.path'));
        $twig = new \Twig_Environment($loader,array('cache'=>$view->registry()->get('cache.dir')));

        if( $view->registry()->get('app.env') != Seaf::ENV_PRODUCTION )
        {
            $twig->clearcachefiles();
        }

        $this->twig = $twig;
        $this->view = $view;
    }

    public function render( $file, $params = array() )
    {
        $params = $this->view->getMergedParams($params);
        echo $this->twig->render( $file, $params);
    }

}
