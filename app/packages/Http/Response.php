<?php
###############################################
# This file is part of phoxphp framework.
################################################
namespace Package\Http;

use Package\Http\Request\RequestManager;

class Response {
	
	/**
	* @var 		$request
	* @access 	private
	*/
	private 	$request;

	/**
	* @var 		$body
	* @access 	private
	*/
	private 	$body;

	/**
	* @var 		$headers
	* @access 	private
	*/
	private 	$headers = [];

	/**
	* Constructor
	*
	* @param 	$request \Http\Request\Interfaces\RequestInterface
	* @param 	$body <String>	Response body | Response body of request
	* @param 	$headers <Mixed> Response headers | Headers fetched from request
	* @access 	public
	* @return 	void
	*/
	public function __construct(RequestManager $request, $body='', $headers=null) {
		$this->request = (Object) $request->getReponse($this);
		$this->body = $body;
		$this->headers = $headers;

		if (gettype($headers) == 'string') {
			$this->headers = explode("\n", $headers);
		}
	}

	/**
	* Sets a response header.
	*
	* @param 	$key <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @return 	String
	*/
	public function setHeader($key='', $value='') {
		$this->headers[$key] = $value;
	}

	/**
	* Return the body of a response.
	*
	* @param 	$setDecode <Boolean>
	* @access 	public
	* @return 	String
	*/
	public function body($setDecode=false) {
		$body = $this->body;
		return ($setDecode == true) ? html_entity_decode($body) : $body;
	}

	/**
	* Returns status code returned from request.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function statusCode() {
		if (!isset($this->request->http_code)) {
			$this->request->http_code = '';
			
			$httpCodeSegment = $this->headers[0];
			$preg = preg_match("/[0-9][0-9][0-9]/", $httpCodeSegment, $match);
			if ($preg) {
				$this->request->http_code = $match[0];
			}
		}

		return $this->request->http_code;
	}

	/**
	* Sets or returns the response content type.
	*
	* @param 	$contentType <String>
	* @access 	public
	* @return 	String
	*/
	public function contentType($contentType='') {
		if ('' !== $contentType)
			$this->request->content_type = $contentType;
		return $this->request->content_type;
	}

	/**
	* Returns the response content length.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function contentLength() {
		return $this->request->download_content_length;
	}

	/**
	* Returns the response redirect url.
	*
	* @access 	public
	* @return 	String
	*/
	public function redirectUrl() {
		return $this->request->redirect_url;
	}

	/**
	* Returns ip address fetched from response header.
	*
	* @access 	public
	* @return 	Double
	*/
	public function ip() {
		return $this->request->primary_ip;
	}

	/**
	* Returns the client ip address.
	*
	* @access 	public
	* @return 	Double
	*/
	public function clientIp() {
		return $this->request->local_ip;
	}

	/**
	* Returns the primary port.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function primaryPort() {
		return $this->request->primary_port;
	}


	/**
	* Returns the local port.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function localPort() {
		return $this->request->local_port;
	}	

	/**
	* Returns json string of the response body.
	*
	* @access 	public
	* @return 	Object
	*/
	public function json() {
		return json_decode($this->body());
	}

	/**
	* Returns an array of header.
	*
	* @access 	public
	* @return 	Array
	*/
	public function getAllHeaders() {
		return $this->headers;
	}

	/**
	* @access 	public
	* @return 	void
	*/
	public function send() {
		if (sizeof($this->headers) > 1) {
			return null;
		}

		foreach(array_keys($this->headers) as $key) {
			header("$key: $this->headers[$key]");
		}
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	String
	*/
	public function getHeader($key='') {
		(Boolean) $responseheader = null;
		(Array) $headers = $this->headers;
		$header = array_map([$this, 'resolveHeaderName'], $headers);

		foreach($header as $value) {
			if (isset($value[$key])) {
				(String) $responseheader = $value[$key];
			}
		}

		return $responseheader;
	}

	/**
	* Sets client response code.
	*
	* @param 	$code <Integer>
	* @access 	public
	* @return 	void
	*/
	public function setResponseCode($code=404) {
		http_response_code($code);
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	Array
	*/
	private function resolveHeaderName($string='') {
		(Array) $resolvedHeader = [];

		if (preg_match("/.*[a-zA-Z0-9]: (.*?)/", $string, $match)) {
			$key = $match[0];
			$value = str_replace($key, "", $string);
			$key = str_replace(': ', '', $key);
			$resolvedHeader[$key] = $value;
		}

		return $resolvedHeader;
	}

}