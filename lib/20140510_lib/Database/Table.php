<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database;

use Seaf\Base\Command;
use Seaf\Base\Event;
use Seaf\Logging;

/**
 * Table Class
 */
class Table implements Event\ObservableIF
{
    use Event\ObservableTrait {
        Event\ObservableTrait::notify as ObservableTraitNotify;
    }
    use Logging\LoggableTrait;

    private $name;
    private $handler;
    private $original_request;

    /**
     * コンストラクタ
     */
    public function __construct ($name, DatabaseHandlerIF $handler)
    {
        $this->name = $name;
        $this->handler = $handler;
    }

    public function setOriginalRequest(Command\RequestIF $request)
    {
        $this->original_request = $request;
    }

    //--------------------------------------------
    // 自前で処理するリクエスト
    //--------------------------------------------
    
    /**
     * DeclearRequestを発行する
     */
    public function declear( )
    {
        $req = new Request\DeclearRequest( );
        $req->addObserver($this);

        $req->on('execute',function($e) use ($req) {
            $req->result()->addReturnValue(
                call_user_func_array([$this,'createTable'], $req('args'))
            );
            $e->stop();
        });

        return $req;
    }

    /**
     * FindRequestを発行する
     */
    public function find($query = [])
    {
        $req = new Request\FindRequest( );
        $req->query($query);
        $req->addObserver($this);

        $req->on('execute',function($e) use ($req) {
            $req->result()->addReturnValue(
                call_user_func_array([$this,'findTable'], $req('args'))
            );
            $e->stop();
        });
        return $req;
    }

    //--------------------------------------------
    // 実行系
    //--------------------------------------------

    /**
     * 配列からテーブルを作成する
     */
    public function createTable($settings)
    {
        // テーブル名を追加する
        $settings['name'] = $this->name;

        // CreateTableを実行する
        return $this->handler->createTable($settings);
    }

    /**
     * 配列からテーブルを検索する
     */
    public function findTable($settings)
    {
        // テーブル名を追加する
        $settings['name'] = $this->name;

        // CreateTableを実行する
        return $this->handler->findTable($settings);
    }

    /**
     * Insertを実行
     */
    public function insert($datas, &$lastInsertId = false)
    {
        $result = $this->handler->insert($this->name, $datas);
        return $result;
    }

    /**
     * updateを実行
     */
    public function update($datas, $query)
    {
        $result = $this->handler->update($this->name, $datas, $query);
        return $result;
    }

    /**
     * Last Insert IDを取得
     */
    public function lastInsertId( )
    {
        return  $this->handler->lastInsertId($this->name);
    }
}
