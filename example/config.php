<?php

function __autoload($className)
{
	if(file_exists('../src/'.str_replace('_','/',$className).'.php')){
	  include_once('../src/'.str_replace('_','/',$className).'.php');
	}
}

class config
{
	const API_HOST 	  = 'https://api.douban.com/v2';
	const API_APPKEY  = '0b2bdeda43b5688921839c8ecb20399b';	
}
