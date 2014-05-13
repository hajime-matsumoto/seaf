<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Command;

use Seaf\Base\Container;
use Seaf\Base\Event;
use Seaf\Util\Util;

/**
 *  Resultの管理クラス
 */
class Result extends Container\ArrayContainer implements ResultIF,Event\ObservableIF
{
    use Event\ObservableTrait;

    private $isErrorFlg = false;

    /**
     * @See ResultIF
     */
    public function addReturnValue($value)
    {
        $this->add('returnValue', $value);
    }

    /**
     * @See ResultIF
     */
    public function fetchReturnValue( )
    {
        return $this->pop('returnValue');
    }

    /**
     * @See ResultIF
     */
    public function error($code, $message = '', $params = array())
    {
        $this->isErrorFlg = true;

        $this->add('error', [
            'code' => $code,
            'message' => $message,
            'params' => $params
        ]);
        return $this;
    }

    /**
     * @See ResultIF
     */
    public function isError( )
    {
        //return $this->isEmpty('error') ? false: true;
        return $this->isErrorFlg;
    }

    public function resetError( )
    {
        $this->isErrorFlg = false;
    }

    public function getErrorMessage ( )
    {
        $text = '';
        foreach($this->get('error') as $e) {
            $text.= sprintf(
                "%s:%s;",
                $e['code'],
                (empty($e['message']) ? '': Util::dump($e['message'],true))
            );
        }
        return $text;
    }
    public function log ($log)
    {
        $this->add('log', $log);
    }

}
