/*
 * Date: 2012/06/20 - 8:49 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");
var _data = "";
$(document).ready(function () {
	getDetails();
	$(document).on("click","#cancel-btn",function(){
		getDetails();
	});

	$(document).on("click","#import-records-table tr.record td",function(e){
		var $this = $(this);
		if (!$this.find("input:checkbox").length){
			$this.closest("tr").find("input:checkbox").trigger("click");
		}

	});
	$(document).on("click","#save-btn",function(){
		var $form = $("#capture-form")
		var data = $form.serialize();
		var $errorArea = $("#errorArea").html("");
		$("#left-area .loadingmask").show();
		$.post("/app/ab/save/admin_accounts_import/_save/", data, function (r) {
			if (r['data']['error'].length) {
				var str = "";
				for (var i in r['data']['error']) {
					str += '<div class="alert alert-error">' + r['data']['error'][i] + '</div>'
				}
				$("#left-area .loadingmask").fadeOut(transSpeed);
				$errorArea.html(str);
				$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
			} else {

				var alert_str = [];
				if (r['data']["records"]['added']) alert_str.push(r['data']["records"]['added'] + " records added");
				if (r['data']["records"]['added']) alert_str.push(r['data']["records"]['edited'] + " records edited");
				alert_str = alert_str.join("\n");
				if (alert_str) {

					getDetails();
					alert(alert_str);
				}

			}

		});
		return false;
	});

	$(document).on("submit", "#capture-form", function (e) {
		e.preventDefault();
		var $this = $(this);
		var data = $this.serialize();

		var $errorArea = $("#errorArea").html("");

		var ID = $.bbq.getState("ID");
		$("#left-area .loadingmask").show();



		$.post("/app/ab/data/admin_accounts_import/_details/", data, function (r) {
			if (r['data']['error'].length) {
				var str = "";
				for (var i in r['data']['error']) {
					str += '<div class="alert alert-error">' + r['data']['error'][i] + '</div>'
				}
				$("#left-area .loadingmask").fadeOut(transSpeed);
				$errorArea.html(str);
				$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
			} else {

				getDetails(r.data);
			}

		});
		return false;
	});

});

function getDetails(data) {
	var ID = $.bbq.getState("ID");

	$("#record-list tr.active").removeClass("active");
	$("#record-list tr[data-id='" + ID + "']").addClass("active");
	$("#left-area .loadingmask").show();

	if (data) {
		
		$("#form-area").jqotesub($("#template-details"), data);
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);

		$("#left-area .loadingmask").fadeOut(transSpeed);

		save_button();
	} else {
		getData();
	}
}
function getData() {
	$.getData("/app/ab/data/admin_accounts_import/_details", "", function (data) {
		getDetails(data)
	},"details");
}
function save_button(){

	if ($("#import-records-table").length){
		var disabled = true;
		var stats_found = $("#import-records-table tbody tr input:checkbox").length, stats_exists = $("#import-records-table tbody tr.highlight input:checkbox").length, stats_new = stats_found - stats_exists, stats_selected = 0;
		;

		$("#import-records-table input:checkbox").each(function () {
			if ($(this).is(":checked")) {
				disabled = false;
				stats_selected++;
			} else {
			}
		});

		var $save_btn = $("#save-btn");
		if (disabled) {
			$save_btn.attr("disabled", "disabled")
		} else {
			$save_btn.removeAttr("disabled");
		}
		if (stats_found){
			alert("Stats\n" + stats_found + " Records found\n" + stats_exists + " Records already exists\n" + stats_new + " New records\n" + stats_selected + " Records selected");
		} else {
			alert("No useable records found\n\nCheck your csv and or change optios under CSV on the right")
		}

	}


}