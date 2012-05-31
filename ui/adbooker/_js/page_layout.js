/*
 * Date: 2012/05/30 - 8:37 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#right-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {

	scrolling(left_pane);

	$(document).on("scroll", "#left-area .scroll-pane",function(){
		visible_pages();
	});

	$(document).on("change","#placingID",function(){
		load_list();

	});

	$(document).on("click","#dummy-bottom .page",function(){
		var $this=$(this);
		var page = "#dummy-area article.pages[rel='" + $this.attr("data-page_nr") + "']";
		if ($("#dummy-area article.pages[rel='" + $this.attr("data-page_nr") + "']").length){
			left_pane.scrollToElement(page, true, true);
		}


	});

	load_list();
	dummy_resize();
});
function dummy_resize(){
	$("#dummy-area").css("bottom", $("#dummy-bottom").height() + 47);
	left_pane.reinitialise();
	//visible_pages();

}
function records_list_resize(){
	$("#record-list-middle").css("bottom", $("#record-details-bottom").height() + 42);
	right_pane.reinitialise();
}
function load_list(){
	var placingID = $("#placingID").val();

	$("#right-area .loadingmask").show();
	listRequest.push($.getJSON("/ab/data/layout_list/", {"placingID":placingID}, function (data) {
		data = data['data'];


		var $recordsList = $("#record-list tbody");
		if (data[0]){
			data = data[0]['records'];
			$recordsList.jqotesub($("#template-records-list"), data);
		} else {
			$recordsList.html('<tr><td class="c no-records">No Records Found</td></tr>')
		}




		$("#right-area .loadingmask").fadeOut(transSpeed);
		records_list_resize();
	}));
}
function visible_pages(){
	var t = new Array();
	$("#dummy-bottom .page.visible").removeClass("visible");
	$("#dummy-area article.pages:visible").each(function(){
		var $this = $(this);
		if (isScrolledIntoView($this)) {
			$("#dummy-bottom .page[data-page_nr='" + $this.attr("rel")+ "']").addClass("visible");
			t.push($this.attr("rel"));
		}
	});
//	console.log(t);

}
function isScrolledIntoView(elem) {
	var docViewTop = $(window).scrollTop();
	var docViewBottom = docViewTop + $(window).height();

	var elemTop = $(elem).offset().top;
	var elemBottom = elemTop + $(elem).height();

	return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom) && (elemBottom <= docViewBottom) && (elemTop >= docViewTop) );
}