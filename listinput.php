<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');
include_once('../acclog.php');
//youtube playlist input
//2015/12/20
//https://developers.google.com/youtube/iframe_api_reference
?>
<!DOCTYPE html>
<html>
  <body>
	<div>信息录入界面</div>
    <div id="player"></div>
	<br>
	<div>list_id</div>
	<input id = "list_id" style="width: 435px;">
	<div>list_title</div>
	<input id = "list_title" style="width: 435px;">
	<div>eng_name</div>
	<input id = "eng_name" style="width: 435px;">
	<div>pinyin_name</div>
	<input id="pinyin_name" style="width: 435px;">
	<div>comment</div>
	<textarea id="comment" style="margin: 0px; width: 433px; height: 125px;"> </textarea>
	<div>button</div>
	<button id = "start_button">button</button>
	<br>

    <script>
		var tag = document.createElement('script');
			tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			
		var player;
		var lid_rowid;
		var list;
		var list_title;
		var eng_name;
		
		function onYouTubeIframeAPIReady() {
			player = new YT.Player('player', {
					height: '150',
					width: '200',
					videoId: 'M7lc1UVf-VE',
					playerVars:{'loop':0},
					events: {
							'onReady': onPlayerReady,
							'onStateChange': onPlayerStateChange,
							'onError':onPlayerError
						}
					}
				);
		}

		function onPlayerReady(event) {
			event.target.setVolume(1);
			event.target.setLoop({loopPlaylists:false});
		}

		
		function onPlayerStateChange(event) {
			
			if(event.target.getPlayerState()== -0){
			}
			
			if(event.target.getPlayerState() == 0){
			}
			
			if(event.target.getPlayerState() == 1){
				if(event.target.getPlaylist()){
					if(event.target.getVideoData().video_id == event.target.getPlaylist()[event.target.getPlaylist().length -1]) {
						event.target.stopVideo();
					}else{
						event.target.nextVideo();
					}
				}
			}
			
			if(event.target.getPlayerState() == 2){
			}
			
			if(event.target.getPlayerState() == 3){
				stop(event);
					console.log('------start------');
					console.log(event.target.getPlaylistIndex());
					console.log(event.target.getVideoData());
					
					var v_data = event.target.getVideoData();
						v_data.title =  encodeURIComponent(v_data.title.replace('&','%26'));
						v_data.title =  encodeURIComponent(v_data.title.replace('"','%22'));
						v_data.author = encodeURIComponent(v_data.author.replace('&','%26'));
						v_data.author = encodeURIComponent(v_data.author.replace('"','%22'));

					send(v_data,'129');
					send({'lid_rowid':lid_rowid,'video_id':v_data.video_id},'198');
					console.log('------end------');
			}
			
			if(event.target.getPlayerState() == 4){
				
			}
			
			if(event.target.getPlayerState() == 5){
				//cuePlaylist
				stop(event);
				if(event.target.getPlaylist()){
					if(event.target.getVideoData().video_id == event.target.getPlaylist()[event.target.getPlaylist().length -1]) {
						event.target.stopVideo();
					}else{
						comment = encodeURIComponent(comment.replace('"','%22'));
						comment = encodeURIComponent(comment.replace('&','%26'));
						send ({'list':list,'title':list_title,'eng_name':eng_name,'pinyin_name':pinyin_name,'comment':comment},'120');
						document.getElementById("list_id").value = "";
						document.getElementById("list_title").value = "";
						document.getElementById("eng_name").value = " ";
						document.getElementById("pinyin_name").value = " ";
						document.getElementById("comment").value = " ";
					}
				}
			}
		}

		
		function onPlayerError(event){
			if(event.target.getPlaylist()){
				if(event.target.getVideoData().video_id == event.target.getPlaylist()[event.target.getPlaylist().length -1]) {
					event.target.stopVideo();
				}else{
					event.target.nextVideo();
				}
			}
		}


		
		
		function send (json_data,rand){
			//リストの送信は120
			var ajax = new XMLHttpRequest();
			var url = '/youtube_db/add_eng.php?rand=' + rand;
				ajax.onreadystatechange = function(){
					if(ajax.readyState == 4 && ajax.status == 200){
						
						if(JSON.parse(ajax.responseText)['video_id'] && !JSON.parse(ajax.responseText)['updatecheck']){
							console.log('v_data: ' + decodeURIComponent(ajax.responseText));
						}						
						
						if(JSON.parse(ajax.responseText)['lid_rowid']){
							lid_rowid = JSON.parse(ajax.responseText)['lid_rowid'];
							console.log('lid_rowid: ' + lid_rowid);
						}
						//console.log('orij:  ' + ajax.responseText);
						//console.log('orij:  ' + decodeURIComponent(ajax.responseText));
					}
				}
				
				ajax.open('POST',url,false);
				ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				ajax.send('p=' + JSON.stringify(json_data));
		}
		
		
		function re_send(json_data,rand){
			//リストIDを入力後、すでに存在するリスト情報を呼び出す。
			//rand == 199
			var ajax = new XMLHttpRequest();
			var url = '/youtube_db/add_eng.php?rand=' + rand;
				ajax.onreadystatechange = function(){
					if(ajax.readyState == 4 && ajax.status == 200){
						var re = JSON.parse(ajax.responseText);
						if(re != ''){
							console.log(re[0]);
							document.getElementById("list_title").value = re[0]['title'];
							document.getElementById("eng_name").value = re[0]['eng_name'];
							document.getElementById("pinyin_name").value = re[0]['pinyin_name'];
							document.getElementById("comment").value = re[0]['comment'];
						}else{
							document.getElementById("list_title").value = '';
							document.getElementById("eng_name").value = '';
							document.getElementById("pinyin_name").value = '';
							document.getElementById("comment").value = '';							
						}
					}
				}
				
				ajax.open('POST',url,true);
				ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				ajax.send('p=' + json_data);
		}

		function stop(event){
			console.log('stop_' + event.target.getPlayerState());
			console.log(event);
			if(event.target.getPlaylist()){
				if(event.target.getVideoData().video_id == event.target.getPlaylist()[event.target.getPlaylist().length -1]) {
					//console.log(event.target.get)
					event.target.setLoop({loopPlaylists:false});
					event.target.stopVideo();
					alert('page_reload');
					location.reload();
				}else{
					event.target.playVideo();
				}
			}
		}


		function start(){
			if(document.getElementById("list_id").value != "" && document.getElementById("list_title").value != "" ){
				console.clear();
				list = document.getElementById("list_id").value;
				list_title = document.getElementById("list_title").value;
				eng_name = document.getElementById("eng_name").value;
				pinyin_name = document.getElementById("pinyin_name").value;
				comment = document.getElementById("comment").value;
				player.cuePlaylist({list:list});
			}
		}	
		
		function list_chk(){
			// rand == 199
			if(document.getElementById("list_id").value != ""){
					re_send(JSON.stringify({'list_id':document.getElementById("list_id").value}),'199');
			}
		}
		
		
		onload = function(){
			var tag_button = document.getElementById("start_button");
				tag_button.addEventListener('click',start,false);
				
			var tag_list = document.getElementById("list_id");
				tag_list.addEventListener('blur',list_chk,true);
		}
	  
	  
	  
    </script>
  </body>
</html>

