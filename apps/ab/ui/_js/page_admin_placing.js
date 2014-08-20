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
		if (confirm("Are you sure you want to delete this placing?")) {
			$("#left-area .loadingmask").show();
			$.post("/app/ab/save/admin_placing/_delete/?ID=" + ID, function (r) {
				$.bbq.removeState("ID");
				getList();
				getDetails();
			});
		}

	});
	$(document).on("click", "#btn-new-sub_placing", function () {
		var c = $("#subplacingtable tbody tr").length;

		var data = {
			"ID":"new|"+c
		};
		$("#subplacingtable tbody").jqoteapp($("#template-new-sub-placing"), data);
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptionsMP);

	});


	$(document).on("submit", "#capture-form", function (e) {
		e.preventDefault();
		var $this = $(this);
		var data = $this.serialize();

		var $errorArea = $("#errorArea").html("");

		var sub_placing_IDs = $("#subplacingtable tbody tr").map(function(){
			return $(this).attr("data-id");
		}).get().join(",");

		data = data + "&sub_placing_IDs="+sub_placing_IDs;

	//	console.log(sub_placing_IDs);
	//	return false;


		var ID = $.bbq.getState("ID");
		$("#left-area .loadingmask").show();
		$.post("/app/ab/save/admin_placing/_save/?ID=" + ID, data, function (r) {
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


		$.getData("/app/ab/logs/placing", {}, function (data) {

			$logarea = $("#view-log table").html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>');
			if (data[0]) {
				$logarea.jqotesub($("#template-admin-logs"), data);

			}
			$("#view-log").modal("show");

		},"logs");

	});
	$(document).on("click", "#copyfrom-btn", function () {
		var copyfromID = $("#copyfrom").val();

		if (confirm("Clicking ok will copy all the records from the selected publication into the current publication?")) {
		$("#right-area .loadingmask").show();
		$.post("/app/ab/save/admin_placing/_copyfrom/?pID=" + copyfromID, "", function (r) {

			getList();
		});
		}

	});

});

function getList() {

	var ID = $.bbq.getState("ID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	$("#right-area .loadingmask").show();

	$.getData("/app/ab/data/admin_placing/_list", {"order":order}, function (data) {


		var $recordsList = $("#record-list");
		var $pagenation = $("#pagination");
		if (data['records'][0]) {
			$recordsList.jqotesub($("#template-list"), data['records']);
			$("#record-list tr.active").removeClass("active");
			$("#record-list tr[data-id='" + ID + "']").addClass("active");

		} else {
			if (data['copyfrom'].length) {
				copyfrom(data['copyfrom'] && data['copyfrom'], $recordsList);
			}
		}
		$recordsList.find("tbody").sortable({
			'axis'       :"y",
			'containment':"#record-list-middle",
			update       :function (event, ui) {
				var rec = [];
				$("#record-list tr").each(function () {
					rec.push($(this).attr("data-id"));
				});
				rec = rec.join(",");

				$.post("/app/ab/save/admin_placing/_sort/", {"order":rec}, function (t) {

				});
			}
		});
		$recordsList.find("tbody").disableSelection();

		$("#record-list-middle").css("bottom", $("#record-details-bottom").outerHeight());
		$("#record-list-middle").jScrollPane(jScrollPaneOptions);
		$("#right-area .loadingmask").fadeOut(transSpeed);

	},"list");

}
function getDetails() {
	var ID = $.bbq.getState("ID");

	$("#record-list tr.active").removeClass("active");
	$("#record-list tr[data-id='" + ID + "']").addClass("active");
	$("#left-area .loadingmask").show();


	$.getData("/app/ab/data/admin_placing/_details", {"ID":ID}, function (data) {

		$("#form-area").jqotesub($("#template-details"), data);

		$recordsList = $("#subplacingtable tbody")
		$recordsList.sortable({
			'axis'       : "y",
			'containment': "#subplacingtable",
			update       : function (event, ui) {
				/*
				var rec = [];
				$("#record-list tr").each(function () {
					rec.push($(this).attr("data-id"));
				});
				rec = rec.join(",");

				$.post("/app/ab/save/admin_placing/_sort/", {"order": rec}, function (t) {

				});*/
			}
		});
		//$recordsList.disableSelection();


		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		$("#uID").select2({});

		$("#left-area .loadingmask").fadeOut(transSpeed);

	},"details");
}
function copyfrom(data, $recordsList) {
	var select = "";
	select += '<select id="copyfrom" name="copyfrom" class="span3" style="float:left; margin-right: 0px;">';
	for (var i in data) {
		select += '<option value="' + data[i].ID + '">' + data[i].label + ' (' + data[i].count + ')</option>';
	}

	select += '</select>';

	$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found<hr>Copy records from<div class="">' + select + '<button id="copyfrom-btn" type="button" class="span1 btn btn-mini">Copy</button></div></td></tr></tfoot>');

}