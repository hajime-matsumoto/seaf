<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Seafのベースクラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Core;

/**
 * 環境オブジェクト
 */
use Seaf\Core\Environment;


/**
 */
class Base extends Environment
{

    public function __construct( )
    {
        parent::__construct();

    }

}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
