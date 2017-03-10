<?php
/**
 * @豆瓣电影api
 * @author liukuan <unemail@qq.com>
 */
class douban_movie extends http_curl
{

	/**
	 * #根据豆瓣id获得电影数据
	 * @param int $id
	 * @return array
	 */
	public function getMovieItem($id)
	{
		return $this->getQuery("/movie/subject/$id");
	}
	
	/**
	 * @搜索电影
	 * @param str $key
	 * @param number $page   页码
	 * @param number $count	 limit
	 * @return array
	 */
	public function searchMovieItems($key,$page = 1,$count = 20)
	{
		$key   = trim(urlencode($key));
		$start = ($page <= 1) ? 0 : ($page-1) * $count;
		return $this->getQuery("/movie/search?q=$key&start=$start&count=$count");
	}
	
	/**
	 * #根据豆瓣id获得影人数据
	 * @param int $id
	 * @return array
	 */
	public function getCelebrityItem($id)
	{
		return $this->getQuery("/movie/celebrity/$id");
	}
	
	/**
	 * @正在上映
	 * @param str $city
	 * @return array
	 */
	public function inTheaters($city)
	{
		$city  = trim(urlencode($city));
		return $this->getQuery("/movie/in_theaters?city=$city");
	}
	
	/**
	 * @即将上映
	 * @param number $page   页码
	 * @param number $count	 limit
	 * @return array
	 */
	public function comingSoon($page = 1,$count = 20)
	{
		$start = ($page <= 1) ? 0 : ($page-1) * $count;
		return $this->getQuery("/movie/coming_soon?start=$start&count=$count");
	}
	
	/**
	 * @热榜250
	 * @param number $page   页码
	 * @param number $count	 limit
	 * @return array
	 */
	public function top250($page = 1,$count = 20)
	{
		$start = ($page <= 1) ? 0 : ($page-1) * $count;
		return $this->getQuery("/movie/top250?start=$start&count=$count");
	}
	
	/**
	 * @新片榜
	 * @return array
	 */
	public function newMovies()
	{
		return $this->getQuery("/movie/new_movies");
	}
	
	/**
	 * @口碑榜
	 * @return array
	 */
	public function weekly()
	{
		return $this->getQuery("/movie/weekly");
	}
	
	/**
	 * #获得api数据
	 * @param unknown $key
	 * @return Ambigous <Ambigous, curl_respone, system_http_curl_response>
	 */
	public function getQuery($query)
	{
		$query .= strstr($query,'?') ? '&' : '?';
		$json  = $this->get(config::API_HOST . $query . 'appkey=' . config::API_APPKEY);
		return json_decode($json,true);
	}
	
	
}