<?php
/**
 * Sample Application
 */
require_once dirname(__FILE__).'/../../vendor/autoload.php';

use Seaf\Core\Base;


class App extends Base {

	public function __construct( $env = 'development' )
	{
		parent::__construct( );

		$this->init( dirname(__FILE__), $env);

		/*----------  View ---------------*/
		// view機能を有効にする
		$loader = new Twig_Loader_Filesystem($this->get('view.path'));
		$twig = new Twig_Environment($loader,array('cache'=>$this->get('cache.path')));

		if( $this->get('app.env') != 'production') 
		{
			$twig->clearCacheFiles();
		}

		/*----------  Routing ---------------*/
		// web機能を有効にする
		$this->enable('web');
		$web  = $this->exten('web');
		$self = $this;

		$web->set('twig', $twig);

		/*----------  パス変換フィルタ ---------------*/
		if( $web->request->base )
		{
			$web->after('start', function($params, &$out) use ($web){
				$data = ob_get_clean();
				ob_start();
				echo  preg_replace('/(src|href)=([\'"])[\/]/','$1=$2'.$web->request->base.'/', $data);
			});
		}

		/*----------  ほぼ静的ページ ---------------*/
		$web->route('/@page:*', function($page ) use ($web) {
			if($page == null) $page = 'index';
			if($page == 'ra') $page = 'ra/index';

			$tpl = $page.".twig";
			try {
				echo $web->get('twig')->render( $tpl, 
					array('base_url'=>$web->request->base)
				);
			} catch(Twig_Error_Loader $e) {
				return true;
			}
		});

		/*----------  メール送信  ---------------*/
		$web->route('/sendMail', function() use ($web, $self) {
			$query = $web->request->body;
			$params = array();

			parse_str( $query, $params);

			if( empty($params['mail']) ) 
			{
				$web->halt('不正なアクセスを検知しました');
			}

			$mail = $this->exten('mail');
			$mail->sendTo(
				$self->get('admin.mail'),
				$self->get('admin.mail'),
				'コンタクトありがとうございます。',
				$web->get('twig')->render('mail/mail.twig', $params)
			);
			$mail->sendTo(
				$params['mail'],
				$self->get('admin.mail'),
				'コンタクトありがとうございます。',
				$web->get('twig')->render('mail/mail.twig', $params)
			);
		});
	}

	public function run( )
	{
		$this->webStart();
	}
}
