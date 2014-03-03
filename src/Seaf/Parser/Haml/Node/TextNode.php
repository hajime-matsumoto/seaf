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
 * テキストを表現するクラス
 */
class TextNode extends Node
{
    public function toHtml( )
    {
        return $this->context;
    }
}

