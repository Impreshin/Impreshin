/*
 * Date: 2012/05/30 - 8:37 AM
 */
var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {

	if ($.bbq.getState("highlight")) {
		$("#list-highlight-btns button[data-highlight].active").removeClass("active");
		$("#list-highlight-btns button[data-highlight='" + $.bbq.getState("highlight") + "']").addClass("active");
	}

	$(document).on("click", "#list-highlight-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		var highlight = $("#list-highlight-btns button.active").attr("data-highlight");


		$.bbq.pushState({"highlight":highlight});
		load_pages();

	});


	$(document).on("scroll", "#whole-area .scroll-pane",function(){
		visible_pages();
	});


	$(document).on("click","#reload-btn",function(){
		load_pages();
	});









	$(document).on("click","#dummy-bottom .page",function(){
		var $this=$(this);
		var page = "#dummy-area .pages[data-page='" + $this.attr("data-page_nr") + "']";
		if ($("#dummy-area .pages[data-page='" + $this.attr("data-page_nr") + "']").length){
			whole_pane.scrollToElement(page, true, true);
		}


	});




	load_pages();
});
function dummy_resize(settings){
	$("#dummy-area").css("bottom", $("#dummy-bottom").outerHeight() + 42);
	if (settings && settings.maintain_position) {
		$("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptionsMP);
	} else {
		whole_pane.reinitialise();
	}

	//visible_pages();

}
function load_pages(settings){

	var highlight = $("#list-highlight-btns button.active").attr("data-highlight");
	highlight = (highlight) ? highlight : "";
	var placingID = $("#placingID").val();

	$("#whole-area .loadingmask").show();
	listRequest.push($.getJSON("/ab/data/overview/_pages/", {"highlight":highlight}, function (data) {
		data = data['data'];

		var $recordsList = $("#pages-area");
		if (data['spreads'][0]) {
			//console.log(data['spreads']);
			$recordsList.jqotesub($("#template-spreads"), data['spreads']);
			$("#dummy-bottom").jqotesub($("#template-spreads-bottom"), data['spreads']);

		} else {

		}




		$("#whole-area .loadingmask").fadeOut(transSpeed);
		dummy_resize(settings);
	}));
}

function visible_pages(){
	var t = new Array();
	$("#dummy-bottom .page.visible").removeClass("visible");
	$("#dummy-area .pages:visible").each(function(){
		var $this = $(this);
		if (isScrolledIntoView($this)) {
			$("#dummy-bottom .page[data-page_nr='" + $this.attr("data-page")+ "']").addClass("visible");
			t.push($this.attr("data-page"));
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