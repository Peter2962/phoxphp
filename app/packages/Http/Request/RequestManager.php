<?php
###############################################
# This file is part of phoxphp framework.
################################################
namespace Package\Http\Request;

use StdClass;
use Exception;
use Package\Http\Response;
use Package\Http\Request\ProxyManager;
use Package\Http\Request\Exceptions\InvalidAuthenticationTypeException;

class RequestManager
{

	/**
	* @var 		$timeout
	* @access 	protected
	*/
	protected 	$timeout = 1200;

	/**
	* @var 		$method
	* @access 	private
	*/
	private 	$method;

	/**
	* @var 		$requestUrl
	* @access 	private
	*/
	private 	$requestUrl;
	
	/**
	* @var 		$headers
	* @access 	private
	*/
	private 	$headers = [];

	/**
	* @var 		$requestReady
	* @access 	private
	*/
	private 	$requestReady = false;

	/**
	* @var 		$requestMethods
	* @access 	private
	*/
	private 	$requestMethods = ['get' => 'GET', 'post' => 'POST', 'put' => 'PUT', 'delete' => 'DELETE', 'head' => 'HEAD'];

	/**
	* @var 		$curlRequest
	* @access 	private
	*/
	private static $curlRequest = true;

	/**
	* @var 		$curlChannel
	* @access 	private
	*/
	private 	$curlChannel;

	/**
	* @var 		$disableSSL
	* @access 	private
	*/
	private static $disableSSL = false;

	/**
	* @var 		$requestWrapper
	* @access 	private
	*/
	private static $requestWrapper;

	/**
	* @var 		$requestParameters
	* @access 	private
	*/
	private 	$requestParameters = [];

	/**
	* @var 		$authentication
	* @access 	private
	*/
	private static $authentication = [];

	/**
	* @var 		$returnTransfer
	* @access 	private
	*/
	private 	$returnTransfer = 1;

	/**
	* @var 		$url
	* @access 	public
	*/
	public 		$url;

	/**
	* @var 		$get
	* @access 	public
	*/
	public 		$get;

	/**
	* @var 		$post
	* @access 	public
	*/
	public 		$post;

	/**
	* @var 		$put
	* @access 	public
	*/
	public 		$put;

	/**
	* @var 		$delete
	* @access 	public
	*/
	public 		$delete;

	/**
	* @var 		$error
	* @access 	private
	*/
	private 	$error;

	/**
	* @var 		$errorNo
	* @access 	private
	*/
	private 	$errorNo;

	/**
	* @var 		$attachedProxy
	* @access 	private
	*/
	private 	$attachedProxy = [];

	/**
	* @var 		$proxyRequest
	* @access 	private
	*/
	private 	$proxyRequest = false;

	/**
	* @var 		$tunnelProxy
	* @access 	private
	*/
	private 	$tunnelProxy = 0;

	/**
	* @var 		$authenticationType
	* @access 	private
	*/
	private 	$authenticationType = '';

	/**
	* @constant FULL_PATH
	*/
	const 		FULL_PATH = 'WITH_PARAMETERS';

	/**
	* @constant PARTIAL_PATH
	*/
	const 		PARTIAL_PATH = 'EXCLUDE_PARAMETERS';

	/**
	* Constructor
	*
	* @access 	public
	* @return 	void
	*/
	public function __construct()
	{
		$this->get = $_GET;
		$this->post = $_POST;
		$this->put = $_POST;
	}

	/**
	* __get magic method: Allows retrieval of input.
	*
	* @param 	$property
	* @access 	public
	* @return 	Mixed
	*/
	public function __get($property)
	{
		if (!property_exists($this, $property)) {
			$pregInputRetrieve = preg_match("/get.*[A-Za-z]/", $property);

			if ($pregInputRetrieve) {
				$input = str_replace('get', '', $property);
				if (isset($this->input()->$input)) {
					return $this->input()->$input; 
				}
			}
		}
	}

	/**
	* __call magic method: Checks the current request method.
	*
	* Method check usage:
	* To check GET 		: \Http\RequestManager::isGet()
	* To check POST 	: \Http\RequestManager::isPost()
	* To check PUT 		: \Http\RequestManager::isPut()
	* To check DELETE 	: \Http\RequestManager::isDelete()
	* To check HEAD 	: \Http\RequestManager::isHead() 
	*
	* @param 	$method
	* @param 	$parameters
	* @access 	public
	* @return 	Boolean
	*/
	public function __call($method, $parameters)
	{
		if (!method_exists($this, $method)) {
			$pregMethodTest = preg_match("/is.*[A-Za-z]/", $method);

			if ($pregMethodTest) {
				$methodInput = str_replace('is', '', $method);
				$serverMethod = $this->server()->REQUEST_METHOD;
				if ($serverMethod == strtoupper($methodInput) && isset($this->requestMethods[strtolower($methodInput)])) {
					return true;
				}
				return false;
			}
		}
	}

	/**
	* Sends a GET request.
	*
	* @param 	$url <String>
	* @access 	public
	* @return 	Response
	*/
	public function get($url)
	{
		$this->url = $url;
		$this->method = $this->requestMethods['get'];

		$request = $this->doRequest();
		return $request;
	}

	/**
	* Sends a PUT request.
	*
	* @param 	$url <String>
	* @access 	public
	* @return 	Response
	*/
	public function put($url)
	{
		$this->url = $url;
		$this->method = $this->requestMethods['put'];

		$request = $this->doRequest();
		return $request;
	}

	/**
	* Sends a DELETE request.
	*
	* @param 	$url <String>
	* @access 	public
	* @return 	Response
	*/
	public function delete($url)
	{
		$this->url = $url;
		$this->method = $this->requestMethod['delete'];

		$request = $this->doRequest();
		return $request;
	}

	/**
	* Sends a POST request
	*
	* @param 	$url <String>
	* @access 	public
	* @return 	Response
	*/
	public function post($url) {
		$this->url = $url;
		$this->method = $this->requestMethods['post'];

		$request = $this->doRequest();
		return $request;
	}

	/**
	* Sends a HEAD request
	*
	* @param 	$url <String>
	* @access 	public
	* @return 	Response
	*/
	public function head($url) {
		$this->url = $url;
		$this->method = $this->requestMethods['head'];

		$request = $this->doRequest();
		return $request;
	}	

	/**
	* Returns the request path
	*
	* @param 	$toString <Boolean>
	* @access 	public
	* @return 	Array|String
	*/
	public function path($toString = false) {
		$uri = explode("/", $this->uri());
		$scriptName = explode("/", $this->server()->SCRIPT_NAME);
		$diff = array_diff($uri, $scriptName);
		$path = array_values($diff);

		if (true == boolval($toString)) {
			$path = implode('/', $path);
		}

		return $path;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function uri()
	{
		return $this->server()->REQUEST_URI;
	}

	/**
	* Returns an object of $_POST global variable.
	*
	* @access 	public
	* @return 	Object
	*/
	public function input()
	{
		return (Object) $_POST;
	}

	/**
	* Returns an object of $_SERVER global variable.
	*
	* @access 	public
	* @return 	Object
	*/
	public function server()
	{
		return (Object) $_SERVER;
	}

	/**
	* Returns the server request method.
	*
	* @access 	public
	* @return 	String
	*/
	public function requestMethod()
	{
		return $this->server()->REQUEST_METHOD;
	}

	/**
	* Returns the full request path without the query string. To return the
	* request path with query string, a boolean value of true should be passed
	* as an argument.
	*
	* @param 	$appendQueryString <Boolean>
	* @access 	public
	* @return 	String
	*/
	public function pathinfo($appendQueryString = false)
	{
		$uri = $this->getCleanUrl($this->server()->REQUEST_URI);

		if (boolval($appendQueryString) == true) {
			$uri = $this->server()->REQUEST_URI;
		}

		return $uri;
	}

	/**
	* Returns an object of $_SERVER global variable.
	*
	* @access 	public
	* @return 	Object
	*/
	public static function all() {
		return (Object) $_REQUEST;
	}

	/**
	* Checks if the request path matches @param $path.
	*
	* @param 	$path 	<String>
	* @param 	$option Request::PARTIAL|Request::FULL
	* @access 	public
	* @return 	Boolean
	*/
	public function matches($path='', $option = RequestManager::PARTIAL_PATH)
	{
		$response = false;
		switch ($option) {
			case RequestManager::PARTIAL_PATH:
				$uri = $this->getCleanUrl($this->pathinfo(true));
				break;
			case RequestManager::FULL_PATH:
				$uri = $this->pathinfo();
				break;
			default:
				return null;
				break;
		}

		if ($path == $uri) {
			$response = true;
		}

		return $response;
	}

	/**
	* Flags disableSSL property as false.
	*
	* @access 	public
	* @return 	void
	*/
	public static function disableSSL()
	{
		RequestManager::$disableSSL = true;
	}

	/**
	* Tells the request to use CURL request over FGC by setting RequestManager::$curlRequest to false.
	*
	* @access 	public
	* @return 	void
	*/
	public static function useFGC()
	{
		RequestManager::$curlRequest = false;
	}

	/**
	* Tells the request to use FGC request over CURL by setting RequestManager::$curlRequest to true.
	*
	* @access 	public
	* @return 	void
	*/
	public static function useCURL()
	{
		RequestManager::$curlRequest = true;
	}

	/**
	* @param 	$proxyName <String>
	* @param 	$tunnelProxy <Boolean>
	* @access 	public
	* @return 	Array
	*/
	public function proxy($proxyName='', $tunnelProxy = false)
	{
		$this->tunnelProxy = (Integer) $tunnelProxy;
		$this->proxyRequest = true;

		if (ProxyManager::exists($proxyName)) {
			$this->attachedProxy = (Object) ProxyManager::getProxy($proxyName);
		}

		return $this->attachedProxy;
	}

	/**
	* Sets authentication username and password.
	*
	* @param 	$username <String>
	* @param 	$password <String>
	* @param 	$authenticationType <String>
	* @access 	public
	* @return 	void
	*/
	public function authorize($username='', $password='', $authenticationType='basic')
	{
		RequestManager::$authentication['username'] = $username;
		RequestManager::$authentication['password'] = $password;
		try {
			if (null == $this->getAuthConfig($authenticationType, "")) {
				throw new InvalidAuthenticationTypeException(
					sprintf("Authentication type %s is not available.",$authenticationType)
				);
			}
			$this->authenticationType = $authenticationType;
		}catch(InvalidAuthenticationTypeException $e) {
			exit($e->getMessage());
		}		
	}

	/**
	* Appends request header.
	*
	* @param 	$key <String> | null
	* @param 	$value <String> | null
	* @access 	public
	* @return 	void
	*/
	public function setHeader($key=null, $value=null)
	{
		$this->headers[$key] = $value;
	}

	/**
	* Tells FGC(file_get_contents) request what http wrapper to use.
	* A valid wrapper is expected.
	*
	* @param 	$wrapper <String>
	* @access 	public
	* @return 	void
	*/
	public static function setWrapperForFGC($wrapper=null)
	{
		RequestManager::$requestWrapper = $wrapper;
	}

	/**
	* Appends url parameters that will be sent in a request.
	*
	* @param 	$parameter <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @return 	void
	*/
	public function setUrlParameters($parameter=null, $value=null)
	{
		$this->requestParameters[$parameter] = $value;
	}

	/**
	* Sets the request timeout.
	*
	* @param 	$time <Integer> {in seconds}
	* @access 	public
	* @return 	void
	*/
	public function setTimeout($time=60)
	{
		if (ctype_digit($time) && !$time < 0) {
			$this->timeout = $time;
		}	
	}

	/**
	* @param 	$response <Object> \Http\Response\Interfaces\ResponseInterface 
	* @access 	public
	* @return 	Object
	* @deprecated
	*/
	public function getReponse(Response $response) : Response
	{
		return $this->response = $response;
	}

	/**
	* Returns error generated from request. Works with CURL only.
	*
	* @access 	public
	* @return 	String
	*/
	public function getError()
	{
		return $this->error;
	}

	/**
	* Returns the generated error number. Works with CURL only.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function getErrorNo()
	{
		return $this->errorNo;
	}

	/**
	* @param 	$key <String>
	* @access 	private
	* @return 	String
	*/
	private function getConfig($key='')
	{
		$config = [
			'proxy' 		  	=> CURLOPT_PROXY,
			'proxy_port' 	  	=> CURLOPT_PROXYPORT,
			'proxy_auth' 	  	=> CURLOPT_PROXYUSERPWD,
			'proxy_http_tunnel' => CURLOPT_HTTPPROXYTUNNEL,
			'auth'				=> CURLOPT_USERPWD,
			'auth_http_basic' 	=> CURLOPT_HTTPAUTH,
			'auth_basic' 	  	=> CURLAUTH_BASIC,
			'auth_digest' 	  	=> CURLAUTH_DIGEST,
			'auth_any' 		  	=> CURLAUTH_ANY,
			'auth_any_safe'   	=> CURLAUTH_ANYSAFE
		];

		return $config[$key];
	}

	/**
	* Returns configuration for authorization type provided.
	*
	* @param 	$key <String>
	* @param 	$authInfo <String>
	* @access 	private
	* @return 	Object|Boolean
	*/
	private function getAuthConfig($key='', $authInfo='')
	{
		$config = [
			'basic' => ['option' => $this->getConfig('auth_basic'), 'value' => "Authorization: Basic ".base64_encode($authInfo)],
			'digest' => ['option' => $this->getConfig('auth_digest'), 'value' => $authInfo]
		];

		return (isset($config[$key])) ? (Object) $config[$key] : null;
	}

	/**
	* Initializes a request. Defaults to CURL.
	* To use FGC(file_get_contents) for a request, static method: \Http\RequestManager::useFileRequest
	* must be called before the request object method will be called. This also applies to CURL request.
	* To send the request using CURL again, \Http\useCurlRequest must be called.
	*
	* @access 	private
	* @return 	\Http\Response
	*/
	private function doRequest() : Response
	{
		if (!in_array($this->method, array_values($this->requestMethods))) {
			throw new Exception(sprintf("Unresolved method %s", $this->method));
		}

		$this->method = $this->requestMethods[strtolower($this->method)];
		if (false == RequestManager::$curlRequest) {
			return $this->doFileRequest();
		}

		(Array)  $headers = $this->resolveHeaders();
		(String) $customRequest = '';

		if (!empty($this->requestParameters)) {
			$this->url = $this->buildUrl($this->url, $this->requestParameters);
		}

		if (!function_exists('curl_init') || !extension_loaded('curl')) {
			throw new \RuntimeException('Curl extension is not available.');
		}

		$this->curlChannel = curl_init($this->url);
		curl_setopt($this->curlChannel, CURLOPT_RETURNTRANSFER, $this->returnTransfer);
		curl_setopt($this->curlChannel, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->curlChannel, CURLOPT_CUSTOMREQUEST, $this->method);

		// Setting post fields if method is 'POST' or 'PUT'
		if ($this->method == 'POST' || $this->method == 'PUT' || $this->method == 'DELETE') {
			curl_setopt($this->curlChannel, CURLOPT_POST, 1);
			curl_setopt($this->curlChannel, CURLOPT_POSTFIELDS, http_build_query($this->requestParameters));

			// If no content-type is specified, set an automatic content type.
			if (!isset($this->headers['Content-type']) || !isset($this->headers['Content-Type']) || !isset($this->headers['content-type'])) {
				$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
			}
		}

		// Is disableSSL property true? tell CURL to disable ssl.
		if (true == RequestManager::$disableSSL) {
			curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->curlChannel, CURLOPT_SSL_VERIFYHOST, false);
		}

		if (boolval($this->proxyRequest) == true) {
			$proxyIp = (isset($this->attachedProxy->address)) ? $this->attachedProxy->address : ProxyManager::$defaultProxyIp;
			$proxyPort = (isset($this->attachedProxy->port)) ? $this->attachedProxy->port : ProxyManager::$defaultProxyPort;

			curl_setopt($this->curlChannel, $this->getConfig('proxy'), $proxyIp);
			curl_setopt($this->curlChannel, $this->getConfig('proxy_port'), $proxyPort);

			if (ctype_digit($this->tunnelProxy) && intval($this->tunnelProxy) == 1) {
				curl_setopt($this->curlChannel, $this->getConfig('proxy_http_tunnel'), 1);
			}
		}

		if (!empty(RequestManager::$authentication)) {
			$authenticationType = $this->authenticationType;		
			$username = RequestManager::$authentication['username'];
			$password = RequestManager::$authentication['password'];
			
			$authenticationTypeConfig = $this->getAuthConfig(
				$authenticationType,
				$username.":".$password
			);

			$authenticationTypeConfigOption = $authenticationTypeConfig->option;
			$authenticationTypeConfigValue = $authenticationTypeConfig->value;
			
			$authenticationInfoStapler = (boolval($this->proxyRequest) == true) ? $this->getConfig('proxy_auth') : $this->getConfig('auth');
			$authInfo = (boolval($this->proxyRequest) == true) ? $username.":".$password : $authenticationTypeConfigValue;

			curl_setopt($this->curlChannel, $authenticationInfoStapler, $authInfo);

			if (boolval($this->proxyRequest) == false) {
				curl_setopt($this->curlChannel, CURLOPT_HTTPAUTH, $authenticationTypeConfigOption);
			}
		}

		if (ctype_digit($this->timeout) && $this->timeout > 0) {
			curl_setopt($this->curlChannel, CURLOPT_TIMEOUT, $this->timeout);
		}

		curl_setopt($this->curlChannel, CURLOPT_CONNECTTIMEOUT, 0);

		if (sizeof($this->headers) > 0) {
			curl_setopt($this->curlChannel, CURLOPT_HTTPHEADER, $this->headers);
		}

		curl_setopt($this->curlChannel, CURLOPT_HEADER, 1);
		curl_setopt($this->curlChannel, CURLOPT_VERBOSE, 1);
		curl_setopt($this->curlChannel, CURLOPT_HEADER, 1);
		
		$response = curl_exec($this->curlChannel);
		$this->response = curl_getinfo($this->curlChannel);

		$headerSize = curl_getinfo($this->curlChannel, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $headerSize);
		$body = substr($response, $headerSize);

		$this->error = curl_error($this->curlChannel);
		$this->errorNo = curl_errno($this->curlChannel);

		curl_close($this->curlChannel);

		/**
		* Returns \Http\Response
		*
		* @param 	\Http\Request $this | Injected into response constructor.
		* @param 	$body String | Response body.
		* @param 	$header String | Response header resolved from CURL.
		*/
		return new Response($this, $body, $header);
	}

	/**
	* Sends a request using FGC(file_get_contents) function with stream_context.
	* This method of request does not return any values but dumps the content of the request
	* 
	*
	* @access 	private
	* @return 	void
	*/
	private function doFileRequest()
	{
		$requestWrappers = ['http'];
		if (!in_array(RequestManager::$requestWrapper, $requestWrappers)) {
			throw new Exception(sprintf("Http wrapper %s not accecpted.", RequestManager::$requestWrapper));
		}

		$wrapper = RequestManager::$requestWrapper;
		$requestWrapperStreamContext = [];
		$requestWrapperStreamContext['method'] = $this->method;

		if ($this->method == 'POST') {
			$requestWrapperStreamContext['content'] = http_build_query($this->requestParameters);
		}

		if (!empty(RequestManager::$authentication)) {
			$username = RequestManager::$authentication['username'];
			$password = RequestManager::$authentication['password'];

			$this->headers['Authorization'] = 'Basic '.base64_encode($username.':'.$password);
		}

		$headers = $this->resolveHeaders(true);
		if (gettype($headers)=='array' && !empty($headers)) {
			$headers = implode("\r\n", $headers[0]);
		}

		$requestWrapperStreamContext['header'] = $headers;
		$requestWrapperStreamContext['timeout'] = (Integer) $this->timeout;

		if (boolval($this->proxyRequest) == true) {
			$proxyIp = (isset($this->attachedProxy->address)) ? $this->attachedProxy->address : ProxyManager::$defaultProxyIp;
			$proxyPort = (isset($this->attachedProxy->port)) ? $this->attachedProxy->port : ProxyManager::$defaultProxyPort;

			$requestWrapperStreamContext['proxy'] = $this->createProxyUrl('tcp', $proxyIp, $proxyPort);
		}

		$requestContext = [
			$wrapper => $requestWrapperStreamContext
		];	

		if (true == RequestManager::$disableSSL) {
			$requestContext['ssl'] = [
				'verify_peer_name' => false,
				'verify_peer' => false
			];
		}

		$body = htmlentities(file_get_contents($this->url, false, stream_context_create($requestContext)));
		// Since $http_response_header is only available after a request has been made, 
		// it is passed as a parameter to the response object.
		return new Response(
			$this,
			$body,
			$http_response_header = (isset($http_response_header)) ? $http_response_header : ['']
		);
	}

	/**
	* Creates a full url with query string parameters by calling http_build_query on
	* @param $parameters array and then concatenating it with @param $url.
	*
	* @param 	$url <String>
	* @param 	$parameters <Array>
	* @access 	private
	* @return 	String
	*/
	private function buildUrl($url, array $parameters = [])
	{
		$parameters = http_build_query($parameters);
		return $url.'?'.$parameters;
	}

	/**
	* @param 	$fgc <Boolean>
	* @access 	private
	* @return 	Array
	*/
	private function resolveHeaders($fgc=false)
	{
		(Array) $headers = [];
		$headerValue = array_values($this->headers);
		foreach(array_keys($this->headers) as $i => $key) {
			if (true == gettype($fgc)) {
				$headers[] = "$key: $headerValue[$i]\r\n";
			}else{
				$headers[] = [$key.': '.$headerValue[$i]];
			}
		}

		return $headers;
	}

	/**
	* Strips query string parameters from a url if any.
	*
	* @param 	$url <String>
	* @access 	private
	* @return 	String
	*/
	private function getCleanUrl($url='')
	{
		$preg=preg_match("/\?(.*?)/", $url, $match);
		if ($preg) {
			$queryPipePosition = strpos($url, '?');
			$queryString = substr($url, 0, $queryPipePosition);
			$url = $queryString;
		}
		return $url;
	}

	/**
	* Builds a proxy url from three parameters passed as arguments
	* to this method.
	*
	* @param 	$protocol <String>
	* @param 	$ip <String>
	* @param 	$port <Integer>
	* @access 	private
	* @return 	String
	*/
	private function createProxy($protocol='tcp', $ip='', $port='')
	{
		return $protocol.'://'.$ip.':'.$port;
	}

}