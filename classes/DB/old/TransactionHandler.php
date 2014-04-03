<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf\Exception;

/**
 * トランザクションハンドラ
 */
class TransactionHandler
{
    private $handlers = [];

    /**
     * DBハンドラ
     */
    public function setDBHandler($name, Handler $handler)
    {
        $this->handlers[strtoupper($name)] = $handler;
    }

    /**
     * DBハンドラを取得
     */
    public function getDBHandler($name = 'default')
    {
        if (isset($this->handlers[$name = strtoupper($name)]) ) {
            return $this->handlers[$name];
        }

        throw new Exception\Exception([
            '%sは登録されていません',
            $name
        ]);
    }

    /**
     * SQLを実行する時に干渉する
     *
     * @param Handler
     * @param string
     * @param array
     */
    public function _query ($handler, $query, $options)
    {
        // タイプを取得する
        $result = $handler->query($query, $options);
        return $result;
    }

    /**
     * 定義されていないコール
     */
    public function __call ($name, $params)
    {
        if (method_exists($this, $method = "_".$name)) {
            array_unshift($params, $this->getDBHandler());
            return call_user_func_array(
                [$this, $method],
                $params
            );
        }
        return call_user_func_array(
            [$this->getDBHandler(),$name],
            $params
        );
    }
}
