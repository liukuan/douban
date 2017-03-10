<?php
class http_curl_response
{
	protected $_adapter;
	protected $_body;
	protected $_headers = array();
	protected $_code = 0;
	protected $_followLocation = array();
	protected $_version;
	public function __construct(http_curl $curl)
	{
		$this->_adapter = $curl;
		$this->load($curl->exec());
	}
	
	protected function load($re)
	{
		$lines = explode("\n", $re);
		if(preg_match("/HTTP\/(\d+\.\d+) (\d+)/is", $lines[0], $match)) {
			$this->_version = $match[1];
			$this->_code = $match[2];
			unset($lines[0]);
			$this->_followLocation[] = $this->_headers;
			$this->_headers = array();
		} else {
			return ;
		}
		
		foreach($lines AS $k=>$v) {
			unset($lines[$k]);
			$val = explode(':', $v);
			if(count($val) < 2) {
				break;
			}
			$key = $val[0];unset($val[0]);
			$this->_headers[$key] = implode(':', $val);
		}
		$re = implode("\n" , $lines);		
		
		if($this->getStatus() >= 300 && $this->getStatus() < 303) {
			return $this->load($re);
		}

        if($this->getStatus() == 100) {
            return $this->load($re);
        }
		
		$this->_body = $re;
	}
	
	public function getBody()
	{
		return new http_body( $this->_body );
	}

	public function setBody($body = null)
	{
		if ($body && is_string($body)) {
			$this->_body = $body;
		}
	}
	
	/**
	 * 服务器响应头部信息.
	 * @return multitype:
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}
	
	/**
	 * 反回Location 过程headers.
	 * @return multitype:
	 */
	public function getFollowLocation()
	{
		return $this->_followLocation;
	}
	
	/**
	 * 得到头部信息值 .
	 * @param unknown_type $k
	 * @return multitype:
	 */
	public function getHeader($k)
	{
		if(isset($this->_headers)) {
			return $this->_headers[$k];
		}
	}
	
	public function getEncoding()
	{
		return $this->getHeader('Content-Encoding');
 	}
	
	public function getCharset()
	{
		if($h = $this->getHeader('Content-Type')) {
			if(preg_match('/charset=([\w\-\d]+)/i', $h, $match)) {
				return $match[1];
			} elseif(preg_match("/charset=([\w\-\d]+)/Usi", $this->getBody(), $match)) {
                return $match[1];
            }
		}
	}
	
	public function getStatus()
	{
		return $this->_code;
	}
	
	public function refresh()
	{
		$this->load($this->_adapter->exec());
		return $this;
	}
	
	public function getVersion()
	{
		return $this->_version;
	}
	public function __toString()
	{
		return strval($this->getBody());
	}
	
	public function __destruct()
	{
		//echo 'response finsh..';
	}
	
}