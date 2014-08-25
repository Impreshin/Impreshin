var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {
	getDetails();
	

	
	
	$(document).on("submit", "#capture-form", function (e) {
		e.preventDefault();
		var $this = $(this);
		var data = $this.serialize();

	

		var $errorArea = $("#errorArea").html("");

		
		$("#left-area .loadingmask").show();
		$.post("/app/ab/save/admin_company/_save/", data, function (r) {
			r = r['data'];
			if (r['error'].length) {
				var str = "";
				for (var i in r['error']) {
					str += '<div class="alert alert-error">' + r['error'][i] + '</div>'
				}
				$("#whole-area .loadingmask").fadeOut(transSpeed);
				$errorArea.html(str);
				$("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
			} else {

				$.bbq.removeState("ID");
				getDetails();
			}

		});
		return false;
	});

	

});


function getDetails() {
	var ID = $.bbq.getState("ID");

	
	$("#whole-area .loadingmask").show();


	$.getData("/app/ab/data/admin_company/_details", {"ID":ID}, function (data) {

		$("#form-area").jqotesub($("#template-details"), data);

		
		$("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);

		$("#whole-area .loadingmask").fadeOut(transSpeed);

	}, "details");
}