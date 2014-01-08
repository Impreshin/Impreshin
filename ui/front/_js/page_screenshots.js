/*
 * Date: 2012/05/30 - 8:37 AM
 */
var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {



	$("a.screenshot").attr('rel', 'gallery').fancybox({
			padding:0,
		type:'image',
		openEffect: 'fade',
			beforeLoad:function () {
				this.title = $(this.element).find("div.hidden").html();


			},
		helpers:{
			overlay:{
				speedIn   :500,
				speedOut  :500,
				opacity   :0.4

			},
			title  :{
				type:'over'
			}

		}
		});

});