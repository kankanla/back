x = function (){
	// 検索結果のページにあるタイトルを集取します。
	console.clear();
	var x = document.getElementsByTagName('h2');
	for(var i = 0; i < x.length; i++){
		var text;
		var ama_url ='http://www.amazon.co.jp/s/?_encoding=UTF8&camp=1207&creative=8415&linkCode=shr&tag=ak8-22&_mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&url=search-alias%3Daps&field-keywords=';

		if(x[i].getAttribute('data-attribute')){
			ama_url = ama_url + x[i].getAttribute('data-attribute');
			text = text +'<a class="xxx" href="' + ama_url + '" thile="' + x[i].getAttribute('data-attribute') + '" target="_blank" rel="follow">' + x[i].getAttribute('data-attribute') + '</a><br>';
		}
	}
	return text;
}();