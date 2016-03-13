<?php
if(count(get_included_files()) == 1 or basename($_SERVER['SCRIPT_FILENAME']) == 'include_main_document.php'){
	include_once('404.php');
}

//Define 設定
///////////////////////////////////////
set_time_limit(120);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');

//
///////////////////////////////////////
//define('DEBUG',false);
define('ADSENSE',true);
define('DB_NAME','youtube_db.db');


if(array_key_exists('debug',$_REQUEST)){
		define('DEBUG',true);
}else{
		define('DEBUG',false);
}

?>