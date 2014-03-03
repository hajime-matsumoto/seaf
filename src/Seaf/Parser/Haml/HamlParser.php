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
 * 使用するノード一式
 */
use Seaf\Parser\Haml\Node\Node;
use Seaf\Parser\Haml\Node\EmptyNode;
use Seaf\Parser\Haml\Node\TagNode;
use Seaf\Parser\Haml\Node\CommandNode;
use Seaf\Parser\Haml\Node\FilterNode;
use Seaf\Parser\Haml\Node\TextNode;

/**
 * HAMLパーサー
 */
class HamlParser
{
    const SINBOLE_CLASS   = '.';
    const SINBOLE_ID      = '#';
    const SINBOLE_FILTER  = ':';
    const SINBOLE_TAG     = '%';
    const SINBOLE_COMMAND = '-';

    /**
     * 文字列をパースする
     */
    public static function parseString( $haml )
    {
        $lines  =self::normalizedLines( $haml ) ;

        $root = new Node(-1,'');

        $head = $root;

        foreach( $lines as $no=>$normalized_line)
        {
            list( $indent, $line ) =  explode(' ',$normalized_line,2);

            // 最初の１文字から処理クラスを判定する
            $node = self::parseLine( $line, $indent, $no, $lines );

            $head = $head->append($node);
        }
        var_dump($root->toHtml());
    }

    public static function normalizedLines( $haml )
    {
        // まずはインデントを数に変えてみよう
        $lines = preg_split('/\n/', $haml );
        for( $no = 0; $no < count($lines); $no++ )
        {
            $line = $lines[$no];

            // タブは空白二文字に変換
            $line = str_replace("\t","  ", $line);

            // インデントの数を覚える
            $indent =  strspn($line," ");

            // インデントをとり終わったら消す
            $line = ltrim($line);

            // divを省略してたら戻す
            if( !empty($line)  && ($line[0] == self::SINBOLE_CLASS || $line[0] == self::SINBOLE_ID ))
            {
                $line = '%div'.$line;
            }

            // 書き戻す
            //
            $lines[$no]  = $indent.' '.$line;
        }
        return $lines;
    }

    public static function parseLine( $line, $indent, $no, $lines )
    {
        // 改行は空クラスで表現する
        if(empty($line)) {
            return new EmptyNode($indent, $line );
        }

        if( $line[0] == self::SINBOLE_TAG ){ // タグオブジェクト
            return self::parseTagLine( $line, $indent, $no, $lines );
        }
        if( $line[0] == self::SINBOLE_COMMAND ){ // コマンドオブジェクト
            return new CommandNode( $indent, $line );
        }
        if( $line[0] == self::SINBOLE_FILTER ){ // コマンドオブジェクト
            return new FilterNode( $indent, $line );
        }
        return new TextNode( $indent, $line ); // テキストノード
    }

    public static function parseTagLine( $line, $indent, $no, $lines )
    {

        // "()" や "{}" などはめんどくさいので後で処理をする
        if( false === self::trimMatchedSimbol('{','}',$line, $curlyBrackets ) )
        {
            throw new SyntaxException('括弧 "{" が一致しません。行:'.$no);
        }
        if( false === self::trimMatchedSimbol('(',')',$line, $roundBrackets ) )
        {
            throw new SyntaxException('括弧 "(" が一致しません。行:'.$no);
        }


        // タグの後ろに文字列があるか調べる
        if( false !== ($p = strpos($line, ' ')) )
        {
            $text = trim(substr($line,$p));
            $line = trim(substr($line,0,$p));
        }

        // タグ直後に=があるか調べる
        if( false !== ($p = strrpos($line,'=')) )
        {
            $val  = trim(substr($line,$p+1));
            $line = trim(substr($line,0,$p));
        }

        // SINBOLE_CLASSを解決する
        $vars = preg_split(
            '/('.preg_quote(self::SINBOLE_CLASS).'|'.preg_quote(self::SINBOLE_ID).')/',
            $line,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );

        $tagName = ltrim(array_shift($vars),'%');
        for($i=0; $i<count($vars);$i+=2)
        {
            if( $vars[$i] == self::SINBOLE_CLASS )
            {
                $classes[] = $vars[$i+1];
            }
            if( $vars[$i] == self::SINBOLE_ID )
            {
                $id  = $vars[$i+1];
            }
        }

        // 取得したタグ情報
        return new TagNode(compact('indent','tagName','classes','curlyBrackets','roundBrackets','text','val'));
    }

    /**
     * @param char
     * @param char
     * @param line
     */
    public static function trimMatchedSimbol( $startMark, $endMark, &$line, &$trimed )
    {
        if( false !== ($start = strpos($line, $startMark)) )
        {
            // 多重にあった場合の対処
            $offset = $start;
            $confirm_start_offset = $start+strlen($startMark);

            while( false !== ($end = strpos($line,$endMark, $offset) ) )
            {
                // そこまでにもう一回開かれていないか確認する
                if( false === ($confirm_start = strpos($line, $startMark, $confirm_start_offset)) )
                {
                    break;
                }

                if( $confirm_start > $end ) {
                    break;
                }

                $offset = $end+strlen($endMark);
                $confirm_start_offset = $end+strlen($endMark);
            }


            if( $end !== false)
            {
                $trimed = substr($line,$start,$end);
                $line = substr($line,0,$start).substr($line,$end+strlen($endMark));
                return true;
            }

            return false;
        }
    }
}




/* vim: set expandtab ts=4 sw=4 sts=4: et*/
