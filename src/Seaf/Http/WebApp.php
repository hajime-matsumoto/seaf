<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Http;

use Seaf\Seaf;

/**
 * ウェブアプリケーションコンポーネント
 */
class WebApp extends Http
{
    public function __construct( )
    {
        parent::__construct();

        $this->di('registry')->set('name', 'WebApp');
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
