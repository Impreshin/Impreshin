git_path = git_path.replace(/.git/,'');
// https://github.com/Impreshin/Impreshin/commits/WIP.atom
$('#divRss').FeedEk({
	FeedUrl    :'https://'+git_path+'/commits/'+git_branch+'.atom?r='+Math.random(),
	MaxCount   :20,
	ShowDesc   :true,
	ShowPubDate:true,
	callBack:function(){
		$("#left-area .loadingmask").fadeOut(transSpeed);
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
	}
});