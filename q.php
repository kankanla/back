<?php 
// include_once('acclog.php');

error_reporting(0);
//質問詳細API
//http://chiebukuro.yahooapis.jp/Chiebukuro/V1/detailSearch
//http://developer.yahoo.co.jp/webapi/chiebukuro/chiebukuro/v1/detailsearch.html
//Twitter Gene_cc,Xshop2  リンク表示用

//error_reporting(0);
date_default_timezone_set('Asia/Tokyo');
//$_GET['q']='1445211424';		/////
//$_GET['q']='12122458572';		/////
if(empty($_GET['q']) or is_null($_GET['q'])){
	//header('Location: http://yapi.hp2.jp/q.php?q=13127326698');
	//header('Location: http://192.168.11.10/gene_cc/q.php?q=13127326698');
	header('Location: http://www.xxx.com');
	exit ('P = Empty OR P==Null');
}

// mf_log($_GET['q']);
$ads_rand = mt_rand(1,2);

$x = new qieid($_GET['q']);
$x->error();

//$x->content();
//$x->Answer();
//$x->Answer()['list']
//$x->Answer()['status']


$sps4=<<<EOD
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- 300x250, 招財進宝 09/05/24 -->
EOD;

$sps4=<<<EOD
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Gene_cc -->
EOD;


$sps5=<<<EOD
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Gene_cc2 -->
EOD;

$sps6=<<<EOD
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Gene_cc -->
EOD;

$sps7=<<<EOD
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Gene_cc3 -->
EOD;

$sps8=<<<EOD
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Gene_cc4 -->
EOD;



if ($_SERVER['REMOTE_ADDR'] == 'xxx.xxx.xxx.250' ){
	//指定IP 広告無表示
	//echo $_SERVER['REMOTE_ADDR'];
	$sps4 = '';
	$sps5 = '';
	$sps6 = '';
	$sps7 = '';
	$sps8 = '';
}

$tw=<<<EOD
<a href="https://twitter.com/share" class="twitter-share-button" data-text="おもしろ話し ＼(^o^)／わーい\n%s" data-lang="ja" data-count="none">ツイート</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
EOD;

$gp=<<<EOD
<a href="https://plus.google.com/share?url=%s" onclick="javascript:window.open(this.href,
'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><img
src="https://www.gstatic.com/images/icons/gplus-16.png" alt="Share on Google+"/></a>
EOD;

$ya=<<<EOD
<!-- Begin Yahoo! JAPAN Web Services Attribution Snippet -->
<a href="http://developer.yahoo.co.jp/about" rel="nofollow">
<img src="http://i.yimg.jp/images/yjdn/yjdn_attbtn1_125_17.gif" title="Webサービス by Yahoo! JAPAN" alt="Web Services by Yahoo! JAPAN" width="125" height="17" border="0" style="margin:15px 15px 15px 15px"></a>
<!-- End Yahoo! JAPAN Web Services Attribution Snippet -->
EOD;

$qel=<<<EOD
<a href="%s" rel="nofollow" ><img src="qe.png" alt="知恵袋"></a>
EOD;

$aml=<<<EOD
<a href="http://yapi.hp2.jp/qie.php" rel="nofollow" ><img src="am.png" alt="アマゾン"></a>
EOD;


class qieid{
	public $xmlo;			//FileOpenしたデーター
	public $content = array();		//本質問
	public $Answer = array();
	public $qieurl = array();
	Public $error;
	
	public function qieid($qid){
		//$this->qieurl='http://detail.chiebukuro.yahoo.co.jp/rd/q'.$qid;
		$this->qieurl['a']='http://yapi.hp2.jp/aq.php?q='.$qid;
		$this->qieurl['b']='http://yapi.hp2.jp/q.php?q='.$qid;
		
		$confs=0;
		//$this->qieurl=$qid;
		do{
				$requestURL = 'http://chiebukuro.yahooapis.jp/Chiebukuro/V1/detailSearch?';
				$aids = array('djXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-',
							   'djXXXXXXXXXXXXXXXXXXXXXXX-');
				$para['appid'] = $aids[array_rand($aids)];
				$para['question_id'] = $qid;
				$para['results'] = '10';
				$para['image_type'] = '1';
			
				$xmlurl = $requestURL.http_build_query($para);
				//echo $xmlurl;			/////
				//echo '<br>';			/////
				$this->xmlo = @simplexml_load_file($xmlurl,$class_name = "SimpleXMLElement",LIBXML_NOCDATA);
				$confs++;
					if ($confs == 10){
						$this->error='error';
						break;
					}
		} while ($this->xmlo == null);
		
				//echo '<pre>';						/////
				//echo print_r($this->xmlo);		/////

		if (!empty($this->xmlo->Message)){
			$this->error='error';
		}
	}
	
	
	public function error(){
		//エラーチェック
		return $this->error;
	}


	public function qieurl(){
		return  $this->qieurl;
		
	}
	

	
    public function content(){
		// 質問文
		$this->content['a']=str_replace("\n",'<br>',$this->xmlo->Result->Content);
		$this->content['b']=$this->xmlo->Result->Content;
		return $this->content;
	}

	
	
	public function Answer(){
			$this->Answer = array();
			switch($this->xmlo->Result->QuestionStatus){
				case 'open':		//受付中の問題
						//echo '<br>DEBUG open<br>';	/////
						if(urldecode($this->xmlo->Result->AnswerCount) >'0'){
							foreach($this->xmlo->Result->AnswerList->Answer as $val){
								$this->Answer['list'][count($this->Answer['list'])]=str_replace($order,'<br>',$val->Content);
							}
							unset($val);
							$this->Answer['status']='open';
							return $this->Answer;
						}else{
							$this->Answer['status']='open';
							$this->Answer['list']=array();
							return $this->Answer;
						}
						break;
				
				case 'vote':			//投票中の問題
						//echo '<br>DEBUG vote<br>';	/////
						//print_r($this->xmlo->Result->AnswerList->Answer);
						foreach ($this->xmlo->Result->AnswerList->Answer as $val){
							$this->Answer['list'][count($this->Answer['list'])] = str_replace("\n",'<br>',$val->Content);
						}
						unset ($val);
						$this->Answer['status']='vote';
						return $this->Answer;
						break;
					
				case 'solved':			//解決済みの問題
						//echo '<br>DEBUG solved';	/////
						//print_r($this->xmlo->Result->AnswerList->BestAnswer);
									
						foreach ($this->xmlo->Result->AnswerList->BestAnswer as $val){
							$this->Answer['list'][count($this->Answer['list'])] = str_replace("\n",'<br>',$val->Content);
						}
						unset($val);
						$this->Answer['status']='solved';
						return $this->Answer;
						break;
				
			}

	}
}


?>


<?php
	function mf_log($log){
		error_reporting(0);
		date_default_timezone_set('Asia/Tokyo');
		$file = basename(__FILE__);
		$log_server[$file] = date('c');
		$log_server['ip'] = $_SERVER['REMOTE_ADDR'];
		$log_server['uag'] = $_SERVER['HTTP_USER_AGENT'];
		$log_server['lg'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$log_server['url'] = $log;
		
		$log_str='<?php //';
		foreach ($log_server as $key=>$val){
			$log_str = $log_str.$key.'>'.$val.', ';
		}
		unset($val);
		unset($key);
		$log_str = $log_str.'// ?>'."\r\n";
		file_put_contents('log_gene_cc.php', $log_str, FILE_APPEND | LOCK_EX); //ログファイルを書き込み
	}
?>





<?php 
	include_once('sqlite3.php');
	$xxx = new sql3;
	$xxx -> insert ($_GET['q'],mb_substr($x->content()['b'],0,80,"utf-8"));
	$tp5 = $xxx->top5();
	$tpone = $xxx->topone();
	function mf_top5($list){
		foreach ($list as $key=>$val){
			echo '<div class="ans">';
			echo '<p class="hb">';
			echo sprintf('<a href="http://yapi.hp2.jp/q.php?q=%s" rel="nofollow">%s</a>',$key,$val);
			echo '</p>';
			echo '</div>';	//ans
		}
	}
	
		
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml">';
	echo '<head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	echo '<link rel="stylesheet" type="text/css" href="qie.css" media="screen,all" />';
	echo sprintf('<title>%s</title>',mb_substr($x->content()['b'],0,120,"utf-8"));
	//echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0" />';
	echo '<meta name="viewport" content="initial-scale=1, maximum-scale=1">';
	echo sprintf('<meta name="description" content="%s">',mb_substr($x->content()['b'],0,64,"utf-8"));
	echo '<body style="width:100%">';
	echo '<link rel="alternate" type="application/atom+xml" title="Atom" href="" />';
	echo '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="" />';
	echo '</head>';
	echo '<body>';
	
	
	
	
	
    include_once("analyticstracking.php");
	
	echo '<div id = "main">';

	echo '<div class = "tit">';
			//echo 'ご拝読いただきありがとうございます';
			echo 'その詳細は…';
	echo '</div>';	//tit
	if($ads_rand == '1'){
		echo '<div id = "sps6">'.$sps6.'</div>';
	}
	
	if ($x->error() != 'error'){
		echo '<div id="con">';
			echo '<p class="hb">';
				echo $x->content()['a'];
			echo '</p>';
		echo '</div>';	//con
		
		if (is_array($x->Answer()['list'])){
			echo '<div class="tit">';
				switch ($x->Answer()['status']){
					case 'open':
						if (count($x->Answer()['list']) > 0){
							echo '<p>皆様からのご意見はこちらです。(^‐^)</p>';
						}else{
							echo '<p>皆様からのご意見待ち</p>';
						}
						break;
					
					case 'vote':
						echo '<p>どの意見を選ぶべきでしょうか(・_・)　ん？?</p>';
						break;
					
					case 'solved':
						echo '<p>皆様のご意見・ご感想ありがとうございました。次の意見を選択いたしました（＾o＾）。</p>';
						break;					
				
				}
			echo '</div>';					//tit
			if (count($x->Answer()['list']) > 0 and mb_strlen($x->Answer()['list']['0']) > 200){
				echo '<div id="asd">';
					if (mb_strlen($x->content()) > 200 ){
							echo $sps4;															/////
						}else{
							echo '<div id = "sps5">'.$sps5.'</div>';
						}

				echo '</div>';	//asd
			}
			
			foreach ($x->Answer()['list'] as $val){
				echo '<div class="ans">';
					echo '<p class="hb">';
						echo $val;
					echo '</p>';
				echo '</div>';	//ans
			}
		}
		mf_top5($tpone);
		echo '<div id="sta">';
		echo sprintf($tw,mb_substr($x->content()['b'],0,98,"utf-8")."\n");
		echo sprintf($gp,$x->qieurl()['b']);
		echo '&nbsp;'.$aml.'&nbsp;';
		echo sprintf($qel,$x->qieurl()['a']);			
		echo '<br>';
		echo $sps4;			/////////
		echo '<br>';
		echo $ya;	
		echo '</div>';	//sta

		
	}else{
		echo '<br>';
		echo '問題がサーバーから削除された可能性があります。';
		echo '<div class="tit">';
		echo '<p>トップ問題</p>';
		echo '</div>';
		mf_top5($tp5);
		echo $sps8;
		echo '<br>';
		exit('');
		
	}
	echo '</div>';	//main

//ページの終了
	echo '</body>';
	echo '</html>';
?>