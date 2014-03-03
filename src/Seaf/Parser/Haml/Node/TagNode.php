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
 * タグを表現するクラス
 */
class TagNode extends Node
{
    private $tagName;
    private $text;
    private $roundBrackets;
    private $curlyBrackets;
    private $var;
    private $classes = array();
    private $id = '';

    public function __construct( $array )
    {
        parent::__construct( $array['indent'], '');

        foreach($array as $k=>$v )
        {
            $this->$k = $v;
        }
    }

    public function toHtml( )
    {
        $attr = array(
            'class'=>implode($this->classes),
            'id'=>$this->id
        );
        foreach($attr as $k=>$v)
        {
            $attrs[] = " $k=\"$v\"";
        }

        $text= str_repeat("\t",$this->indent);
        $text.= '<'.$this->tagName.implode(' ',$attrs).'>';
        $text.= $this->text;

        $text.= parent::toHtml();

        $text.= str_repeat("\t",$this->indent);
        $text.= '</'.$this->tagName.'>';
        $text.="\n";

        return $text;
    }

}

