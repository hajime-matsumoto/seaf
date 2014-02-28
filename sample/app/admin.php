<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Web Application
 */
require_once dirname(__FILE__).'/../../vendor/autoload.php';

use Seaf\Net\WebApp;


class Admin extends App
{
    /**
     * 出力前の調整フィルター
     *
     * @SeafHookOn stop before
     */
    public function changePathFilter( )
    {
        parent::changePathFilter();
    }

    /**
     * 管理画面へのアクセス
     *
     * @SeafURL /*
     * @SeafMethod POST|GET
     */
    public function showAdmin()
    {
        if( isset($_SESSION['authorized']) && $_SESSION['authorized'] === true ) 
        {
            $tpl = 'admin/index.twig';
        }
        else
        {
            $tpl = 'admin/login.twig';
        }
        $news = file_get_contents( $this->get('app.root')."/data/news.txt" );
        echo $this->twig->render( $tpl, 
            array(
                'base_url'=>$this->request->base,
                'news'=>$news
            )
        );
        return true;
    }

    /**
     * ログイン認証
     *
     * @SeafURL /login
     * @SeafMethod POST
     */
    public function login()
    {
        $requested_password = $this->request->getParam('password');

        if( $requested_password === 'deganjue' )
        {
            $_SESSION['authorized'] = true;
        }
        else
        {
            $_SESSION['authorized'] = false;
        }

        $this->web->redirect('/');
    }

    /**
     * ログアウト認証
     *
     * @SeafURL /logout
     * @SeafMethod GET
     */
    public function logout()
    {
        $_SESSION['authorized'] = false;
        $this->web->redirect('/');
    }

    /**
     * ニュース保存
     *
     * @SeafURL /update_news
     * @SeafMethod POST
     */
    public function updateNews()
    {
        if( $_SESSION['authorized'] === true )  {
            file_put_contents(
                $this->get('app.root')."/data/news.txt",
                $_POST['news']
            );
        }

        $this->web->redirect('/');
    }


}
