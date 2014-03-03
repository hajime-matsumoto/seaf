<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * MMIZUI管理用のモジュール
 */
class Admin extends App
{
    public function __construct( )
    {
        parent::__construct( );
        $this->router()->init();
        $this->event()->init();

        $this->router()->map('GET /', array($this,'showAdmin'));
        $this->router()->map('POST /login', array($this,'login'));
        $this->router()->map('POST /update_news', array($this,'updateNews'));
        $this->router()->map('GET /logout', array($this,'logout'));
        $this->event()->addHook('after.start',array($this,'changePathFilter'));
    }

    public function get($name)
    {
        return $this->registry()->get($name);
    }

    /**
     * 管理画面へのアクセス
     *
     * @SeafURL GET /*
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
                'news'=>$news
            )
        );
    }

    /**
     * ログイン認証
     *
     * @SeafURL /login
     * @SeafMethod POST
     */
    public function login()
    {
        $requested_password = $this->request()->get('password');

        if( $requested_password === 'deganjue' )
        {
            $_SESSION['authorized'] = true;
        }
        else
        {
            $_SESSION['authorized'] = false;
        }

        $this->redirect('/');
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
        $this->redirect('/');
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

        $this->redirect('/');
    }
}
