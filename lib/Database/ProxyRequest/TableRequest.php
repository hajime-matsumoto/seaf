<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * プロキシコマンド
 */
namespace Seaf\Database\ProxyRequest;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Module;

/**
 * テーブル宛てのリクエスト
 */
class TableRequest extends Module\ProxyRequest
{
    public function insert($datas)
    {
        $result = $this->executeproxyrequestcall(
            'insert', [$this->getParam('table'), $datas]
        );
        return $result->retrive();
    }

    public function update($datas, $query)
    {
        $result = $this->executeproxyrequestcall(
            'update', [$this->getParam('table'), $datas, $query]
        );
        return $result->retrive();
    }
}
