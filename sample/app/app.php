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
		$this->set('twig', $twig);

		/*----------  Routing ---------------*/
		// web機能を有効にする
		$this->enable('web');
		$web  = $this->exten('web');
		$self = $this;

		$web->route(array(
			'/' => function() use ($web, $self, $twig) {
				$web->response->header('Content-Type', 'text/plain');
				$self->get('twig')->render('index.html');
			}
		));
	}

	public function run( )
	{
		$this->webStart();
	}
}
