<?php

	include_once 'config.php';
	
	$movie = new douban_movie();
	
	//搜索电影条目
	$data = $movie->searchMovieItems('肖申克' /*关键词*/, 1 /*页码*/, 20 /*limit*/);
	var_dump($data);
	
	//搜索电影条目
	$data = $movie->getMovieItem('1292052' /*豆瓣电影id*/);
	var_dump($data);
	
	//上映电影
	$data = $movie->inTheaters('上海' /*地区*/);
	var_dump($data);