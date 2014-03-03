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

namespace Seaf\Parser\Haml\Node;

/**
 * 空行を表現するクラス
 */
class EmptyNode extends Node
{
    public function canContain( )
    {
        return false;
    }
}
