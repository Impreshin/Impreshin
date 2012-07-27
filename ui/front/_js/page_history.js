/*
 * Date: 2012/07/26 - 9:48 AM
 */

var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
$('#divRss').FeedEk({
	FeedUrl    :'https://github.com/WilliamStam/Press-Apps/commits/master.atom?login=WilliamStam&token=e0619b48df9ba55f7f131a360bdf3ee3',
	MaxCount   :20,
	ShowDesc   :true,
	ShowPubDate:true,
	callBack:function(){
		whole_pane.reinitialise();
	}
});