/*
 * Date: 2012/12/18 - 1:20 PM
 */
$(document).ready(function(){
	$.ajax({
		url     :path + '?callback=fn&return=3&type=ticker',
		dataType:'jsonp',
		//data    :$('form#searchlocfrm').serialize(),
		success :function (data) {
			//console.log(data);
			if (data.length) {
				var news = $.map(data,function (v) {
					var ret = "";
					var txt = '<span class="g s">' + v.datein_d + '</span> | ' + v.news;
					if (v.link) {
						ret = '<li class="news-item"><a href="' + v.link + '">' + txt + '</a></li>';
					} else {
						ret = '<li class="news-item">' + txt + '</li>';
					}
					return ret;
				}).join("");

				$('#js-news').html(news).ticker({
					speed      :0.5,
					controls   :true,
					titleText  :'News',
					displayType:'fade'
				});
			}
		}
	});
});


