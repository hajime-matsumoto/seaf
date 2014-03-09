<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FrameWork\Component;

use Seaf\FrameWork\Application;
use Seaf\Commander\Command;

/**
 * Route
 */
class Route
{
    private $pattern;
    private $action;
    private $methods = array('*');
    private $params = array();


    public function __construct ($pattern, $action)
    {
        if (strpos($pattern, ' ') !== false) {
            list($method, $url) = explode(' ', trim($pattern), 2);
            $this->methods = explode('|', $method);
            $pattern = $url;
        }
        $this->pattern = $pattern;
        $this->action = $action;
    }

    public function getAction()
    {
        return $action;
    }

    public function getCommand( )
    {
        return Command::factory(array(
            'type' => 'closure',
            'content'=> $this->action,
            'params'=>$this->params,
            'caller'=>$this
        ));
    }

    public function match (Request $request)
    {
        if ($this->methods !== array('*')) {
            $method = $request->getMethod();
            if (!count(array_intersect(array($method, '*'),$this->methods)) > 0){
                return false;
            }
        }
        $uri = $request->getUri();

        if( $this->pattern === "*" || $this->pattern === $uri ) return true;

        $ids = array();

        // パターンの最後一文字を取得
        $char = substr($this->pattern, -1);

        // * で指定されているパターン位置を取得
        $splat = substr($uri, strpos($this->pattern, '*'));

        // ?は量指定子 {0,1}と同等
        $pattern = str_replace(')',')?',$this->pattern);
        $pattern = str_replace('*','.*?',$pattern);

        $param_index = array(); // リスト
        $regex = preg_replace_callback(
            '/@([\w]+)(:([^\/\(\)]*))?/', // パラメータ指定の部分に適用
            function( $m ) use (&$param_index) {
                // 見つかったパラメタを保存
                $param_index[$m[1]] = null;
                // var_dump($m[1]); // @の後にあった文字列
                // var_dump($m[2]); // :を含む:以降の文字列
                // var_dump($m[3]); // :を含まない:以降の文字列
                if (isset($m[3])) {
                    return '(?P<'.$m[1].'>'.$m[3].')'; 
                }
                return '(?P<'.$m[1].'>[^\/?]+)';
            }, $pattern);

        // 指定されたパターンが/で終わる場合
        // あってもなくてもヒットするように正規表現を変更
        $regex .= $char == '/' ? '?': '/?';

        if( preg_match('#^'.$regex.'(?:\?.*)?$#i', $uri, $m) ) {
            $params = array();
            foreach ($param_index as $k=>$v) {
                if (array_key_exists($k, $m)) {
                    $params[$k] = urldecode($m[$k]);
                }else{
                    $params[$k] = null;
                }
            }
            $this->params = $params;
            return true;
        }
        return false;
    }
}
