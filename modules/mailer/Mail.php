<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Mailer;

use Seaf;
use Seaf\Pattern;
use Seaf\Exception;

/**
 * メール用の構造体
 * 基本的な情報の保存と送信機能を持つ
 */
class Mail
{
    private $mailer;

    private $attrs = array('to', 'from', 'subject', 'body');

    private $to;
    private $from;
    private $subject;
    private $body;
    private $headers = array();

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * From: Hoge <huga@hoge.com>
     * ヘッダーを追加する
     *
     * @param string
     * @param string
     * @return Mail
     */
    public function fromHeader($name, $address)
    {
        $this->headers['From'] = sprintf("%s <%s>", mb_encode_mimeheader($name), $address);
        return $this;
    }

    /**
     * ヘッダーを追加する
     *
     * @param string
     * @param string
     * @return Mail
     */
    public function header($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * ヘッダーを作成する
     *
     * @return string
     */
    protected function buildHeaders( )
    {
        $headers = '';
        foreach ($this->headers as $k=>$v) {
            $headers.= sprintf("%s: %s\n", $k, $v);
        }
        return $headers;
    }

    /**
     * テンプレートを使って本文を作成する
     *
     * @param string
     * @param array
     * @return Mail
     */
    public function template ($tpl, $vars)
    {
        $this->body($this->mailer->view()->render($tpl, $vars));
        return $this;
    }

    /**
     * メールを送信する
     *
     * @return Mail
     */
    public function submit ( )
    {
        Seaf::Logger('Mailer')->debug("Headers:".$this->buildHeaders());
        $result = mb_send_mail($this->to, $this->subject, $this->body, $this->buildHeaders());
        Seaf::Logger('Mailer')->debug("Result:".$result);
        return $this;
    }

    /**
     * 定義されてない呼び出しは、パラメタの
     * セッターとして使用する
     *
     * @param string
     * @param array
     * @exception Exception\InvalidCall
     */
    public function __call ($name, $params)
    {
        if (in_array($name, $this->attrs)) {
            $this->$name = $params[0];
            return $this;
        }

        throw new Exception\InvalidCall($name, $this);
    }
}
