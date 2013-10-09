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

	$(document).on("click", "#record-list .record", function (e) {
		var $this = $(this), ID = $this.attr("data-id");

		var $cur_pub = $(e.target).closest(".cur-pub");
		$.bbq.pushState({"ID":ID});
		if ($cur_pub.length) {
			$.post("/app/ab/save/admin_publications/_pub/?ID=" + ID, function (r) {
				getList();
				getDetails();
			});
		} else {
			getDetails();
		}

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
			$.post("/app/ab/save/admin_publications/_delete/?ID=" + ID, function (r) {
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

		var colours = "";
		colours = [];
		$("#usecolours input:checked").each(function(){
			colours.push($(this).val());
		});
		colours = colours.join(",");
		data = data + "&colours="+ colours;

		var $errorArea = $("#errorArea").html("");

		var ID = $.bbq.getState("ID");
		$("#left-area .loadingmask").show();
		$.post("/app/ab/save/admin_publications/_save/?ID=" + ID, data, function (r) {
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


		$.getData("/app/ab/logs/publications", {}, function (data) {

			$logarea = $("#view-log table").html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>');
			if (data[0]) {
				$logarea.jqotesub($("#template-admin-logs"), data);

			}
			$("#view-log").modal("show");

		}, "logs");

	});

});

function getList() {

	var ID = $.bbq.getState("ID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	$("#right-area .loadingmask").show();

	$.getData("/app/ab/data/admin_publications/_list", {"order":order}, function (data) {


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

	}, "list");

}
function getDetails() {
	var ID = $.bbq.getState("ID");

	$("#record-list tr.active").removeClass("active");
	$("#record-list tr[data-id='" + ID + "']").addClass("active");
	$("#left-area .loadingmask").show();


	$.getData("/app/ab/data/admin_publications/_details", {"ID":ID}, function (data) {

		$("#form-area").jqotesub($("#template-details"), data);

		if (!data['ID']) {
			$("#datepicker").datepicker({
				format     :"yy-mm-dd",
				altField   :"#publish_date",
				altFormat  :"yy-mm-dd",
				changeMonth:true,
				changeYear :true
			});
		}
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		$("#uID").select2({});

		$("#left-area .loadingmask").fadeOut(transSpeed);

	}, "details");
}