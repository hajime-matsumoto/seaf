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

namespace Seaf\Parser\Haml;

use Seaf\Seaf;


/**
 * HAMLパーサーノード
 */
class Node
{
    public $indent;
    public  $context;
    public $parent;
    public $children = array();

    public function __construct($indent, $context )
    {
        $this->context = $context;
        $this->indent = $indent;
    }

    public function setParent(Node $node)
    {
        $this->parent = $node;
    }

    public function canContain(Node $node)
    {
        if($this->indent < $node->indent)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function append( Node $node)
    {
        if( $this->canContain( $node ) )
        {
            $node->setParent($node);
            $this->children[] = $node;
            return $node;
        }

        if( $this->parent ) 
        {
            return $this->parent->append( $node);
        }

        return $this;
    }

    public function toHtmlBegin( )
    {
    }
    public function toHtmlEnd( )
    {
    }
    public function toHtml( )
    {
        $text = $this->toHtmlBegin( );
        foreach( $this->children as $child )
        {
            $text.= $child->toHtml();
        }
        $text .= $this->toHtmlEnd( );

        return $text;
    }

    public static function rootNode( )
    {
        return new RootNode();
    }

    /**
     * ノードを作成する
     */
    public static function createNode( $line )
    {
        $firstChar = '';
        for($i=0;$i<strlen($line);$i++)
        {
            // タブと空白を数える
            if(!empty($line[$i]))
            {
                $firstChar = $line[$i];
                break;
            }
        }

        if( $firstChar == '' )
        {
            // 文字が居ない場合改行ノードを作成
            return new NodeCR($i);
        }

        if( $firstChar == '%' ) // タグ
        {
            return new NodeHTML($i, substr($line,1));
        }

        if( $firstChar == '-' ) // コマンド
        {
            return new NodeCommand($i, substr($line,1));
        }

        die("あてはまらん");
    }
}

/**
 * ルートノード
 */
class RootNode extends Node
{
    public function __construct()
    {
    }

    public function canContain( Node $node )
    {
        return true;
    }
}

/**
 * HTMLノード
 *
 * %h1(属性群){a=b,c=d,e=f}=xxx
 * %h1{a=b,c=d,e=f}
 * %h1=xxx
 * %h1 aaaa
 */
class NodeHtml extends Node
{
    private $paresed;

    public function __construct( $indent, $context )
    {
        parent::__construct($indent, $context);

        $scope = 'tagName';
        $parsed = array(
            'tagName'=>'',
            'attr'=>'',
            'desc'=>'',
            'var'=>'',
            'text'=>''
        );
        $context = $this->context;

        for($i=0;$i<strlen($context);$i++)
        {
            $char = $context[$i];

            if( $char == '(' ) {
                while( ')' == $context[++$i] )
                {
                    $parsed['attr'].= $char;
                    if( $i > strlen($context) ) die('合いません');
                }

                $scope = false;
            }


            if( $char == '{' ) {
                while( '}' == $context[++$i] )
                {
                    $parsed['desc'].= $char;
                    if( $i > strlen($context) ) die('合いません');
                }

                $scope = false;
            }

            if( $char == '=' ) {
                $parsed['var'] = substr($context,++$i);
                break;
                $scope = false;
            }

            if( $scope == 'tagName' && $char == ' ')
            {
                $scope = 'text';
            }

            if( $scope ) $parsed[$scope] .= $char;
        }

        $this->parsed = $parsed;
    }

        
    public function toHtmlBegin( )
    {
        $tag = trim($this->parsed['tagName']);
        return '<'.$tag.'>'.trim($this->parsed['text']);
    }

    public function toHtmlEnd( )
    {
        $tag = trim($this->parsed['tagName']);
        return '</'.$tag.'>';
    }

}



/* vim: set expandtab ts=4 sw=4 sts=4: et*/
