<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database;

use Seaf\Base\Module;
use Seaf\Base\Command;
use Seaf\Util\Util;
use Seaf\Base\Event;

/**
 * 
 */
class DatabaseFacade extends Module\Facade
{
    const DEFAULT_SOURCE_NAME = 'sql';

    private $datasources = [];


    public function __construct ($config = [])
    {
    }

    /**
     * Mediatorからの初期化
     */
    public function initWithMediator($mediator)
    {
        $c = Util::ArrayContainer(
            $mediator->config->getConfig('database')
        );

        // データソースを設定する
        foreach ($c('datasources', []) as $name=>$ds) {
            $this->setDatasource($name, $ds);
        }
    }

    /**
     * データソースを設定する
     */
    public function setDatasource($name, $dsn_string)
    {
        $this->datasources[$name] = new DSN($dsn_string);
    }


    /**
     * Requestの受信
     */
    public function execute(Command\RequestIF $request, $from = null)
    {
        $targets = $request->dict('target')->toArray();

        $table = false;

        if (count($targets) == 1) {
            $object = $this;
        } elseif (count($targets) == 2) {
            // ターゲットが1階層以上の場合は
            // DBHへのリクエストとする
            $object = $this->DBH($targets[1]);

        } elseif (count($targets) == 3) {
            // ターゲットが2階層以上の場合は
            // Tableへのリクエストとする
            $object = $this->DBH($targets[1])->getTable($targets[2]);
        }


        // メソッド取得
        $method = $request('method');

        // 引数取得
        $args = $request('args', []);

        $this->debug("PROCESS", sprintf(
            "Process %s [%s]",
            get_class($object),
            $method
        ));

        if (!is_callable([$object, $method])) {
            return $request->result()->error('METHOD_NOTFOUND', [
                    'class' => get_class($object),
                    'method' => $method
                ]
            );
        }

        // 実行
        $request->result()->addReturnValue(
            call_user_func_array([$object,$method], $args)
        );
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

        $this->handlers[$name] = DatabaseHandler::factory($this->datasources[$name]);
        $this->handlers[$name]->addObserver($this);

        return $this->handlers[$name];
    }
}

class InvalidDataBaseHandlerName extends \Exception
{
    public function __construct($name)
    {
        parent::__construct("Invalid DB Handler Name $name");
    }
}
