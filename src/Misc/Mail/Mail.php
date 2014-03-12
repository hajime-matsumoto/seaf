<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Misc\Mail;

use Seaf;

/**
 * Mail
 */
class Mail
{

    private $to,$from,$subject,$body;

    public function __construct ( )
    {
        $this->init( );
    }

    public function init ( ) {
        $this->to = $this->from = $this->subject = $body = '';
    }

    public function __call ($name, $params)
    {
        if (in_array($name, array('to','from','subject','body'))) {
            $this->$name = $params[0];
            return $this;
        }

        throw new \Exception('Invalid Call');
    }

    public function submit (  )
    {
        mb_send_mail($this->to, $this->subject, $this->body, 'From:'.$this->from);
        return $this;
    }
}
