/*
 * Date: 2012/05/30 - 8:37 AM
 */
var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");
$(document).ready(function () {

	scrolling(api);

	$(document).on("click", ".record", function (e) {
		e.preventDefault();
		var $this = $(this);
		var id = $this.attr("data-id");
		$.bbq.pushState({"ID":id});
		getTarget();
	});

	$(document).on("click", ".pagination a", function (e) {
		e.preventDefault();
		var $this = $(this).parent();
		$.bbq.pushState({"page":$this.attr("data-page")});
		getData();
	});

	$(document).on("click", ".order-btn", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-list .order-btn").removeClass("asc desc");
		//$this.addClass("active");
		$.bbq.pushState({"order":$this.attr("data-col")});
		getData();
		$.bbq.removeState("order");
	});
	$(document).on("click", "tr.figure-month-details.record", function () {
		var $this = $(this);
		var ID = $this.attr("data-id");
		if ($this.hasClass("active")) {
			$("tr.figure-month-details.record.active").removeClass("active");
			$.bbq.removeState("dID");
		} else {
			$("tr.figure-month-details.record.active").removeClass("active");
			$this.addClass("active");
			$.bbq.pushState({"dID":ID});
		}
		getData();
	});

	$("#pub-select input:checkbox").change(function () {
		var str = $("#pub-select input:checkbox:checked").map(function () {
			return $(this).attr("data-pub");
		});
		str = $.makeArray(str);
		if (str.length > 1) {
			str = str.length + " Publications"
		} else {
			str = str[0];
		}
		$("#pub-select-label").html(str);

	});
	$(document).on("click", "#year-select button", function () {
		getData();
	});

	$(document).on("change", ".trigger_getdata input:checkbox, select.trigger_getdata", function () {
		getData();
	});
	$(document).on("click", "#combine-btn", function () {
		getData();
	});
	$(document).on("click", ".report-bottom-tabs button.back", function () {
		$.bbq.removeState("dID");
	});
	$(document).on("click", ".report-bottom-tabs button", function () {
		getData();
	});
	getData();

	$("select#selectID").select2().on("change", function () {
		getData();
	});

});

function getData() {

	var pubs = $("#pub-select input:checkbox:checked").map(function () {
		return $(this).attr("data-id");
	});
	pubs = $.makeArray(pubs);
	pubs = pubs.join(",");

	var years = $("#year-select button.active").map(function () {
		return $(this).attr("data-val");
	});
	years = $.makeArray(years);
	years = years.join(",");

	var ID = $("#selectID").val();

	var page = $.bbq.getState("page");
	page = (page) ? page : "";

	var $combined = $("#combine-btn");

	var daterange = $("#date-picker").val();
	var combined = ($combined.length) ? ($combined.hasClass("active")) ? 1 : 0 : 'none';

	var dID = $.bbq.getState("dID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	$("#whole-area .loadingmask").show();
	var tolerance = $("#tolerance").val();

	var $scrollpane = $("#whole-area .scroll-pane");

	var wh = $scrollpane.height();
	wh = wh - 90;
	var rows = wh / 27;
	rows = Math.floor(rows);


	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/ab/data/reports/marketer_targets/_data", {"pubs":pubs, "years":years, "daterange":daterange, "combined":combined, "ID":ID, "dID":dID, "order":order, "tolerance":tolerance,"page":page,"rows":rows}, function (data) {
		data = data['data'];

		$("#scroll-container").jqotesub($("#template-report-figures"), data);

		var $recordsList = $("#scroll-container");
		var $pagenation = $("#pagination");
		var bottomChanges = 42;
		if (data.targets[0]) {
			$recordsList.jqotesub($("#template-report-figures"), data);

			if (data['pagination']['pages'].length > 1) {
				$pagenation.jqotesub($("#template-report-figures-pagination"), data['pagination']).stop(true, true).fadeIn(transSpeed);
				bottomChanges = $pagenation.outerHeight() + bottomChanges;

			} else {
				$pagenation.stop(true, true).fadeOut(transSpeed)
			}

		} else {
			$recordsList.html('<div style="padding-top: 10px;padding-bottom: 10px;"><table class="table table-condensed table-bordered s records" id="record-list" style="margin-right: 15px;"><tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot></table></div>')
		}






		$scrollpane.jScrollPane(jScrollPaneOptionsMP);




		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}));

}
function getTarget(){

	var ID = $.bbq.getState("ID");
	console.log(ID);

	$('#targets-modal').modal('show')
}