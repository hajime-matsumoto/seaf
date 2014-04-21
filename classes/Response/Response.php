<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Response;

use Seaf\Container\ArrayContainer;
use Seaf\Base;

/**
 * リクエスト管理クラス
 */
class Response extends ArrayContainer
{
    use Base\RecurseCallTrait;

    /**
     * ステータスコード
     *
     * @param int
     */
    public $status  = 200;

    protected $body    = '';

    public function __construct ( )
    {

    }

    /**
     * @return Response
     */
    public function clear ( )
    {
        parent::clear( );
        $this->status = 200;
        $this->body    = '';
        return $this;
    }

    /**
     * @param int
     * @return Response
     */
    public function status($code = 200)
    {
        $this->status = $code;
        return $this;
    }


    /**
     * @param string
     * @param mixed
     * @return Response
     */
    public function param($name, $value = null)
    {
        if ($this->recurseCallIfArray($name, __FUNCTION__)) return $this;
        $this->set($name, $value);
        return $this;
    }

    /**
     * @param string
     * @return Response
     */
    public function write($body)
    {
        $this->body .= $body;
        return $this;
    }

    //---------------------------------------
    // 取得系
    //---------------------------------------

    /**
     * レスポンスパラメタを取得する
     *
     * @return array()
     */
    public function getParams( )
    {
        return parent::toArray();
    }


    /**
     * レスポンスbodyを取得する
     *
     * @return string
     */
    public function getBody( )
    {
        return $this->body;
    }

    /**
     * レスポンスコードを取得する
     *
     * @return int
     */
    public function getStatus( )
    {
        return $this->status;
    }

    //---------------------------------------
    // 変換系
    //---------------------------------------

    /**
     * 文字列にする
     */
    public function toString( )
    {
        $array = $this->toArray();
        return json_encode($array);
    }

    /**
     * 配列にする
     */
    public function toArray( )
    {
        $array = array(
            'status'  => $this->status,
            'params'  => $this->getParams(),
            'body'    => $this->body
        );
        return $array;
    }
}
