/*
 * Date: 2012/05/30 - 8:37 AM
 */
var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {

	if ($.bbq.getState("highlight")) {
		$("#list-highlight-btns button[data-highlight].active").removeClass("active");
		$("#list-highlight-btns button[data-highlight='" + $.bbq.getState("highlight") + "']").addClass("active");
	}
	if ($.bbq.getState("page")) {
		getPageDetails();
	}

	$(document).on("click", "#list-highlight-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		var highlight = $("#list-highlight-btns button.active").attr("data-highlight");

		$.bbq.pushState({"highlight":highlight});
		load_pages();

	});

	$(document).on("click", "#list-zoom-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		var zoom = $(this).attr("data-zoom");

		$.bbq.pushState({"zoom":zoom});
		load_pages();

	});

	$(document).on("click", "#reload-btn", function () {
		load_pages();
	});

	$(document).on("click", "#dummy-bottom .page", function () {
		var $this = $(this);
		var page = "#dummy-area .pages[data-page='" + $this.attr("data-page_nr") + "']";
		if ($("#dummy-area .pages[data-page='" + $this.attr("data-page_nr") + "']").length) {
			whole_pane.scrollToElement(page, true, true);
		}

	});
	$(document).on("click", ".pages", function () {
		var $this = $(this), ID = $this.attr("data-page");
		$.bbq.pushState({"page": ID});
		getPageDetails();
	});
	$(document).on('hide', '#page-details-modal', function () {
		$.bbq.removeState("page");
		
	});
	

	load_pages();
});
function getPageDetails(){
	var ID = $.bbq.getState("page");
	$.getData("/app/ab/data/layout/_details_page?r=" + Math.random(), {"val": ID}, function (data) {

		$("#page-details-modal").jqotesub($("#template-page-details-modal"), data).modal("show");
	});
}
function dummy_resize(settings) {
	$("#dummy-area").css("bottom", $("#dummy-bottom").outerHeight());
	if (settings && settings.maintain_position) {
		$("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptionsMP);
	} else {
		whole_pane.reinitialise();
	}

}
function load_pages(settings) {

	var highlight = $("#list-highlight-btns button.active").attr("data-highlight");
	highlight = (highlight) ? highlight : "";
	var placingID = $("#placingID").val();
	
	var zoom = $.bbq.getState("zoom");
	$.bbq.removeState("zoom");

	$("#whole-area .loadingmask").show();

	$.getData("/app/ab/data/overview/_pages", {"highlight":highlight,"zoom":zoom}, function (data) {


		var $recordsList = $("#pages-area");
		//console.log(data['zoom']);
		
		var $pl = $("#list-zoom-btns button[data-zoom='+1']");
		var $mi = $("#list-zoom-btns button[data-zoom='-1']");
		if (data.zoom.p){
			$pl.removeAttr("disabled")
		} else {
			$pl.attr("disabled","disabled")
		}
		if (data.zoom.m){
			$mi.removeAttr("disabled")
		} else {
			$mi.attr("disabled","disabled")
		}
		
		
			
			
			$recordsList.jqotesub($("#template-spreads"), data);
			$("#dummy-bottom").jqotesub($("#template-spreads-bottom"), data['spreads']);

		
		$("#whole-area .loadingmask").fadeOut(transSpeed);
		dummy_resize(settings);
	}, "data");
}
