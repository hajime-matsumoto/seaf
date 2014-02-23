<?php

namespace Seaf\Factory;

use ReflectionClass;

class FactoryClassName extends Factory
{


	public function createInstance( )
	{
		$rc = new ReflectionClass( $this->context );
		$instance = $rc->newInstanceArgs($this->params);
		return $instance;
	}
}
