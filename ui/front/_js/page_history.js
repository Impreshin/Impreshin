/*
 * Date: 2012/07/26 - 9:48 AM
 */

$('#divRss').FeedEk({
	FeedUrl    :'https://github.com/WilliamStam/Impreshin/commits/master.atom?login=WilliamStam&token=e0619b48df9ba55f7f131a360bdf3ee3&r='+Math.random(),
	MaxCount   :20,
	ShowDesc   :true,
	ShowPubDate:true,
	callBack:function(){
		$("#left-area .loadingmask").fadeOut(transSpeed);
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
	}
});