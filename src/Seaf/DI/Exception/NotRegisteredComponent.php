<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * DIコンテナ用の例外クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\DI\Exception;


use Seaf\Exception\Exception as SeafException;


/**
 * DIコンテナ用の例外
 */
class NotRegisteredComponent extends SeafException
{
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
