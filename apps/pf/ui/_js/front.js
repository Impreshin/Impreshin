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
$(document).on("change", "#dID", function () {
	$.bbq.pushState({"dID":$(this).val()})
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
	var dID = $("#dID").val();
	$.getData("/app/pf/data/front/_details?r=" + Math.random(), {"page": ID,"dID":dID}, function (data) {

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

	var dID = $.bbq.getState("dID");
	highlight = (highlight) ? highlight : "";
	//$.bbq.removeState("dID");

	$("#whole-area .loadingmask").show();
	$("#page-heading-area").html("loading...");
	
	

	$.getData("/app/pf/data/front/_pages", {"highlight":highlight,"zoom":zoom,"dID":dID}, function (data) {


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



		$("#page-heading-area").html(data.date);

		$recordsList.jqotesub($("#template-spreads"), data);
		$("#dummy-bottom").jqotesub($("#template-spreads-bottom"), data['spreads']);

		$('#dID').html("").append( $.map(data.datelist, function(v, i){ return $('<option>', { val: v.ID, text: v.publish_date_display }).attr("data-has",v.has); })).val(data.dID).select2({
			formatResult: dID_options,
			formatSelection: dID_options
		});

		$("#whole-area .loadingmask").fadeOut(transSpeed);
		dummy_resize(settings);
		if ($.bbq.getState("page")) {
			getPageDetails();
		}

	}, "data");
}
function dID_options(data) {
	if (!data.id) return data.text; // optgroup
	if ($(data.element).attr("data-has")=='1'){
		return "* "+data.text;
	} else {
		return "<span class='g'>" + data.text+"</span>";
	}
	
}