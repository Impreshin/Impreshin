
$(document).ready(function () {

	getFormData();

	$(document).on("submit", "#modal-delete form", function (e) {
		e.preventDefault();

		var data = $(this).serialize();

		if ($("#delete_reason").val() == "") {
			alert("You must specify a reason");
		} else {
			$("input", $(this)).attr("disabled", "disabled");
			$.post("/app/cm/save/form/_delete/?ID=" + var_record_ID, data, function (r) {
				alert("Record deleted");
				document.location = "/app/cm/";
			});

		}

	});
	$(document).on("click", ".view-record-btn", function () {
		var id = $(this).attr("data-link-id");
		if (id) {
			$(this).closest(".modal").modal("hide");
			$.bbq.pushState({"ID": id});

			getDetails();
		}

	});
	
	
	

	$(document).on("submit", "#main-form", function (e) {
		e.preventDefault();
		form_submit();
		return false;

	});
	$(document).on("reset", "#main-form", function (e) {
		e.preventDefault();

		getFormData();
		return false;

	});
	

	$(document).on("click", "#booking-type button", function () {
		var type = $(this).attr("data-type");
		$.bbq.pushState({"type": type});
		
		getFormData()
	});

	
	

	

});

function getFormData() {
	var ID = var_record_ID;
	//console.log("getFormData"); 

	var $bookingTypeBtns = $("#booking-type");
	
	
	
	var type = $bookingTypeBtns.find("button.active").attr("data-type");

	$("#left-area .loadingmask").show();

	$("#whole-area .loadingmask").show();
	
	$.getData("/app/cm/data/form/_details", {"ID": ID,"type":type}, function (data) {

		var title = "";
		if (data['details']['ID']) {
			title = "Edit Record";
		} else {
			title = "New Record";
		}
		document.title = "CM - Form - " + title;

		var toolbar = {
			"heading": title,
			"data"   : data
		};
		var type = data['settings']['type'];

		$("#scroll-container").jqotesub($("#template-form"), data);
		$("#form-body").jqotesub($("#template-form-"+type), data);
		$("#maintoolbar").jqotesub($("#template-toolbar"), toolbar);


		
		
		$("select").select2();
		
		

		formLoaded(data);
		resizeform();
		//setTimeout(resizeform, 1000)
		$("#whole-area .loadingmask").fadeOut(transSpeed,function(){}());
	}, "form_data");

}


function formLoaded(data) {
	
	var pane = $(".form-body").jScrollPane(jScrollPaneOptionsMP);
	var api = pane.data("jsp");
	var $cm = $("#cm-block");
	var body = "";
	


	$("select.select2").select2();


}
function resizeform() {

	var pane = $(".form-body").jScrollPane(jScrollPaneOptionsMP);
	var api = pane.data("jsp");
	//api.reinitialise();

	scrolling(api);
}

function form_submit() {
	$form = $("#main-form");
	$(".fielderror", $form).remove();

	var type = $("#booking-type button.active").attr("data-type");
	

	if (!type) {
		alert("Something went wrong, please select a record type");
		return false;
	}

	var submit = true;

	var $fld = "";
	$fld = $("#title");
	if (!$fld.val()) {
		submit = error_msg($fld, "<strong>Title</strong> is Required");
	}

	$fld = $("#authorID");
	if (!$fld.val()) {
		submit = error_msg($fld, "<strong>Author</strong> is Required");
	}



	resizeform();

	if (submit) {
		$("#pagecontent .loadingmask").show();
		var data = $form.serialize();
		$.post("/app/cm/save/form/form?ID=" + var_record_ID + "&type=" + type + "&locked="+locked, data, function (response) {


			if (response['error'] && response['error'].length) {
				var str = "";
				for (var i in response.error) {
					$fld = $("#"+response.error[i].field) ;
					error_msg($fld, response.error[i].msg);
					
				}
				$("#pagecontent .loadingmask").fadeOut(transSpeed);
				resizeform();
			} else {

				getFormData();
				$("#pagecontent .loadingmask").fadeOut(transSpeed);
				$("#modal-form").jqotesub($("#template-modal-form"), response).modal("show");
			}
			
			
			//$("#record-ID").val(response[0]['ID']);
			
		});
	}

}
function error_msg($fld, msg) {
	var str = '<div class="alert fielderror alert-error">' + msg + '</div>';
	if (!$fld.hasClass("control-group") && !$fld.hasClass("fieldgroup")) {
		$fld = $fld.closest(".control-group");
	}

	$fld.prepend(str);
	return false;

}