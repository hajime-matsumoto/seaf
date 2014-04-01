<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

/**
 * リザルトセット
 */
class CacheableResult extends Result
{
    private $result_array, $result_is_error, $result_error;


    /**
     * コンストラクタ
     *
     * @param Result
     */
    public function __construct (Result $result)
    {
        $this->result_array = $result->fetchAssocAll( );
        $this->result_is_errro = $result->isError();
        $this->result_errro = $result->getError();
        $this->model_class = $result->model_class;
    }

    /**
     * エラー判定
     *
     * @return bool
     */
    public function isError ( )
    {
        return $this->result_is_erro;
    }

    /**
     * エラー取得
     *
     * @return string
     */
    public function getError ( )
    {
        return $this->result_error;
    }

    /**
     * 連想配列でレコードを取得
     *
     * @return array
     */
    public function fetchAssoc ( )
    {
        $rec = current($this->result_array);
        next($this->result_array);
        return $rec;
    }
}
