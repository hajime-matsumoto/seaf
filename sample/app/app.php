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

		/*----------  Routing ---------------*/
		// web機能を有効にする
		$this->enable('web');
		$web  = $this->exten('web');
		$self = $this;

		$web->set('twig', $twig);

		$web->route('/ra/(@page)', function($page ) use ($web) {
			if($page == null) $page = 'index';
			$tpl = '/ra/'.$page.".twig";
			try {
				echo $web->get('twig')->render( $tpl );
			} catch(Twig_Error_Loader $e) {
				$web->notFound();
			}
		});
		$web->route('/(@page)', function($page ) use ($web) {
			if($page == null) $page = 'index';
			$tpl = $page.".twig";
			try {
				echo $web->get('twig')->render( $tpl );
			} catch(Twig_Error_Loader $e) {
				$web->notFound();
			}
		});
	}

	public function run( )
	{
		$this->webStart();
	}
}
