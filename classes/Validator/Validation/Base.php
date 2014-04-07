<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator\Validation;

/**
 * バリデーションのベースクラス
 */
class Base
{
    /**
     * バリデーションメッセージ
     */
    protected $message = null;

    /**
     * 必須フラグ
     */
    private $flag;

    /**
     * パラメタ
     */
    protected $config;

    /**
     * バリデーションを作成する
     */
    public function __construct ($config, $validator)
    {
        $this->config = $config;
        if (isset($config['message'])) {
            $this->setMessage($config['message']);
        }
    }

    public function setMessage($code)
    {
        $this->message = $code;
    }


    /**
     * @param Value
     */
    public function valid ($value)
    {
        if ($this->_valid($value)) {
            return true;
        }
        return false;
    }

    /**
     * メッセージを取得する
     */
    public function getMessageCode( )
    {
        if ($this->message == null) {
            $class = get_class($this);
            $class = strtoupper(substr($class, strrpos($class, '\\')+1));
            $code = 'MESSAGE.INVALID.'.$class;
            $params = $this->getMessageParams();
            return array($code, $params);
        }else{
            return array($this->message, $this->getMessageParams());
        }
    }

    /**
     * メッセージパラメタを取得する
     */
    public function getMessageParams( )
    {
        return $this->config;
    }
}
