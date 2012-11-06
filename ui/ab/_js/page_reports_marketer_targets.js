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

	$(document).on("click","#new-target",function(){
		getTarget();
	});

	$(document).on('hide', '#targets-modal', function () {
		$.bbq.removeState("ID");
		$("#record-list .record.active").removeClass("active");
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

	$(document).on("click", ".focustrigger", function () {
		$(this).parent().find("input").trigger("focus")
	});

	$(document).on("submit", "#capture-form", function (e) {
		e.preventDefault();
		var $this = $(this);
		var data = $this.serialize();


		var $errorArea = $("#errorArea").html("");
		var mID = $("#selectID").val();

		var ID = $.bbq.getState("ID");
		$("#left-area .loadingmask").show();
		$.post("/ab/save/admin_marketers_targets/_save/?ID=" + ID + "&mID=" + mID, data, function (r) {
			r = r['data'];
			if (r['error'].length) {
				var str = "";
				for (var i in r['error']) {
					str += '<div class="alert alert-error">' + r['error'][i] + '</div>'
				}
				$("#left-area .loadingmask").fadeOut(transSpeed);
				$errorArea.html(str);
				$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
			} else {

				$.bbq.removeState("ID");
				getData();
				$("#targets-modal").modal("hide");
			}

		});
		return false;
	});
	$(document).on("click", "#btn-delete", function () {
		var ID = $.bbq.getState("ID");
		if (confirm("Are you sure you want to delete this record?")) {
			$.post("/ab/save/admin_marketers_targets/_delete/?ID=" + ID, function (r) {
				$.bbq.removeState("ID");
				getData();
				$("#targets-modal").modal("hide");
			});
		}

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
		var bottomChanges = 0;
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

function getTarget() {
	var ID = $.bbq.getState("ID");
	var mID = $("#selectID").val();

	$("#record-list tr.active").removeClass("active");
	$("#record-list tr[data-id='" + ID + "']").addClass("active");

	for (var i = 0; i < detailsRequest.length; i++) detailsRequest[i].abort();
	detailsRequest.push($.getJSON("/ab/data/reports/marketer_targets/_details", {"ID":ID,"mID":mID}, function (data) {
		data = data['data'];
		$("#targets-modal").jqotesub($("#template-report-figures-target-form"), data);

		$("#targets-modal #date_from").datepicker({
			changeMonth:true,
			changeYear :true,
			dateFormat :"yy-mm-dd",
			defaultDate:data['details']['date_from']
		});
		$("#targets-modal #date_to").datepicker({
			changeMonth:true,
			changeYear :true,
			dateFormat :"yy-mm-dd",
			defaultDate:data['details']['date_to']
		});

		$('#targets-modal').modal('show')

	}));
}