<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Command;

use Seaf\Base\Event;

/**
 * リクエストハンドラ
 */
abstract class RequestHandler implements Event\ObservableIF
{
    use Event\ObservableTrait {
        Event\ObservableTrait::notify as eventNotify;
    }

    private $currentResult;

    public function __get($name)
    {
        $req = $this->makeRequest( );
        $req->add('target',$name);
        return $req;
    }

    /**
     * 新規リクエストを生成する
     */
    public function makeRequest ( )
    {
        // リクエスト作成
        $request = new Request ( );

        // リクエストを監視する
        $request->addObserver($this);
        return $request;
    }

    /**
     * エラー判定
     */
    public function isError ($var)
    {
        if ($var instanceof ResultIF) return $var->isError();
        return false;
    }

    /**
     * Requestからのイベントを受け付ける
     */
    public function notify(Event\EventIF $event)
    {
        if ($event('target') instanceof RequestIF) {
            if(!$event->isStop()) {
                $this->recieve($event('target'));
            }
        }

        $this->eventNotify($event);
    }
    /**
     * リクエストを受け付ける
     */
    abstract public function recieve(RequestIF $request);
}
