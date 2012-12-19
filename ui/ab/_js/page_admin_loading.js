/*
 * Date: 2012/06/20 - 8:49 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {
	getList();
	getDetails();
	$(document).on("click", ".pagination a", function (e) {
		e.preventDefault();
		var $this = $(this).parent();
		$.bbq.pushState({"page":$this.attr("data-page")});
		getList();
	});

	$(document).on("click", "#record-list .record, .pages .record", function () {
		var $this = $(this);
		$.bbq.pushState({"ID":$this.attr("data-id")});
		getDetails();
	});

	$(document).on("click", "#suggested_dates tr", function () {
		var default_date = $(this).attr("data-date");
		$("#suggested_dates tr.active").removeClass("active");
		$("#suggested_dates tr[data-date='" + default_date + "']").addClass("active");
		default_date = Date.parse(default_date);
		$('#datepicker').datepicker('setDate', default_date);

	});

	$(document).on("change", "#searchform select", function () {
		$("#searchform").trigger("submit")
	});
	$(document).on("submit", "#searchform", function (e) {
		e.preventDefault();
		getList();
		return false;
	});

	$(document).on("click", "#reload-btn", function () {
		getList();
		getDetails();
	});
	$(document).on("click", "#btn-new", function () {
		$.bbq.removeState("ID");
		getDetails();
	});
	$(document).on("click", "#btn-delete", function () {
		var ID = $.bbq.getState("ID");
		if (confirm("Are you sure you want to delete this loading?")) {
			$("#left-area .loadingmask").show();
			$.post("/ab/save/admin_loading/_delete/?ID=" + ID, function (r) {
				$.bbq.removeState("ID");
				getList();
				getDetails();
			});
		}

	});
	$(document).on("submit", "#capture-form", function (e) {
		e.preventDefault();
		var $this = $(this);
		var data = $this.serialize();

		var $errorArea = $("#errorArea").html("");

		var ID = $.bbq.getState("ID");
		$("#left-area .loadingmask").show();
		$.post("/ab/save/admin_loading/_save/?ID=" + ID, data, function (r) {
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
				getList();
				getDetails();
			}

		});
		return false;
	});

	$(document).on("click", ".order-btn", function (e) {
		e.preventDefault();
		var $this = $(this);
		$this.closest("table").find(".order-btn").removeClass("asc desc");
		//$this.addClass("active");
		$.bbq.pushState({"order":$this.attr("data-col")});

		getList();
		$.bbq.removeState("order");

	});

	$(document).on("click", "#btn-log", function (e) {
		e.preventDefault();
		var $this = $(this);

		for (var i = 0; i < logsRequest.length; i++) logsRequest[i].abort();
		logsRequest.push($.getJSON("/ab/logs/loading", {}, function (data) {
			data = data['data'];
			$logarea = $("#view-log table").html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>');
			if (data[0]) {
				$logarea.jqotesub($("#template-admin-logs"), data);

			}
			$("#view-log").modal("show");

		}));

	});

});

function getList() {

	var ID = $.bbq.getState("ID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	$("#right-area .loadingmask").show();
	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/ab/data/admin_loading/_list", {"order":order}, function (data) {
		data = data['data'];

		var $recordsList = $("#record-list");
		var $pagenation = $("#pagination");
		if (data['records'][0]) {
			$recordsList.jqotesub($("#template-list"), data['records']);
			$("#record-list tr.active").removeClass("active");
			$("#record-list tr[data-id='" + ID + "']").addClass("active");

		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
		}

		$("#record-list-middle").css("bottom", $("#record-details-bottom").outerHeight());
		$("#record-list-middle").jScrollPane(jScrollPaneOptions);
		$("#right-area .loadingmask").fadeOut(transSpeed);

	}));

}
function getDetails() {
	var ID = $.bbq.getState("ID");

	$("#record-list tr.active").removeClass("active");
	$("#record-list tr[data-id='" + ID + "']").addClass("active");
	$("#left-area .loadingmask").show();

	for (var i = 0; i < detailsRequest.length; i++) detailsRequest[i].abort();
	detailsRequest.push($.getJSON("/ab/data/admin_loading/_details", {"ID":ID}, function (data) {
		data = data['data'];
		$("#form-area").jqotesub($("#template-details"), data);
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		$("#uID").select2({});

		$("#left-area .loadingmask").fadeOut(transSpeed);

	}));
}