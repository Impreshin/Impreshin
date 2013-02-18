/*
 * Date: 2012/06/20 - 8:49 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {
	$("#wizard-previous .btn").removeAttr("disabled");
	$("#wizard-next .btn, #wizard-previous .btn").on("click", function () {
		var $this = $(this), url = $this.attr("data-url");

		window.location = url;
	});
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
			$.post("/setup/save/ab/categories/_pub/?ID=" + ID + "&pID=" + pID + "&cID=" + cID + "&app=ab", function (r) {
				getList();
				getDetails();
			});
		} else {
			getDetails();
		}

	});


	$(document).on("click", "#btn-new", function () {
		$.bbq.removeState("ID");
		getDetails();
	});
	$(document).on("click", "#btn-delete", function () {
		var ID = $.bbq.getState("ID");
		if (confirm("Are you sure you want to delete this record?")) {
			$("#left-area .loadingmask").show();
			$.post("/setup/save/ab/categories/_delete/?ID=" + ID + "&pID=" + pID + "&cID=" + cID + "&app=ab", function (r) {
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
		$.post("/setup/save/ab/categories/_save/?ID=" + ID + "&pID=" + pID + "&cID=" + cID + "&app=ab", data, function (r) {
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



});

function getList() {

	var ID = $.bbq.getState("ID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	$("#right-area .loadingmask").show();
	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/setup/data/ab/categories/_list", {"pID": pID, "cID": cID, "app": "ab","order":order}, function (data) {
		data = data['data'];

		var $recordsList = $("#record-list");
		var $pagenation = $("#pagination");
		$("#wizard-next .btn").attr("disabled", "disabled");
		if (data['records'][0]) {

			var shownext = false;
			$.each(data['records'], function (i, v) {
				if (v['currentPub'] == '1') {
					shownext = true;
				}
			});

			if (shownext) {
				$("#wizard-next .btn").removeAttr("disabled");
			}
			$recordsList.jqotesub($("#template-list"), data['records']);
			$("#record-list tr.active").removeClass("active");
			$("#record-list tr[data-id='" + ID + "']").addClass("active");

		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
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

				$.post("/setup/save/ab/categories/_sort/?" + "pID=" + pID + "&cID=" + cID + "&app=ab", {"order":rec}, function (t) {

				});
			}
		});
		$recordsList.find("tbody").disableSelection();

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
	detailsRequest.push($.getJSON("/setup/data/ab/categories/_details", {"pID": pID, "cID": cID, "app": "ab","ID":ID}, function (data) {
		data = data['data'];
		$("#form-area").jqotesub($("#template-details"), data);
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);

		$("#left-area .loadingmask").fadeOut(transSpeed);

	}));
}