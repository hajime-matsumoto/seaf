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

namespace Seaf\Component;

use Seaf\DI\DIContainer;
use Seaf\Collection\ArrayCollection;

use Seaf\Seaf;
use Seaf\Core\Base;
use Seaf\Http\Http as SeafHTTP;

/**
 * HTTPコンポーネント
 */
class Http extends SeafHTTP
{

    /**
     * @param object $di
     */
    public function acceptDIContainer( DIContainer $di )
    {
        //Seaf::debug($this->di()->get('registry')->get('name'));
        //Seaf::debug($di->get('registry')->get('name'));
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
