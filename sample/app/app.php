<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Web Application
 */
require_once dirname(__FILE__).'/../../vendor/autoload.php';


use Seaf\Http\WebApp;
use Seaf\Seaf;

class App extends WebApp
{
    /**
     * @var object
     */
    protected $twig;

    public function __construct( )
    {
        parent::__construct( );
        $this->registry()->set('app.root', dirname(__FILE__));
        $this->initTwig();

        $this->router()->addRoute('/(@page:*)', array($this,'showPage'));
        $this->router()->addRoute('PUT /sendMail', array($this,'sendMail'));
        $this->event()->addHook('after.start',array($this,'changePathFilter'));
    }

    /**
     * Initialize
     */
    public function initTwig( )
    {
        /*----------  Session  ---------------*/
        if(session_id() === '') session_start();

        /*----------  Twig ---------------*/
        $loader = new Twig_Loader_Filesystem(
            $this->registry()->get('app.root').'/views'
        );
        $twig = new Twig_Environment($loader,array(
            'cache'=>$this->registry()->get('app.root').'/tmp/cache'
        ));

        if( $this->registry()->get('app.env') != 'production') 
        {
            $twig->clearCacheFiles();
        }
        $this->twig = $twig;
    }


    /**
     * 出力前の調整フィルター
     *
     * @SeafHookOn stop before
     */
    public function changePathFilter( )
    {
        if( $this->request()->getBaseURL() )
        {
            /* パスの変換処理 */
            $data = ob_get_clean();
            ob_start();
            echo  preg_replace(
                '/(src|href|action)=([\'"])[\/]/',
                '$1=$2'.$this->request()->getBaseURL().'/',
                $data
            );
        }
    }


    /**
     * テンプレートのみページを出力
     *
     * @SeafURL /@page:*
     * @SeafMethod POST|GET
     */
    public function showPage( $page ) 
    {
        if($page == null) $page = 'index';
        if($page == 'ra') $page = 'ra/index';

        $viewParams = array('base_url'=>$this->request()->getBaseURL());
        if( $page == 'index')
        {
            $viewParams['news'] = 
                file_get_contents( $this->registry()->get('app.root')."/data/news.txt" );
        }

        $tpl = $page.".twig";
        try {
            echo $this->twig->render( $tpl, $viewParams);
        } catch(Twig_Error_Loader $e) {
            Seaf::debug($e->getMessage());
            return true;
        }
    }

    /**
     * メール送信
     *
     * @SeafURL /sendMail
     * @SeafMethod PUT
     */
    public function sendMail( )
    {
        $mail = $this->di('mail');
        $query = file_get_contents('php://input');
        $params = array();

        parse_str( $query, $params);

        if( empty($params['mail']) ) 
        {
            $this->halt('不正なアクセスを検知しました');
        }

        $mail->sendTo(
            $this->registry()->get('admin.mail'),
            $this->registry()->get('admin.mail'),
            'コンタクトありがとうございます。',
            $this->twig->render('mail/mail.twig', $params)
        );
        $mail->sendTo(
            $params['mail'],
            $this->registry()->get('admin.mail'),
            'コンタクトありがとうございます。',
            $this->twig->render('mail/mail.twig', $params)
        );

        Seaf::debug('メールを送信しました');
    }

    /**
     * 何もマッチしなかった場合
     *
     * @SeafURL *
     */
    public function notFound( )
    {
        $this->web->notFound();
        return false;
    }

}
