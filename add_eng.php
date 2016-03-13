<?php
// Youtube_db server
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');

//log
////////////////////////////////////////////
if($_SERVER['SERVER_ADDR'] === '192.168.11.10'){
	include_once('acclog.php');
}else{
	//C:/htdocs/doc/acclog.php ; サイトに利用する際に利用URL
	include_once('C:/htdocs/doc/acclog.php');
}


//
////////////////////////////////////////////
define('YOU_DB_NAME','youtube_db.db');


$hi = new youtube_db_eng();

if(array_key_exists('p',$_REQUEST) and array_key_exists('rand',$_REQUEST)){
	//
	if($_GET['rand'] == "120"){
		$hi->lid($_POST['p']);
	}

	if($_GET['rand'] == "129"){
		$hi->vid($_POST['p']);
	}

	if($_GET['rand'] == "198"){
		$hi->lvid($_POST['p']);
	}
	
	if($_GET['rand'] == "199"){
		$hi->chk_list($_POST['p']);
	}

}else{
	$hi->set_myitem();
	$hi->count_table('lid');
	$hi->last_insert('lid');
	$hi->count_table('vid');
	$hi->last_insert('vid');
	$hi->count_table('vid_lid');
	$hi->last_insert('vid_lid');
}

//
///////////////////////////////////////////////////
class youtube_db_eng{
	
	public $db_name = YOU_DB_NAME;

	//rand = "120"
	//動画リスト追加/更新
	function lid($p){
		$temp = json_decode($p);
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$title = urldecode($temp->title);
		$comment = urldecode(htmlentities($temp->comment));
		$list = urldecode($temp->list);
		
		$sql_select = "select rowid from LID where list = '{$list}'";
		$sql_insert = "insert into LID (list,title,eng_name,pinyin_name,comment)values('{$list}','{$title}','{$temp->eng_name}','{$temp->pinyin_name}','{$comment}')";
		$sql_update = "update LID set title = '{$title}', eng_name = '{$temp->eng_name}', pinyin_name = '{$temp->pinyin_name}', comment = '{$comment}', Updatedate = date('now') where list = '{$list}'";
		
							   
			if($db->querySingle($sql_select) == null){
				$db->exec($sql_insert);
				$temp2['lid_rowid'] = $db->querySingle($sql_select);
				$temp2['zFunction'] = 'function lid($p)';
				echo json_encode($temp2);
			}else{
				$db->exec($sql_update);
				$temp2['lid_rowid'] = $db->querySingle($sql_select);
				$temp2['zFunction'] = 'function lid($p)';
				echo json_encode($temp2);
				$sql_update2 = "update VID_LID set active = '3' where LID_rowid = '{$temp2['lid_rowid']}' AND active != '2'";
				$db->exec($sql_update2);
			}

		$db->close();
	}

/* 	function OLD_lid($p){
		$temp = json_decode($p);
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		
		$sql_select = sprintf("select rowid from LID where list = \"%s\"",trim($temp->list));
		
		$sql_insert = sprintf("insert into LID (list,title,eng_name,pinyin_name,comment)values(\"%s\",\"%s\",\"%s\",\"%s\",\"%s\")",
		trim($temp->list),urldecode($temp->title),trim($temp->eng_name),trim($temp->pinyin_name),htmlspecialchars(urldecode($temp->comment)));
		
		//$sql_update = sprintf("update LID set title = \"%s\", eng_name = \"%s\", pinyin_name = \"%s\", Updatedate = date('now') where list = \"%s\"",
		//					   urldecode($temp->title),trim($temp->eng_name),trim($temp->pinyin_name),trim($temp->list));
		
		$sql_update = sprintf("update LID set title = \"%s\", eng_name = \"%s\", pinyin_name = \"%s\", comment = \"%s\", Updatedate = date('now') where list = \"%s\"",
		urldecode($temp->title),trim($temp->eng_name),trim($temp->pinyin_name),htmlspecialchars(urldecode($temp->comment)),trim($temp->list));
		
		
		if($db->querySingle($sql_select) == null){
			$db->exec($sql_insert);
			$temp2['lid_rowid'] = $db->querySingle($sql_select);
			echo json_encode($temp2);
			}else{
			$db->exec($sql_update);
			$temp2['lid_rowid'] = $db->querySingle($sql_select);
			echo json_encode($temp2);
			$sql_update2 = sprintf("update VID_LID set active = \"3\" where LID_rowid = \"%s\" AND active != \"2\"",
			$temp2['lid_rowid']);
			$db->exec($sql_update2);
		}
		
		$db->close();
	} */
	
	
	//rand = "129"
	//Video情報を追加/更新
	function vid ($p){
		$temp = json_decode($p);
		$a = $temp->video_id;
		$b = urldecode($temp->author);
		$c = urldecode($temp->title);
		
 		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$sql_select = "select video_id from VID where video_id = '{$a}'";
		$sql_insert = "insert into vid (video_id,author,title) values ('{$a}','{$b}','{$c}')";
		$sql_update = "update vid set title = '{$c}', author = '{$b}' where video_id = '{$a}'";
								
 			if($db->querySingle($sql_select) == null){
				$db->exec($sql_insert);
				$j['video_id'] = $db->querySingle($sql_select);
				$j['title'] = $temp->title;
				$j['json_last_error'] = json_last_error();
				$j['SQLite3::lastErrorMsg'] = $db->lastErrorMsg();
				echo json_encode($j);
			}else{
				$db->exec($sql_update);
				$j['video_id'] = $db->querySingle($sql_select);
				$j['title'] = $temp->title;
				$j['json_last_error'] = json_last_error();
				$j['SQLite3::lastErrorMsg'] = $db->lastErrorMsg();
				$j['zFunction'] = 'function vid ($p)';
				echo json_encode($j);
			}
			
			$db->close();
	}

	//OLD
	/* function OLD_vid ($p){
		$temp = json_decode($p);
 		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$sql_select = sprintf("select video_id from VID where video_id = \"%s\"",$temp->video_id);
		$sql_insert = sprintf("insert into vid (video_id,author,title) values (\"%s\",\"%s\",\"%s\")",
		$temp->video_id,(urldecode($temp->author)),htmlspecialchars(urldecode($temp->title)));
		$sql_update = sprintf("update vid set title = \"%s\", author = \"%s\" where video_id = \"%s\"",
		htmlspecialchars(urldecode($temp->title)),htmlspecialchars(urldecode($temp->author)),$temp->video_id);
		
		if($db->querySingle($sql_select) == null){
			$db->exec($sql_insert);
			$j['video_id'] = $db->querySingle($sql_select);
			$j['title'] = $temp->title;
			$j['json_last_error'] = json_last_error();
			$j['SQLite3::lastErrorMsg'] = $db->lastErrorMsg();
			echo json_encode($j);
			}else{
			$db->exec($sql_update);
			$j['video_id'] = $db->querySingle($sql_select);
			$j['title'] = $temp->title;
			$j['json_last_error'] = json_last_error();
			$j['SQLite3::lastErrorMsg'] = $db->lastErrorMsg();
			echo json_encode($j);
		}
		
		$db->close();
	} */
	
	
	//rand = "198"
	//VID_LID関連付け
	function lvid($p){
		$temp = json_decode($p);
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		
		$sql_select = sprintf("select video_id from VID_LID where LID_rowid = \"%s\" and video_id = \"%s\"",
								$temp->lid_rowid,$temp->video_id);
		$sql_select2 = sprintf("select video_id from vid where video_id = \"%s\"",$temp->video_id);
		$sql_insert = sprintf("insert into VID_LID (LID_rowid,video_id) values(\"%s\",\"%s\")",$temp->lid_rowid,$temp->video_id);
		$sql_update = sprintf("update VID_LID set active = \"0\" where LID_rowid = \"%s\" and video_id = \"%s\"",
								$temp->lid_rowid,$temp->video_id);

		
		if($temp->lid_rowid != "" && $temp->video_id != "" ){
			if($db->querySingle($sql_select) == null){
				if($db->querySingle($sql_select2)){
					$db->exec($sql_insert);
					$j['updatecheck'] = 'succeed';
				}else{
					$j['updatecheck'] = 'false';
				}
			}else{
				if($db->querySingle($sql_select2)){
					$db->exec($sql_update);
					$j['updatecheck'] = 'succeed';
				}else{
					$j['updatecheck'] = 'false';
				}
			}
			
			$j['LID_rowid'] = $temp->lid_rowid;
			$j['video_id'] = $temp->video_id;
			$j['json_last_error'] = json_last_error();
			$j['SQLite3::lastErrorMsg'] = $db->lastErrorMsg();
			$j['zFunction'] = 'function lvid($p)';
			echo json_encode($j);
			
			$db->close();
		}		
	}
	
	//rand == 199
	//LID 情報をリクエスト
	function chk_list($p){
		$qtemp = urldecode(json_decode($p)->list_id);
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$temp = $db->prepare('select * from lid where list = :a');
		$temp->bindValue(':a',$qtemp,SQLITE3_TEXT);
		$temp2 = $temp->execute();
		$temp3 = array();
		while($val = $temp2->fetchArray(SQLITE3_ASSOC)){
			$con = count($temp3);
				$temp3[$con] = $val;
				$temp3[$con]['zFunction'] = 'function chk_list($p)';
		}
		echo json_encode($temp3);
		$db->close();
	}
	
	
	function set_myitem(){
		//VID_LID内部、動画をactive = 2 設定
		//update vid_lid set active ="2" where video_id in (select video_id from vid where author = "AW Music Mix");
		
		$items = array('Xiao Bai','AW Music Mix','KANKAN');
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$temp = $db->prepare('update vid_lid set active ="2" where video_id in (select video_id from vid where author = :a)');
		
		foreach($items as $val){
			$temp->bindParam(':a',$val,SQLITE3_TEXT);
			$temp->execute();
			echo '<br>';
			echo $db->lastErrorMsg();
		}
	}
	
	
	
	function count_table($table_name){
		//テーブルのカラムの合計数量
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$temp = $db->prepare("select count(*) from $table_name");
		$ro = $temp->execute();
		echo '<pre>';
		print_r($ro->fetchArray(SQLITE3_ASSOC));
	}

	//最後に追加した内容 3 Limit;
	function last_insert($table_name){
		$db = new SQLite3($this->db_name);
		$db->busyTimeout(10000);
		$temp = $db->prepare("select rowid,* from $table_name ORDER by rowid desc limit 3");
		$row = $temp->execute();
		echo '<pre>';
		while ( $ttemp = $row->fetchArray(SQLITE3_ASSOC)){
			print_r($ttemp);
		}
	}
}
?>