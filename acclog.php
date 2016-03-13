<?php
//logo
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');
set_time_limit(120);
session_name('aixin');
session_start();

//利用方法
//log
////////////////////////////////////////////
// if($_SERVER['SERVER_ADDR'] === '192.168.11.10'){
	// include_once('acclog.php');
	// }else{
	// //C:/htdocs/doc/acclog.php ; サイトに利用する際に利用URL
	// include_once('C:/htdocs/doc/acclog.php');
// }

//ファイルネーム
////////////////////////////////////////////
	if($_SERVER['SERVER_ADDR'] === '192.168.11.10'){
		define('LOG_DB_NAME','acclog.db');
	}else{
		// C:/htdocs/doc/acclog.db ; サイトに利用する際に利用URL
		define('LOG_DB_NAME','C:/htdocs/doc/acclog.db');
	}

$hi = new acclog();
	if(count(get_included_files()) == 1){
		if(array_key_exists('password',$_GET)){
			//$hi->viewlog();
			$hi->viewlog2();
		}else{
			$name = basename(__FILE__);
			$name = $_SERVER['SCRIPT_NAME'];
			header("HTTP/1.0 404 Not Found");
			echo '<!DOCTYPE html>';
			echo '<html><head>';
			echo '<title>404 Not Found</title>';
			echo '</head><body>';
			echo '<h1>Not Found</h1>';
			echo "<p>The requested URL {$name} was not found on this server.</p>";
			echo '</body></html>';
			exit();
		}
	}

class acclog{
	
	public $db_name = LOG_DB_NAME;
	// select datetime(request_time,"localtime") from log;	SQLITE3 Localtime の表示方法

	public function acclog(){
		if(!file_exists($this->db_name)){
			$this->create_acclog_db();
		}
		
		if(filesize($this->db_name) > 6438912){
			$this->diet();			
		}
		
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$temp = $db->prepare("insert into log ('remote_addr','http_user_agent','request_uri','http_accept_language') values (:a,:b,:c,:d)");
		$temp->bindValue(':a',$_SERVER['REMOTE_ADDR'],SQLITE3_TEXT);
		$temp->bindValue(':b',$this->user_agent($_SERVER['HTTP_USER_AGENT']),SQLITE3_TEXT);
		$temp->bindValue(':c',$_SERVER['REQUEST_URI'],SQLITE3_TEXT);
		$temp->bindValue(':d',$_SERVER['HTTP_ACCEPT_LANGUAGE'],SQLITE3_TEXT);
		$temp->execute();
		$db->close();
		$this->page_count($_SERVER['SCRIPT_FILENAME']);
	}
	
	private function user_agent($agent){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$sql_select = sprintf("select rowid from user_agent where http_user_agent = \"%s\"",$agent);
		$sql_insert = sprintf("insert into user_agent (http_user_agent) values (\"%s\")",$agent);
		$temp = $db->querySingle($sql_select);
			if(is_null($temp)){
				$db->exec($sql_insert);
				$temp = $db->querySingle($sql_select);
				$db->close();
				return $temp;
			}else{
				$db->close();
				return $temp;
			}
		
		$db->close();
	}
	
	public function viewlog2(){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(1000);
		$temp1 = $db->querySingle("select count(*) from log");
		$temp2 = $db->querySingle("select count(*) from user_agent");
		$temp3 = $db->querySingle("select count(*) from page_count");
		$file_size = round((filesize($this->db_name)/1024/1024),2);
		$file_path = realpath($this->db_name);
		echo "<div>log_count:: {$temp1}</div>";
		echo "<div>user_agent_count:: {$temp2}</div>";
		echo "<div>page_count:: {$temp3}</div>";
		echo "<div>file_size:: {$file_size} MB</div>";
		echo "<div>file_path:: {$file_path}</div>";
		echo '<div>__FEIL__::'.__FILE__.'<div>';
		echo '<br>';
		
		$vt = $db->prepare("select * from page_count");
		$vx = $vt->execute();
		
		echo '<table border=1>';
			while($vxx = $vx->fetchArray(SQLITE3_ASSOC)){
				echo "<tr><td>{$vxx['script_filename']}</td><td>{$vxx['count']}</td></tr>";
			}
		echo '</table>';
		$db->close();		
	}
	
	
	public function viewlog(){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$temp = $db->prepare("select rowid,http_user_agent from user_agent order by http_user_agent asc");
		$temp2 = $temp->execute();
		while($temp3 = $temp2->fetchArray(SQLITE3_ASSOC)){
			$temp4 = $this->user_agent_count($temp3['rowid']);
			if($temp4 != 0){
				echo "<div>{$temp3['rowid']}::{$temp3['http_user_agent']}</div>";
				echo "<div>{$temp4}</div>";
				echo "<div><br></div>";
			}
		}
		
		$vt = $db->prepare("select * from page_count");
		$vx = $vt->execute();
		while($vxx = $vx->fetchArray(SQLITE3_ASSOC)){
			echo "<div>[script_filename] => {$vxx['script_filename']}</div>";
			echo "<div>[count] => {$vxx['count']}</div>";
		}
		
		$db->close();
	}
	
	private function user_agent_count($rowid){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);		
		$temp = $db->prepare("select count(*) as count from log where http_user_agent = :a");
		$temp->bindValue(':a',$rowid);
		$temp2 = $temp->execute();
			
		while($temp3 = $temp2->fetchArray(SQLITE3_ASSOC)){
			$db->close();
			return $temp3['count'];
		}
		$db->close();
	}
	
	private function page_count($script_filename){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		//script_filename
		$sql_1 = sprintf("select script_filename from page_count where script_filename = \"%s\"",$script_filename);
		$sql_2 = sprintf("insert into page_count (script_filename,count) values (\"%s\",0)",$script_filename);
		$sql_3 = sprintf ("update page_count set count = count + 1 where script_filename = \"%s\"",$script_filename);
		$temp = $db->querySingle($sql_1);
		
		if($temp == null){
			$db->exec($sql_2);
			$db->exec($sql_3);
		}else{
			$db->exec($sql_3);
		}
		$db->close();
	}
	
	private function diet(){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		//$temp = $db->prepare("delete from log where request_time < datetime('now','-2 days')");	
		//$temp->execute();
		$sql = sprintf("delete from log where request_time < datetime('now','-1 days')");
		$db->exec($sql);
		$db->exec('VACUUM');
		$db->close();
	}
	
	public function create_acclog_db(){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$create_db = 
			"create table log(
				rowid INTEGER PRIMARY KEY AUTOINCREMENT,
				request_time text default current_timestamp,
				remote_addr text,
				http_user_agent text,
				request_uri text,
				http_accept_language text		
			)";
		
		$db->exec($create_db);
		
		$create_db =
			"create table user_agent(
			    rowid INTEGER PRIMARY KEY AUTOINCREMENT,
				http_user_agent text
			)";
			
		$db->exec($create_db);
		
		$create_db = 
			"create table page_count(
				rowid INTEGER PRIMARY KEY AUTOINCREMENT,
				script_filename text unique,
				count integer
			)";

		$db->exec($create_db);
		$db->close();
	}
}

//old
function old_mf_log($log=null){
	error_reporting(0);
	date_default_timezone_set('Asia/Tokyo');
	$file=basename(__FILE__);
	$log_server[$file]=date('c');
	$log_server['ip']=$_SERVER['REMOTE_ADDR'];
	$log_server['uag']=$_SERVER['HTTP_USER_AGENT'];
	$log_server['lg']=$_SERVER['HTTP_ACCEPT_LANGUAGE'];
	$log_server['url']=$log;
	
	$log_str='<?php //';
	foreach ($log_server as $key=>$val){
		$log_str=$log_str.$key.'>'.$val.', ';
		unset($val);
		unset($key);
	}
	$log_str=$log_str.'// ?>'."\r\n";
	file_put_contents('log_itunes.php', $log_str, FILE_APPEND | LOCK_EX);
}

?>