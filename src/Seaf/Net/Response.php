<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * @copyright Copyright (2) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Net;

use Seaf\Seaf;

class Response 
{
	/**
	 * @var int
	 */
	protected $status = 200;

	/**
	 * @var array
	 */
	protected $headers = array();

	/**
	 * @var string
	 */
	protected $body;

	/**
	 * @var array
	 */
	public static $codes = array(
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',

		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',

		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',

		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);

	/**
	 * @param int
	 * @return object $this
	 */
	public function status($code)
	{
		if(array_key_exists($code, self::$codes))
		{
			$this->status = $code;
		}
		else 
		{
			throw new InvalidStatusCode($code);
		}
		return $this;
	}

	/**
	 * @param string
	 * @param value
	 * @return object $this
	 */
	public function header($name, $value = null)
	{
		if(is_array($name))
		{
			foreach($name as $k=>$v) $this->header[$k] = $v;
		}
		else 
		{
			$this->headers[$name] = $value;
		}
		return $this;
	}

	/**
	 * @param string 
	 * @return object $this
	 */
	public function write($str)
	{
		$this->body .= $str;
		return $this;
	}

	/**
	 * @return object $this
	 */
	public function clear()
	{
		$this->status = 200;
		$this->headers = array();
		$this->body = '';
		return $this;
	}
	/**
	 * @param string
	 * @return object $this
	 */
	public function cache($expires)
	{
		if($expires === false)
		{
			$this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
			$this->headers['Cache-Control'] = array(
				'no-store, no-cache, must-revalidate',
				'post-check=0, pre-check=0',
				'max-age=0'
			);
			$this->headers['Pragma'] = 'no-cache';
		}else{
			$expires = is_int($expires) ? $expires: strtotime($expires);
			$this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
			$this->headers['Cache-Control'] = 'max-age='.($expires - time());
		}
		return $this;
	}


	/**
	 * @return object $this
	 */
	public function sendHeaders()
	{
		if(strpos(php_sapi_name(), 'cgi') !== false) 
		{
			header(
				sprintf(
					'Status: %d %s',
					$this->status,
					self::$codes[$this->status]
				),
				true
			);
		}
		else 
		{
			header(
				sprintf(
					'%s %d %s',
					(isset($_SERVER['SERVER_PROTOCOL']) ?  $_SERVER['SERVER_PROTOCOL']: 'HTTP/1.1'),
					$this->status,
					self::$codes[$this->status]
				),
				true,
				$this->status
			);
		}
		foreach($this->headers as $field => $value) 
		{
			if(is_array($value)) 
			{
				foreach($value as $v) 
				{
					header($field.': '.$v, false);
				}
			}
			else
			{
				header($field.': '.$value);
			}
		}
		return $this;
	}

	/**
	 * 
	 */
	public function send( ) 
	{
		if(ob_get_length()>0)
		{
			ob_end_clean();
		}
		if(!headers_sent()) 
		{
			$this->sendHeaders();
		}
		Seaf::stop($this->body);
	}
}
