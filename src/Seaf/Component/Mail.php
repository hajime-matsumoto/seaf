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

namespace Seaf\Component;

use Seaf\DI\DIContainer;
use Seaf\Collection\ArrayCollection;

/**
 * Mailコンポーネント
 */
class Mail 
{
    /**
     * @param object $di
     */
    public function acceptDIContainer( DIContainer $di )
    {
        $this->di = $di;
    }

    /**
     */
    public function sendTo( $to, $subject, $body,  $from,  $headers = array())
    {
        mb_send_mail($to,$subject,$body,"From:".$from);
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
