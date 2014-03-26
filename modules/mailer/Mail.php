<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Mailer;

use Seaf;
use Seaf\Pattern;
use Seaf\Exception;

class Mail
{
    private $mailer;

    private $attrs = array('to', 'from', 'subject', 'body');

    private $to, $from, $subject, $body;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function template ($tpl, $vars)
    {
        $this->body($this->mailer->view()->render($tpl, $vars));
        return $this;
    }

    public function submit ( )
    {
        mb_send_mail($this->to, $this->subject, $this->body, "<".$this->from.">");
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
