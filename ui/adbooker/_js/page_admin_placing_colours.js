/*
 * Date: 2012/06/20 - 8:49 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function(){
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
			$.post("/ab/save/admin_placing_colours/_pub/?ID=" + ID, function (r) {
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
		$.bbq.removeState("ID");
		getList();
		getDetails();
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
		if (confirm("Are you sure you want to delete this Colour?")){
			$("#left-area .loadingmask").show();
			$.post("/ab/save/admin_placing_colours/_delete/?ID=" + ID, function (r) {
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
		var colour = $("#colour button.active").attr("data-val");
		data = data + "&colour="+colour;

		var placingID = $("#placingID").val();


		var $errorArea = $("#errorArea").html("");

		var ID = $.bbq.getState("ID");
		$("#left-area .loadingmask").show();
		$.post("/ab/save/admin_placing_colours/_save/?ID=" + ID+"&placingID="+placingID, data, function (r) {
			r = r['data'];
			if (r['error'].length){
				var str="";
				for (var i in r['error']) {
					str += '<div class="alert alert-error">' + r['error'][i] + '</div>'
				}
				$("#left-area .loadingmask").fadeOut(transSpeed);
				$errorArea.html(str);
				$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
			} else {

				$.bbq.pushState({"ID":r['ID']});
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





});

function getList(){


	var ID = $.bbq.getState("ID");


	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	var placingID = $("#placingID").val();


	$("#right-area .loadingmask").show();
	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/ab/data/admin_placing_colours/_list/",{"order":order,"placingID":placingID}, function (data) {
		data = data['data'];

		var placings = $.map(data['placing'], function (record) {
			var selected = "";
			if (placingID == record['ID']) {
				selected = 'selected="selected"';
			} else {
				selected = "";
			}
			var padding = "";

			var recordcount = record['colourCount'];
			if (recordcount == '0') {
				padding = "";
			} else {

				padding = ' (' + record['colourCount'] + ')';
			}
			padding = PadDigits(padding, 10);

			return '<option value="' + record['ID'] + '" ' + selected + '>' + padding + record['placing'] + '</option>';
		});
		placings = placings.join("");

		$("#placingID").html(placings);





		var $recordsList = $("#record-list");
		var $pagenation = $("#pagination");
		if (data['records'][0]) {
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

				$.post("/ab/save/admin_placing_colours/_sort/?placingID="+placingID, {"order":rec}, function (t) {

				});
			}
		});
		$recordsList.find("tbody").disableSelection();


		$("#record-list-middle").css("bottom", $("#record-details-bottom").outerHeight() + 42);
		$("#record-list-middle").jScrollPane(jScrollPaneOptions);
		$("#right-area .loadingmask").fadeOut(transSpeed);

	}));

}
function getDetails(){
	var ID = $.bbq.getState("ID");


	$("#record-list tr.active").removeClass("active");
	$("#record-list tr[data-id='" + ID + "']").addClass("active");
	$("#left-area .loadingmask").show();


	for (var i = 0; i < detailsRequest.length; i++) detailsRequest[i].abort();
	detailsRequest.push($.getJSON("/ab/data/admin_placing_colours/_details/", {"ID":ID}, function (data) {
		data = data['data'];
		$("#form-area").jqotesub($("#template-details"), data);
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);




		$("#left-area .loadingmask").fadeOut(transSpeed);

	}));
}
function PadDigits(n, totalDigits) {
	n = n.toString();
	var pd = '';
	if (totalDigits > n.length) {
		for (i = 0; i < (totalDigits - n.length); i++) {
			pd += '&nbsp';
		}
	}
	return  n.toString() + pd;
}