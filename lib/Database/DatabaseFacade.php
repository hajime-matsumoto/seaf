<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * データベースモジュール
 */
namespace Seaf\Database;

use Seaf\Util\Util;
use Seaf\Base\Module;
use Seaf\Base\Proxy;
use Seaf\Base\ConfigureTrait;
use Seaf\Base\Component;

class DatabaseFacade extends Module\ModuleMediator
{
    use ConfigureTrait;
    use Component\ComponentContainerTrait;

    protected static $object_name = 'DB';

    private $datasources;
    private $handlers;
    private $tables;

    /**
     * コンストラクタ
     */
    public function __construct (Module\ModuleIF $module = null, $configs = [])
    {
        parent::__construct($module);

        if($config = $this->module('config')) {
            $configs = $this->module('util')->phpFunction->array_merge(
                $configs, $config->getConfig('database', [])
            );
        }

        $this->datasources = Util::Dictionary([ ]);
        $this->handlers = Util::Dictionary([ ]);
        $this->tables = Util::Dictionary([ ]);


        // 設定
        $this->configure($configs,[]);

        $this->registerModule([
            'table' => __NAMESPACE__.'\TableHandler',
        ]);
    }

    /**
     * リクエストへの__get
     */
    public function __proxyRequestGet(Proxy\ProxyRequestIF $req, $name)
    {
        if ($req instanceof ProxyRequest\TableRequest) {
            return $this->loadModule('table')->__proxyRequestGet($req, $name);
        }

        // リクエストを変換して送り返す
        $request = $req->factory(__NAMESPACE__.'\ProxyRequest\TableRequest');
        $request->setParam('table', $name);
        return $request;
    }

    public function __proxyRequestCall(Proxy\ProxyRequestIF $req, $name, $params)
    {
        $this->debug("Recive $name");

        if ($req instanceof ProxyRequest\TableRequest) {
            return $this->loadModule('table')->__proxyRequestCall($req, $name, $params);
        }
        return $this->proxyRequestCall($req, $name, $params);
    }

    /**
     * データソースを設定する
     */
    public function setDatasource($name, $dsn_string)
    {
        $this->datasources->set($name, $dsn_string);
    }

    public function getDatasourceList( )
    {
        return $this->datasources;
    }

    /**
     * データソースを表示する
     */
    public function explain( )
    {
        printf("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\n");
        printf("[ Data Source List ]\n");
        printf("||\n");
        foreach ($this->datasources as $k => $v)
        {
            printf("|| >>> %s = %s \n", $k, $v);
        }
        printf("||\n");
        printf("[ Table Map ]\n");
        printf("||\n");
        foreach ($this->tables as $k => $v)
        {
            printf("|| >>> %s = %s # %s\n", $k, $v['ds'], $v['comment']);
        }
        printf("||\n");
        printf("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\n");
    }

    /**
     * データソースとテーブルを関連付ける
     */
    public function setTable($name, $ds = null, $comment = '')
    {
        if (is_array($name)) {
            foreach($name as $k=>$v) {
                if (is_array($v)) {
                    $ds = $v[0];
                    $comment = isset($v[1]) ? $v[1]: '';
                } else {
                    $ds = $v;
                    $comment = '';
                }
                $this->setTable($k, $ds, $comment);
                return $this;
            }
        }
        $this->tables->set($name, ['ds'=>$ds,'comment'=>$comment]);
        return $this;
    }

    public function getHandlerByTable($table)
    {
        $ds = 'default';
        if ($this->tables->has($table)) {
            $ds = $this->tables->dict($table)->ds;
        }
        $this->debug(['Get Handler By Table >>> %s %s', $table, $ds]);
        return $this->DBH($ds);
    }

    /**
     * DatabaseHandlerを取得もしくは作成する
     */
    public function DBH($name)
    {
        if (isset($this->handlers[$name])) {
            return $this->handlers[$name];
        }
        if (!isset($this->datasources[$name])) {
            throw new InvalidDataBaseHandlerName($name);
        }

        $dsn = new DSN($this->datasources[$name]);
        $class = Util::ClassName(
            __NAMESPACE__,
            $dsn->getType(),
            'Handler'
        );
        $handler = $class->newInstance($this, $dsn);

        $this->handlers->set($name, $handler);
        return $handler;
    }
}
