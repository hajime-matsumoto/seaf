<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Web Application
 */
require_once dirname(__FILE__).'/../../vendor/autoload.php';

use Seaf\Core\Base;
use Seaf\Net\WebApp;


class App extends WebApp 
{
    private $twig;

    public function __construct( $env = 'development' )
    {
        parent::__construct( dirname(__FILE__), $env );

    }

    /**
     * Initialize
     */
    public function init( )
    {
        /*----------  Twig ---------------*/
        $loader = new Twig_Loader_Filesystem($this->get('view.path'));
        $twig = new Twig_Environment($loader,array('cache'=>$this->get('cache.path')));

        if( $this->get('app.env') != 'production') 
        {
            $twig->clearCacheFiles();
        }
        $this->twig = $twig;
    }

    /**
     * 出力前の調整フィルター
     *
     * @after start
     */
    public function changePathFilter( $params, &$output )
    {
        if( $this->request->base )
        {
            /* パスの変換処理 */
            $data = ob_get_clean();
            ob_start();
            echo  preg_replace('/(src|href)=([\'"])[\/]/','$1=$2'.$this->request->base.'/', $data);
        }
    }

    /**
     * テンプレートのみページを出力
     *
     * @route /@page:*
     * @method POST|GET
     */
    public function showPage( $page ) 
    {
        if($page == null) $page = 'index';
        if($page == 'ra') $page = 'ra/index';

        $tpl = $page.".twig";
        try {
            echo $this->twig->render( $tpl, 
                array('base_url'=>$this->request->base)
            );
        } catch(Twig_Error_Loader $e) {
            return true;
        }
    }

    /**
     * メール送信
     *
     * @route /sendMail
     * @method PUT
     */
    public function sendMail( )
    {
        $mail = $this->exten('mail');
        $query = $this->request->body;
        $params = array();

        parse_str( $query, $params);

        if( empty($params['mail']) ) 
        {
            $this->web->halt('不正なアクセスを検知しました');
        }

        $mail->sendTo(
            $this->get('admin.mail'),
            $this->get('admin.mail'),
            'コンタクトありがとうございます。',
            $this->twig->render('mail/mail.twig', $params)
        );
        $mail->sendTo(
            $params['mail'],
            $this->get('admin.mail'),
            'コンタクトありがとうございます。',
            $this->twig->render('mail/mail.twig', $params)
        );
    }

}
