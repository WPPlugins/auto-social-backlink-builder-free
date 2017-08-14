<?php

class HttpRequestFree {
	
	protected $url;
	
	protected $data = array();
	
	protected $method = 'GET';	//'CUSTOMREQUEST';
	
	protected $timeout = false;
	
	public function __construct($url, $data = array(), $method = 'GET', $timeout = false) {
		$this->url = $url;
		$this->data = $data;
		$this->method = $method;
		$this->timeout = $timeout;

		if (! empty($data)) {
			if ($method == 'GET' || $method == 'HTTPGET') {
				$url .= (stripos($url, '?') !== false) ? '&' : '?';
				$url .= $this->buildQueryString($data);
				$this->url = $url;
				$this->data = array();
			}			
		}		
	}

	public function buildQueryString($data) {		
		return http_build_query($data);
	}
	
	public function url() {
		return $this->url;
	}
	
	public function data() {
		return $this->data;
	}

	public function method() {
		return $this->method;
	}

	public function timeout() {
		return $this->timeout;
	}
		
	public function __toString() {
		return $this->url; 
	}
}


class HttpClientFree {
	
	private $handle;
	
	private $request = false;
	
	private $result = false;
	
	private $error = false;
	
	public function __construct() {
		
	}
	
	public function init($request) {
		if (!is_resource($this->handle)) {
			
			//$user_agent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
			$user_agent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)';
			//$user_agent = 'Pinterest For iPhone / 1.4.3';  
			//$cookie_file = tempnam("tmp", "curl_cookie");
			$cookie_file = dirname(__FILE__).'/cookie.txt';

			$handle = curl_init();
			
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($handle, CURLOPT_HEADER, true);
			curl_setopt($handle, CURLOPT_AUTOREFERER, true);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($handle, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($handle, CURLOPT_COOKIEFILE, $cookie_file);
			curl_setopt($handle, CURLOPT_COOKIEJAR, $cookie_file);
			
			$this->handle = $handle;			
		}
		
		$this->request = $request;
		$this->result = false;
		$this->error = false;
				
		return $this->handle;	
	}
	
	public function setHeader($headers = array()) {
		foreach ($headers as $key => $value) {
			$httpheader[] = $key . ': ' . $value;
		}
		curl_setopt($this->handle, CURLOPT_HTTPHEADER, $httpheader);	 
	}
	
	public function get($url, $data = array(), $authinfo = null, $referer = null) {
		$request = new HttpRequestFree($url, $data, 'HTTPGET');
		return $this->execute($request, $authinfo, $referer);
	}
	
	public function post($url, $data = array(), $authinfo = null, $referer = null) {
		$request = new HttpRequestFree($url, $data, 'POST');
		return $this->execute($request, $authinfo, $referer);
	}
	
	public function execute($request, $authinfo = null, $referer = null) {				
		$handle = $this->init($request);
		
		curl_setopt($handle, constant('CURLOPT_' . $request->method()), true);
		curl_setopt($handle, CURLOPT_URL, $request->url());
		$data = $request->data();
		if ($data && !empty($data)) {
			curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		}
	
		if ($authinfo) {
			curl_setopt($handle, CURLOPT_USERPWD, $authinfo);
		}
		if ($referer) {
			curl_setopt($handle, CURLOPT_REFERER, $referer);
		}
		
		$response_content = curl_exec($handle);
		$http_status_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if (!$response_content) {
			 $error = curl_errno($handle) . ' - ' . curl_error($handle);
			 $this->error = $error;
			 $response_content = $error;
		}
		$this->result = new HttpResponseResultFree($http_status_code, $response_content);
		
		return $this->result;
		//return $http_status_code;
				
	}
	
	public function result() {
		return $this->result;
	}
	
	public function error() {
		return $this->error;
	}
	
	public function close() {
		curl_close($this->handle);
		$this->handle = null;
	}

}

class HttpResponseResultFree {
	
	private $content;
	private $body = '';
	private $status;
	private $headers = array();
	
	public function __construct($status = 200, $content = '') {
		$this->status = $status;
		$this->content = $content;
	}
	
	public function content() {
		return $this->content;
	}
	
	public function status() {
		if (!$this->status) {
			$this->parseHeader();
		}
		return $this->status;
	}
	
	public function body() {
		if (!$this->body) {
			$this->parseBody();
		}
		return $this->body;
	}
	
	public function headers() {
		if (empty($this->headers)) {
			$this->parseHeader();
		}		
		return $this->headers;
	}
	
	public function header($name) {
		if ($name) {
			$headers = $this->headers();
			return $headers[strtolower($name)];		
		} 
		return FALSE;
	}
	
	public function should_redirect() {
		if ($this->status() >= 300 && $this->status() < 400) {
			return $this->header('location');
		}
		return FALSE;
	}
	
	public function parseBody() {
		# Extract headers from response
		$pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
		
		$this->body = preg_replace($pattern, '', $this->content);
		
	}
	
	public function parseHeader() {
		$response = $this->content;
		
		# Extract headers from response
		$pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
		
		preg_match_all($pattern, $response, $matches);
		$headers = split("\r\n", str_replace("\r\n\r\n", '', array_pop($matches[0])));
		
		# Extract the version and status from the first header
		$version_and_status = array_shift($headers);
		preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
		$this->status = $matches[2];
		
		# Convert headers into an associative array
		foreach ($headers as $header) {
            list($header_name, $header_value) = explode(':', $header, 2);
            if ($header_name) {
            	$header_name = strtolower($header_name);
            	if ($header_name != 'set-cookie') {
            		$this->headers[$header_name] = trim($header_value);            		
            	} else {
            		//setcookie($header_name, trim($header_value));
            	}
            }            
		}
				
	}
	
	public function __toString() {
		return '[' . $this->status . ']' . $this->content;
	}

}


?>