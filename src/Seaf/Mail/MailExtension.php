<?php

namespace Seaf\Mail;

use Seaf\Core\Extension;
use Seaf\Core\Base;

class MailExtension extends Extension
{
	public function init( $prefix, $base )
	{
	}

	public function mapSendTo( $to, $subject, $body,  $from,  $headers = array())
	{
		mb_send_mail($to,$subject,$body,"From:".$from);
	}
}
