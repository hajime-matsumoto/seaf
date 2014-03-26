<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Mailer;

use Seaf;
use Seaf\Pattern;
use Seaf\Exception;

class Mail
{
    private $mailer;

    private $attrs = array('to', 'from', 'subject', 'body');

    private $to, $from, $subject, $body, $headers = array();

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function fromHeader($name, $address)
    {
        $this->headers['From'] = sprintf("%s <%s>", mb_encode_mimeheader($name), $address);
        return $this;
    }

    public function header($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function buildHeaders( )
    {
        $headers = '';
        foreach ($this->headers as $k=>$v) {
            $headers.= sprintf("%s: %s\n", $k, $v);
        }
        return $headers;
    }

    public function template ($tpl, $vars)
    {
        $this->body($this->mailer->view()->render($tpl, $vars));
        return $this;
    }

    public function submit ( )
    {
        Seaf::Logger('Mailer')->debug("Headers:".$this->buildHeaders());
        $result = mb_send_mail($this->to, $this->subject, $this->body, $this->buildHeaders());
        Seaf::Logger('Mailer')->debug("Result:".$result);
        return $this;
    }

    public function __call ($name, $params)
    {
        if (in_array($name, $this->attrs)) {
            $this->$name = $params[0];
            return $this;
        }

        throw new Exception\InvalidCall($name, $this);
    }
}
