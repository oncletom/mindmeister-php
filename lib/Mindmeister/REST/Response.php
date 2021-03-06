<?php
/**
 * REST response from a request
 * 
 * @todo	Implements ArrayAccess
 * @author oncletom
 */
class Mindmeister_REST_Response
{
	/**
	 * @var Mindmeister_REST_Request
	 */
	private $_request;
	
	/**
	 * @var SimpleXMLElement
	 */
	private $_response;

	/**
	 * @var String
	 */
	private $_raw_response;
	
	/**
	 * @var String
	 */
	private $_status;

	/**
	 * Builds a new response
	 * 
	 * @param Mindmeister_REST_Request $request
	 * @param String $raw_response
	 */
	public function __construct(Mindmeister_REST_Request $request, $raw_response)
	{
		$this->_request = $request;
		$this->_raw_response = $raw_response;
		
		$this->dispatch();
	}
	
	/**
	 * Dispatches the response where it belongs
	 */
	private function dispatch()
	{
		$this->_response = new SimpleXMLElement($this->_raw_response);
		$attributes = $this->_response->attributes();

		$this->_status = $attributes->stat;
	}
	
	/**
	 * Returns the response values, if any
	 * 
	 * @return Array
	 */
	public function getContent()
	{
		if ($this->isFailure())
		{
			return array();
		}

		if (isset($this->_response->auth))
		{
			return $this->_response->auth->children();
		}
		else
		{
			return $this->_response->children();
		}
	}
	
	/**
	 * Returns the raw response of the request
	 * 
	 * @return String
	 */
	public function getRawResponse()
	{
		return $this->_raw_response;
	}
	
	/**
	 * Returns the content value for a given key
	 * 
	 * @param String $key
	 * @return SimpleXMLElement|null
	 */
	public function getValue($key)
	{
		$content = $this->getContent();

		return isset($content->$key) ? $content->$key : null;
	}
	
	/**
	 * Returns true if the request was a failure
	 * 
	 * @return Boolean
	 */
	public function isFailure()
	{
		return $this->_status === 'fail';
	}
	
	/**
	 * Returns true if the request was a success
	 * 
	 * @return Boolean
	 */
	public function isSuccess()
	{
		return $this->_status === 'ok';
	}
	
	/**
	 * Returns a raw response of the request
	 * 
	 * @see getRawResponse()
	 * @return String
	 */
	public function __toString()
	{
		return $this->getRawResponse();
	}
	
	/**
	 * Destructs the object
	 */
	public function __destruct()
	{
		unset($this->_request);
	}
}