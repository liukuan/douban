<?php
class http_body
{
	protected $_body;
	public function __construct($body)
	{
		$this->_body = $body;
	}
	
	
	public function __toString()
	{
		return strval($this->_body);
	}
	
	public function query($query)
	{
		$dom = new http_query($this->_body);
		return $dom->query($query);
	}
}