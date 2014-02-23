<?php

namespace Seaf\Util;

use Seaf\Core\Extension;
use Seaf\Core\Base;

class ErrorExtension extends Extension
{
	public function init( $prefix, $base )
	{
		set_error_handler( 
			array(
				$this,
				'errorHandler'
			)
		);

		set_exception_handler(
			array(
				$this,
				'exceptionHandler'
			)
		);
	}

	public function errorHandler($errno, $errstr, $errfile, $errline)
	{
		if (!(error_reporting() & $errno)) {
			return;
		}

		printf('[%d] %s %s %s',
			$errno,
			$errstr,
			$errfile,
			$errline
		);
	}

	public function exceptionHandler( $e )
	{
		echo '<pre>';
		echo $e;
		echo '</pre>';
	}
}
