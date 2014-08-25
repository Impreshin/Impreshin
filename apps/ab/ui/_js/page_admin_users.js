var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {
	getList();
	getDetails();

	$(document).on("click", "#record-list .record", function (e) {
		var $this = $(this), ID = $this.attr("data-id");

		var $cur_pub = $(e.target).closest(".cur-pub");
		$.bbq.pushState({"ID":ID});
		if ($cur_pub.length) {
			$.post("/app/ab/save/admin_users/_pub/?ID=" + ID, function (r) {
				getList();
				getDetails();
			});
		} else {
			getDetails();
		}

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
		if (confirm("Are you sure you want to remove this user from the company?")) {
			$("#left-area .loadingmask").show();
			$.post("/app/ab/save/admin_users/_delete/?ID=" + ID, function (r) {
				$.bbq.removeState("ID");
				getList();
				getDetails();
			});
		}

	});
	$(document).on("click", "#btn-add-app", function () {
		var ID = $.bbq.getState("ID");
		if (confirm("Are you sure you want to allow access to Adverts?")) {
			$("#left-area .loadingmask").show();
			$.post("/app/ab/save/admin_users/add_app/?ID=" + ID, function (r) {
				getList();
				getDetails();
			});
		}

	});
	$(document).on("click", "#btn-remove-app", function () {
		var ID = $.bbq.getState("ID");
		if (confirm("Are you sure you want to remove access to Adverts?")) {
			$("#left-area .loadingmask").show();
			$.post("/app/ab/save/admin_users/remove_app/?ID=" + ID, function (r) {
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
		if (ID == "undefined") {
			ID = "";
		}
		if (!ID) {
			ID = "";
		}
		$("#left-area .loadingmask").show();
		$.post("/app/ab/save/admin_users/_save/?ID=" + ID, data, function (r) {
			r = r['data'];
			if (r['exists']) {
				if (confirm("This user already exists. do you want to add them to the application / company?")) {
					$.post("/app/ab/save/admin_users/add_company/?ID=" + r['exists'], function () {
						$.bbq.pushState({"ID":r['exists']});
						getList();
						getDetails();
					});
				} else {
					str = '<div class="alert alert-error">A user with that email already exists</div>';
					$("#left-area .loadingmask").fadeOut(transSpeed);
					$errorArea.html(str);
				}

				return false;
			}
			if (r['error'].length) {
				var str = "";
				for (var i in r['error']) {
					str += '<div class="alert alert-error">' + r['error'][i] + '</div>';
				}
				$("#left-area .loadingmask").fadeOut(transSpeed);
				$errorArea.html(str);
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

function getList() {
	var ID = $.bbq.getState("ID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	$("#right-area .loadingmask").show();

	$.getData("/app/ab/data/admin_users/_list", {"order":order}, function (data) {


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
		right_pane.reinitialise();
		$("#right-area .loadingmask").fadeOut(transSpeed);

	}, "list");

}
function getDetails() {
	var ID = $.bbq.getState("ID");

	$("#record-list tr.active").removeClass("active");
	$("#record-list tr[data-id='" + ID + "']").addClass("active");
	$("#left-area .loadingmask").show();


	$.getData("/app/ab/data/admin_users/_details", {"ID":ID}, function (data) {

		$("#form-area").jqotesub($("#template-details"), data);

		$("#ab_marketerID").select2({});
		$("#ab_productionID").select2({});

		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		$("#left-area .loadingmask").fadeOut(transSpeed);

	}, "details");
}