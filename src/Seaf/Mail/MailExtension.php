<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * メール送信エクステンションクラス定義
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Mail;

use Seaf\Core\Extension;
use Seaf\Core\Base;

/**
 * メールエクステンションクラス
 */
class MailExtension extends Extension
{
    public function initExtension( )
    {
    }

    /**
     * @SeafBind sendTo
     */
    public function mapSendTo( $to, $subject, $body,  $from,  $headers = array())
    {
        mb_send_mail($to,$subject,$body,"From:".$from);
    }
}
