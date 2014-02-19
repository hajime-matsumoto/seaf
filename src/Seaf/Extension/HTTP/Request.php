<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * @copyright Copyright (2) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Extension\HTTP;


class Request {

	private $data;

	public function __get($key) {
		return $this->data[$key];
	}

	public function __set($key, $value) {
		$this->data[$key] = $value;
	}

	public  function __construct($data = false){
		if( $data == false ){
			$data = array(
				'url' => $this->getEnv('REQUEST_URI', '/'),
				'base' => str_replace(array('\\',' '), array('/','%20'), dirname($this->getEnv('SCRIPT_NAME'))),
				'method' => $this->getMethod(),
				'referrer' => $this->getEnv('HTTP_REFERER'),
				'ip' => $this->getEnv('REMOTE_ADDR'),
				'ajax' => $this->getEnv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest',
				'scheme' => $this->getEnv('SERVER_PROTOCOL', 'HTTP/1.1'),
				'user_agent' => $this->getEnv('HTTP_USER_AGENT'),
				'body' => file_get_contents('php://input'),
				'type' => $this->getEnv('CONTENT_TYPE'),
				'length' => $this->getEnv('CONTENT_LENGTH', 0),
				'query' => $_GET,
				'data' => $_POST,
				'cookies' => $_COOKIE,
				'files' => $_FILES,
				'secure' => $this->getEnv('HTTPS', 'off') != 'off',
				'accept' => $this->getEnv('HTTP_ACCEPT'),
				'proxy_ip' => $this->getProxyIpAddress()
			);
		}
		$this->data = $data;
		$this->init();
	}


	public function init() {
		if ( $this->base != '/' && strlen($this->base) > 0 && strpos($this->url, $this->base) === 0 ) {
			if (php_sapi_name() !== 'cli-server') {
				$this->url = substr($this->url, strlen($this->base) );
			}
		}
		if (empty($this->url)) {
			$this->url = "/";
		}else{
			$_GET += self::parseQuery($this->url);
			$parts =  explode('?', $this->url, 2);
			$this->url = $parts[0];
			$this->query->setData( $_GET );
		}
	}

	public static function parseQuery($url) {
		$params = array();
		$args = parse_url($url);
		if(isset($args['query'])) {
			parse_str($args['query'], $params);
		}
		return $params;
	}

	private function getProxyIpAddress() {
		static $forwarded = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED'
		);

		$flags = \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;

		foreach ($forwarded as $key) {
			if (array_key_exists($key, $_SERVER)) {
				sscanf($_SERVER[$key], '%[^,]', $ip);
				if (filter_var($ip, \FILTER_VALIDATE_IP, $flags) !== false) {
					return $ip;
				}
			}
		}

		return '';
	}

	private function getMethod(){
		if ( isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ) {
			return $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}elseif (isset($_REQUEST['_method'])) {
			return $_REQUEST['method'];
		}
		return $this->getEnv('REQUEST_METHOD', 'GET');
	}

	private function getEnv($var, $default = '') {
		return isset($_SERVER[$var]) ? $_SERVER[$var]: $default;
	}

}
