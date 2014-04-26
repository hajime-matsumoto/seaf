<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Routing;

use Seaf\Com\Request\Request;
use Seaf\Wrapper;

/**
 * ルート
 */
class Route
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var mixed
     */
    private $action;

    /**
     * @var array
     */
    private $methods = array('*');

    /**
     * @var array
     */
    private $params = array();

    /**
     * @var string
     */
    private $compiled_regex = '';


    /**
     * @param string
     * @param mixed
     */
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

    /**
     * execute
     *
     * @param 
     * @return void
     */
    public function execute ()
    {
        $params = $this->params + func_get_args();
        return Kernel::dispatcher($this->action, $params, $this)->dispatch();
    }

    /**
     * @param Request
     */
    public function match (Request $request)
    {
        if ($this->methods !== array('*')) {
            if (!count(array_intersect(array($request->getMethod( ), '*'),$this->methods)) > 0){
                return false;
            }
        }

        if( $this->pattern === "*" || $this->pattern === $request->getPath() ) return true;

        $ids = array();

        // パターンの最後一文字を取得
        $char = substr($this->pattern, -1);

        // * で指定されているパターン位置を取得
        $splat = substr($request->getPath(), strpos($this->pattern, '*'));

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

        $this->compiled_regex = $regex;

        if( preg_match('#^'.$regex.'(?:\?.*)?$#i', $request->getPath(), $m) ) {
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

    /**
     * パラメタを取得する
     *
     * @param array 追加するパラメタ
     * @return array
     */
    public function getParams($add_params = array())
    {
        $params = array_merge($this->params, $add_params);
        return $params;
    }

    /**
     * アクションを取得する
     *
     * @return array
     */
    public function getAction( )
    {
        return $this->action;
    }

    /**
     * アクションをクロージャで取得する
     *
     * @return Wrapper\ReflectionFunction
     */
    public function getClosure( )
    {
        return Wrapper\ReflectionFunction::create($this->action);
    }

    /**
     * 変換した正規表現を取得する
     *
     * @return string
     */
    public function getCompiledRegex( )
    {
        return $this->compiled_regex;
    }
}
