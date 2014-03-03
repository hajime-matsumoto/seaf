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
 * 行を表現するクラス
 */
class Node
{
    private $parent;
    protected $children = array();
    public $indent;
    protected $context;

    public function __construct( $indent, $line )
    {
        $this->indent = $indent;
        $this->context = $line;
    }

    public function setParent( Node $parent )
    {
        $this->parent = $parent;
    }

    public function _append( Node $node )
    {
        $this->children[] = $node;

        $node->setParent($this);
    }

    public function append( Node $node )
    {
        if( $this->canContain( $node ) )
        {
            $this->_append( $node);
            return $node;
        }

        if( $this->parent )
        {
            return $this->parent->append($node);
        }

        return $this;
    }

    public function canContain( Node $node )
    {
        if( $this->indent < $node->indent )
        {
            return true;
        }
        return false;
    }

    public function toHtml( )
    {
        $text = '';
        foreach($this->children as $child )
        {
            $text.= $child->toHtml();
        }
        return $text;
    }
}

