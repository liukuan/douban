<?php
/**
 * @author liukuan <unemail@qq.com>
 */
class http_curl
{
	protected $_options = array();
	protected $_ch;
	protected $_headers = array();
	
	public function __construct()
	{
		$this->init();
		$this->setUserAgent('Mozilla/4.0');
		$this->setFollowLocation(2);
		$this->setAutoReferer(1);
	}
	
	public function init()
	{
		$this->_ch = curl_init();
		$this->setOpt(CURLOPT_HEADER, 1);
		$this->setOpt(CURLOPT_SSL_VERIFYPEER, 0);//对认证证书来源的检查
		$this->setOpt(CURLOPT_SSL_VERIFYHOST, 2);//从证书中检查SSL加密算法是否存
		$this->setAcceptEncoding('gzip');
	}
	
	/**
	 * 开启cookie功能.
	 * @param unknown_type $file
	 * @return system_http_curl
	 */
	public function enableCookie($file = null)
	{
		if($file==null) {
			$file = new file_tmp();
		}
		$this->setOpt(CURLOPT_COOKIEJAR, $file);
		$this->setopt(CURLOPT_COOKIEFILE, $file);
		return $this;
	}
	
	public function setCookie($mark,$Write=false){
		$mark = VAR_PATH.'/cache/cookie/'.$mark;
		$this->setOpt(CURLOPT_COOKIEJAR, $mark);
		$this->setopt(CURLOPT_COOKIEFILE, $mark);
		return $this;
	}
	
	/**
	 * 设置代理，传入方式用支持parse_url解析的方式.
	 * @param unknown_type $url
	 */
	public function setProxy($url)
	{
		if(is_string($url)) {
			$url = parse_url($url);
		}
		//'proxyhttp://fenli:password@192.168.0.11:8089';
		if($url['scheme'] == 'proxyhttp') {
			$host = $url['host'].':'.$url['port'];
			$this->setOpt(CURLOPT_PROXY, $host);//http 代理.
			if(isset($url['user'])) {
				$userpwd = $url['user'].':'.$url['pass'];
				$this->setOpt(CURLOPT_PROXYUSERPWD, $userpwd);//http验证用户.
			} else {
				$this->setOpt(CURLOPT_PROXYUSERPWD, null);//http验证用户.
			}
		}
	}
	
	/**
	 * 
	 * @param unknown_type $version CURLOPT_VERSION_!_0
	 * @return system_http_curl
	 */
	public function setVersion($version)
	{
		$this->setOpt(CURLOPT_HTTP_VERSION, $version);
		return $this;
	}
	
	public function getHandle()
	{
		return $this->_ch;
	}
	
	public function setHeader($k, $val)
	{
		$this->_headers[$k] = $val;
		return $this;
	}
	
	public function getHeader($k)
	{
		if(isset($this->_headers[$k])) {
			return $this->_headers;
		}
	}
	
	public function setHeaders($list)
	{
		$this->_headers = $list;
		return $this;
	}
	
	/**
	 * 是否自动处理跳转地址到最终地址.
	 * @param unknown_type $val
	 * @return system_http_curl
	 */
	public function setFollowLocation($val)
	{
		$this->setOpt(CURLOPT_FOLLOWLOCATION, intval($val));
		return $this;
	}
	
	/**
	 * 设置自动referer信息.
	 * @param unknown_type $val
	 * @return system_http_curl
	 */
	public function setAutoReferer($val)
	{
		$this->setOpt(CURLOPT_AUTOREFERER, $val);
		return $this;
	}
	
	public function setAcceptEncoding($encoding = 'gzip')
	{
		$this->setOpt(CURLOPT_ENCODING, $encoding);
		$this->setHeader('Accept-Encoding', $encoding);	
		return $this;
	}
	
	public function getEncoding()
	{
		return $this->getHeader('Accept-Encoding');
	}
	
	public function setAcceptLanguage($lang)
	{
		$this->setHeader('Accept-Language', $lang);
		return $this;
	}
	
	public function setUserAgent($val)
	{
		$this->setOpt(CURLOPT_USERAGENT, $val);
		return $this;
	}
	
	
	
	/**
	 * 设置请求超时秒数.
	 * @param unknown_type $time
	 * @return system_http_curl
	 */
	public function setTimeout($time = 30)
	{
		$this->setOpt(CURLOPT_TIMEOUT, $time);
		return $this;
	}
	
	
	
	/**
	 * 
	 * @param unknown_type $opt
	 * @param unknown_type $val
	 */
	public function setOpt($opt, $val)
	{
		curl_setopt($this->getHandle(), $opt, $val);		
		return $this;
	}
	
	/**
	 * GET 请求.
	 * @param unknown_type $url
	 * @param unknown_type $query
	 * @return Ambigous <system_http_curl_respone,...curl_response>
	 */
	public function get($url, $query = array())
	{
		if($query) {
			$url.='?'.http_build_query($query);
		}
		$this->setOpt(CURLOPT_HTTPGET, 1);
		return $this->request($url);
	}
	
	/**
	 * POST 请求.
	 * @param unknown_type $url
	 * @param unknown_type $query
	 * @return Ambigous <curl_respone, curl_response>
	 */
	public function post($url, $query = null)
	{
		if(is_array($query)) {
			$query = http_build_query($query);
		}
		$this->setOpt(CURLOPT_POSTFIELDS, $query);
		$this->setOpt(CURLOPT_POST, 1);
		//$this->setOpt(CURLOPT_REFERER,$url); //伪造来路
		return $this->request($url);
	}
	
	/**
	 * 
	 * @return curl_respone
	 */
	public function request($url)
	{
		$url = trim($url);
		if($this->_headers) {
			$this->setOpt(CURLOPT_HTTPHEADER, $this->_headers);
		}
		$this->setOpt(CURLOPT_URL, $url);
		return new http_curl_response($this);
	}
	
	/**
	 * 执行并反回执行结果.
	 * @return mixed
	 */
	public function exec()
	{
		$this->setOpt(CURLOPT_RETURNTRANSFER, 1);
		return curl_exec($this->getHandle());
	}
	
	public function __destruct()
	{
		curl_close($this->_ch);
	}
	
	static protected $_instance;
	/**
	 * 单一化实例方法.
	 * @return ...curl_curl
	 */
	static public function getInstance()
	{
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}