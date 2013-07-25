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

	$(document).on("click", "#record-list .record, .pages .record", function () {
		var $this = $(this);
		$.bbq.pushState({"ID":$this.attr("data-id")});
		getDetails();
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
		if (confirm("Are you sure you want to delete this record?")) {
			$("#left-area .loadingmask").show();
			$.post("/setup/save/ab/sections/_delete/?ID=" + ID + "&pID=" + pID + "&cID=" + cID + "&app=ab", function (r) {
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
		$.post("/setup/save/ab/sections/_save/?ID=" + ID + "&pID=" + pID + "&cID=" + cID + "&app=ab", data, function (r) {
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
	$(document).on("click", "#copyfrom-btn", function () {
		var copyfromID = $("#copyfrom").val();
		//console.log(pID)
		if (confirm("Clicking ok will copy all the records from the selected publication into the current publication?")) {
			$("#right-area .loadingmask").show();
			$.post("/apps/ab/save/admin_sections/_copyfrom/?pID=" + copyfromID+ "&new_pID="+pID,"", function (r) {

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
	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/setup/data/ab/sections/_list", {"pID": pID, "cID": cID, "app": "ab", "order":order}, function (data) {
		data = data['data'];

		var $recordsList = $("#record-list");
		var $pagenation = $("#pagination");
		$("#wizard-next .btn").attr("disabled", "disabled");
		if (data['records'][0]) {
			$("#wizard-next .btn").removeAttr("disabled");
			$recordsList.jqotesub($("#template-list"), data['records']);
			$("#record-list tr.active").removeClass("active");
			$("#record-list tr[data-id='" + ID + "']").addClass("active");

		} else {
			if (data['copyfrom'] && data['copyfrom'].length) {
				copyfrom(data['copyfrom'], $recordsList);
			}
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
	detailsRequest.push($.getJSON("/setup/data/ab/sections/_details", {"pID": pID, "cID": cID, "app": "ab", "ID":ID}, function (data) {
		data = data['data'];
		$("#form-area").jqotesub($("#template-details"), data);
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);

		$('#section_colour').miniColors({
			letterCase:'uppercase'

		});

		$("#left-area .loadingmask").fadeOut(transSpeed);

	}));
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