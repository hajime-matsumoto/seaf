<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource;

use Seaf;
use Seaf\Base;
use Seaf\Container\ArrayContainer;

class DataSource
{
    use Base\ComponentCompositeTrait;
    use Base\RecurseCallTrait;

    private $defaultDataSourceName = 'default';
    private $tableMap = [];

    /**
     * コンストラクタ
     *
     * @param string|array
     */
    public function __construct ($cfg = [])
    {
        $this->setComponentContainer(__NAMESPACE__.'/ComponentContainer');

        if (is_string($cfg)) {
            $this->addDataSourceHandler('default', $cfg);
        } elseif (is_array($cfg)) {
            $cfg = new ArrayContainer($cfg);
            $this->addDataSourceHandler($cfg('connectMap', []));
            $this->tableMap = $cfg('tableMap', []);
            $this->defaultDataSourceName = $cfg('setting.default_connection', 'default');
        }
    }

    /**
     * データソースハンドラを追加する
     *
     * @param string
     * @param DSN
     */
    public function addDataSourceHandler($name, $dsn = null)
    {
        if($this->recurseCallIfArray($name, __FUNCTION__)) return $this;
        $this->handlers[$name] = new DSN($dsn);
        return $this;
    }

    /**
     * データソーステーブルを取得する
     *
     * @param string
     * @return Table
     */
    public function getTable ($table_name)
    {
        return new Table($table_name, $this);
    }

    /**
     * リクエストを実行する
     *
     * @param Request
     */
    public function execute (Request $request)
    {
        $request = $this->normalize($request);
        $dsh = $this->getDataSourceHandler($request->getDataSourceHandlerName());
        $result = $dsh->execute($request);
        return $result;
    }

    /**
     * 宛先を取得する
     *
     * @param string
     * @return string
     */
    private function getDataSourceHandlerName ($name)
    {
        if (isset($this->tableMap[$name])) {
            return $this->tableMap[$name];
        }
        return $this->defaultDataSourceName;
    }

    /**
     * データソースハンドラを取得する
     *
     * @param string
     * @return DataSourceHandler
     */
    protected function getDataSourceHandler ($name)
    {
        $handler = $this->handlers[$name];
        if ($handler instanceof DSN) {
            $handler = $this->handlers[$name] = DataSourceHandler::factory($handler, $this);
        }
        return $handler;
    }

    /**
     * リクエストをノーマライズする
     */
    public function normalize ($request)
    {
        if (!$request->getDataSourceHandlerName()) {
            $request->setDataSourceHandlerName(
                $this->getDataSourceHandlerName($request->getPath())
            );
        }
        return $request;
    }

    /**
     * リクエストを作成
     *
     * @return Request
     */
    public function newRequest ($method = false)
    {
        $req = Request::Factory( )->ds($this);
        if ($method != false) $req->method($method);
        return $req;
    }
}
