/*
 * Date: 2012/06/20 - 8:49 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {
	$("#wizard-previous .btn").removeAttr("disabled");
	$("#wizard-next .btn, #wizard-previous .btn").on("click",function(){
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
		if (confirm("Are you sure you want to delete this date?")) {
			$("#left-area .loadingmask").show();
			$.post("/setup/save/ab/dates/_delete/?ID=" + ID + "&pID=" + pID + "&cID=" + cID + "&app=ab", function (r) {
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
		$.post("/setup/save/ab/dates/_save/?ID=" + ID+"&pID="+pID+"&cID="+cID+"&app=ab", data, function (r) {
			r = r['data'];
			if (r['error'].length) {
				var str = "";
				for (var i in r['error']) {
					str += '<div class="alert alert-error">' + r['error'][i] + '</div>'
				}
				$("#left-area .loadingmask").fadeOut(transSpeed);
				$errorArea.html(str);
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
	var page = $.bbq.getState("page");
	page = (page) ? page : "";

	var height = $("#record-list-middle").height();
	var records = height / 27;
	records = Math.floor(records) - 1;

	var ID = $.bbq.getState("ID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	$("#right-area .loadingmask").show();
	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/setup/data/ab/dates/_list", {"pID": pID, "cID": cID, "app": "ab", "page":page, "nr":records, "order":order}, function (data) {
		data = data['data'];

		var $recordsList = $("#record-list");
		var $pagenation = $("#pagination");

		$("#wizard-next .btn").attr("disabled","disabled");
		if (data['records'][0]) {
			$("#wizard-next .btn").removeAttr("disabled");
			$recordsList.jqotesub($("#template-list"), data['records']);
			$("#record-list tr.active").removeClass("active");
			$("#record-list tr[data-id='" + ID + "']").addClass("active");
			if (data['pagination']['pages'].length > 1) {
				$pagenation.jqotesub($("#template-pagination"), data['pagination']).stop(true, true).fadeIn(transSpeed);
			} else {
				$pagenation.stop(true, true).fadeOut(transSpeed)
			}
		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
		}
		$("#record-list-middle").css("bottom", $("#record-details-bottom").outerHeight());
		right_pane.reinitialise();
		$("#right-area .loadingmask").fadeOut(transSpeed);

	}));

}
function getDetails() {
	var ID = $.bbq.getState("ID");

	$("#record-list tr.active").removeClass("active");
	$("#record-list tr[data-id='" + ID + "']").addClass("active");
	$("#left-area .loadingmask").show();

	for (var i = 0; i < detailsRequest.length; i++) detailsRequest[i].abort();
	detailsRequest.push($.getJSON("/setup/data/ab/dates/_details", {"pID": pID, "cID": cID, "app": "ab","ID":ID}, function (data) {
		data = data['data'];
		$("#form-area").jqotesub($("#template-details"), data);

		var default_date = "";
		if (data['ID']) {
			default_date = data['publish_date'];
		} else {
			if (data['suggestions'].length) {
				default_date = data['suggestions'][0]['date'];
			}

		}
		//console.log(default_date)
		$("#suggested_dates tr.active").removeClass("active");
		if (default_date) {
			$("#suggested_dates tr[data-date='" + default_date + "']").addClass("active");

		}
		if (!default_date)default_date = 'today';
		default_date = Date.parse(default_date);

		$("#datepicker").datepicker({
			format     :"yy-mm-dd",
			altField   :"#publish_date",
			altFormat  :"yy-mm-dd",
			changeMonth:true,
			changeYear :true,
			defaultDate:default_date
		});
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		$("#left-area .loadingmask").fadeOut(transSpeed);

	}));
}