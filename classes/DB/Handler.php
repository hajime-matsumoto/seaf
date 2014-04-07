<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf\Util\ArrayHelper;
use Seaf\Exception;
use Seaf\Cache;
use Seaf;


/**
 * データベース処理ハンドラ
 */
class Handler
{
    private $defaultConnection = 'default';
    private $conPool = [];
    private $conDSNs = [];
    private $tableMap = [];
    private $cache;

    protected static function who( ) {
        return __CLASS__;
    }

    /**
     * 作成
     */
    public static function factory ($config) 
    {
        $c = ArrayHelper::container($config);
        $class = static::who();
        $handler = new $class( );

        // デフォルトコネクションをセットする
        $handler->defaultConnection = $c('setting.default_connection', 'default');

        // DSNをセットする
        foreach ($c('connectMap', array()) as $name => $dsn) {
            $handler->conDSNs[$name] = new DSN($dsn);
        }
        // tableをセットする
        foreach ($c('tableMap', array()) as $name => $table) {
            $handler->setTableMap($name, $table);
        }

        // キャッシュハンドラをセットする
        if ($c('cache', false)) {
            $handler->cache = Cache\CacheHandler::factory($c('cache'));
        }

        return $handler;
    }

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->conPool = ArrayHelper::container([]);
        $this->conDSNs = ArrayHelper::container([]);
        $this->tableMap = ArrayHelper::container([]);
    }

    public function setTableMap($name, $params)
    {
        if (!is_array($params)) {
            $params = array($params);
        }
        if (!isset($params[1])) $params[1] = null;

        $this->tableMap[$name] = [
            'datasource' => $params[0],
            'schema' => $params[1]
        ];
    }

    /**
     * データソースコネクションを開く
     *
     * @param string
     * @retucn DataSource
     */
    public function open ($name)
    {
        if ($name instanceof DataSource) {
            return $name;
        }

        if ($this->conPool->has($name)) {
            return $this->conPool->get($name);
        }

        // データソース名を取得
        if (!$this->conDSNs->has($name)) {
            throw new Exception\Exception([
                "%sに対応するデータソース名が取得できません",
                $name
            ]);
        }
        $dsn = $this->conDSNs->get($name);

        // データソースをオープンする
        $ds  = DataSource::factory($dsn, $this);

        $this->conPool->set($name, $ds);
        return $ds;
    }

    /**
     * リクエストを作成する
     *
     * @param string 処理区分 (QUERY|INSERT|UPDATE...)
     */
    public function newRequest ($type)
    {
        $request = Request::factory($type);
        $request->setHandler($this);
        return $request;
    }

    /**
     * リクエストを処理する
     *
     * @param Request
     */
    public function execute (Request $request)
    {
        $request = $this->normalizeRequest($request);


        // キャッシュの使用
        if (!$request->isAllowCache()) return $this->_execute($request);


        $key = $request->getHash();
        $expires = $request->getCacheExpires();

        if ($this->cache->has($key) && !empty($expires)) {
            Seaf::Logger('DB')->debug('CACHE-USED');
            $result = $this->cache->getCachedData($key, $status);
            $result->setCacheStatus(sprintf('Hit Created:%s Expires:%s',
                $status['created'],
                $status['expire']
            ));
            return $result;
        }else{
            $result = $this->_execute($request);

            if ($result->isError()) { // エラー時にはキャッシュを作らない
                return $result;
            }

            $result->save();

            $this->cache->put($key, $expires, $result);
            return $result;
        }
    }

    private function _execute (Request $request)
    {
        // ターゲットコネクションをオープンする
        $con = $this->open($request->getTarget());
        $result = $con->request($request);

        // 結果クラスを作成する
        $result =  new Result($con, $result, $this);

        return $result;
    }

    /**
     * リクエストをノーマライズする
     *
     * @param Request
     * @return Request
     */
    public function normalizeRequest (Request $request)
    {
        // 宛先テーブルからコネクションを判定
        if ($tt = $request->getTargetTable()) {
            // テーブルマップを参照
            $tm = $this->tableMap;

            if($tm->has($tt)) {
                $request->setTarget(
                    $tm($tt.'.datasource')
                );
            }
        }
        // 宛先がなければデフォルトのコネクションへ
        if ($request->getTarget() == false) {
            $request->setTarget($this->defaultConnection);
        }

        return $request;
    }

    /**
     * テーブルを取得する
     */
    public function getTable ($name)
    {
        return new Table($name, $this);
    }

    /**
     * openへのシンタックスシュガー
     */
    public function __invoke ($name)
    {
        return $this->open($name);
    }

    /**
     * getTableへのシンタックスシュガー
     */
    public function __get ($name)
    {
        return $this->getTable($name);
    }
}
