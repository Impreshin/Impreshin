var text_toolbar = [
	{ name: 'basicstyles', groups: [ 'basicstyles' ], items: [ 'Bold', 'Italic', 'Underline', , 'Subscript', 'Superscript' ] },
	{ name: 'tools', items: [ 'Find','Replace' ] },
	{ name: 'tools', items: [ 'Source', 'ShowBlocks' ] },
	{ name: 'tools', items: [ 'SpecialChar' ] },
	{ name: 'tools', items: [ 'Maximize' ] },
	{ name: 'spellcheck', items: [ 'jQuerySpellChecker' ]}

];
var caption_settings = {
	uiColor           : '#FFFFFF',
	height            : '310px',
	toolbar           : text_toolbar,
	removePlugins     : 'elementspath',
	resize_enabled    : false,
	
};



var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {
	getData();

	$(document).on("click", ".view-record-btn", function () {
		var id = $(this).attr("data-id");
		if (id) {
			$(this).closest(".modal").modal("hide");
			$.bbq.pushState({"ID": id});

			getDetails();
		}

	});

	$(document).on("shown", "#account-search-modal", function () {
		$("#account-search-box").focus();
	});

	$(document).on("click", "#account-search", function () {
		var $this = $(this);

		$("#account-search-modal").modal("show");

		account_search();

	});

	$(document).on("submit", "#account-search-form", function(e){
		e.preventDefault();
		account_search();
		return false;
	});

	$(document).on("click", "#account-search-modal .record", function () {
		var $this = $(this);

		//<option value="" data-accNum="" data-account="" data-blocked="" data-labelClass="" data-remark="">accNum | account</option>

		$accountID = $("#accountID");

		if ($("option[value='"+ $this.attr("data-id")+"']", $accountID).length ==0){
			$accountID.append('<option value="' + $this.attr("data-id") + '" data-accNum="' + $this.attr("data-accNum") + '" data-account="' + $this.attr("data-account") + '" data-blocked="' + $this.attr("data-blocked") + '" data-labelClass="' + $this.attr("data-labelclass") + '" data-remark="' + $this.attr("data-remark") + '">' + $this.attr("data-accNum") + ' | ' + $this.attr("data-account") + '</option>')
		}



		$accountID.val($this.attr("data-id")).trigger("change");

		$("#account-search-modal").modal("hide");



	});



	$(document).on("click", "#booking-type button", function () {
		var type = $(this).attr("data-type");
		$.bbq.pushState({"type": type});
		$("#form-diff > article").hide();
		$("#form-diff-" + type).show();
		display_notes();
	});

	$(document).on("change", "#placingID", function () {
		sub_placing_fn();
		colours_fn();
		resizeform();

	});
	$(document).on("change", "form input, form select", function () {

		if (("form .fielderror").length) {
			$("form .fielderror").remove();
			resizeform();
		}

	});
	$(document).on("shown", "#suggestion-tabs", function () {
		resizeform();
	});
	
	$(document).on("keyup", "#classifiedText", 
		$.debounce(250, function() {
			 display_notes();
		}));


	$(document).on("change", "#sub_placingID", function () {
		var $this = $(this);
		var ID = $this.val(), placingID = $this.find("option:selected").attr("data-placingID");
		$("#placingID").find("option[value='" + placingID + "']").attr("data-sub-selected", ID);
		colours_fn();
		resizeform();

	});
	$(document).on("click", "*[data-fld]", function () {
		var $this = $(this), fld = $this.attr("data-fld"), val = $this.attr("data-val");

		if (fld == "accNum") {
			$("#all_accounts *[data-accNum='" + val + "']").trigger("click");
		} else {
			$("#" + fld).val(val).trigger("change");
		}

	});

	$(document).on("change", "#accountID", function () {
		account_note();
		account_lookup_history_suggestions();

	});
	$(document).on("click", ".dates-btn", function () {
		var $this = $(".dates-btn"), $otherdates = $("#dates_list .otherdates"), $dates_list = $("#dates_list");
		if ($dates_list.hasClass("showit")) {
			$dates_list.removeClass("showit");
			$this.html("More");
		} else {
			$dates_list.addClass("showit");
			$this.html("Less");
		}

		show_checkhox_fn();
		resizeform($("form"));
	});
	$(document).on("change", ".display_notes", function () {
		display_notes();
	});
	$(document).on("submit", "#booking-form", function (e) {
		e.preventDefault();
		form_submit();
		return false;

	});
	$(document).on("reset", "#booking-form", function (e) {
		e.preventDefault();

		getData();
		return false;

	});

	$(document).on("submit", "#modal-delete form", function (e) {
		e.preventDefault();

		var data = $(this).serialize();

		if ($("#delete_reason").val() == "") {
			alert("You must specify a reason");
		} else {
			$("input", $(this)).attr("disabled", "disabled");
			$.post("/app/ab/save/bookings/booking_delete/?ID=" + var_record_ID, data, function (r) {
				alert("Booking deleted");
				document.location = "/app/ab/";
			});

		}

	});

});
function account_search() {
	var $body = $("#account-search-modal tbody").html("");
	var search = $("#account-search-box").val();
	var $count = $("#accounts-search-results-count").html("");

	var $loadingmask = $("#account-search-modal .loadingmask").show();
	if (search){

		$.getData("/app/ab/data/form/_accounts", {"search": search}, function (data) {


			$count.html(data['count'] + " Record(s) found");
			$body.jqotesub($("#template-modal-account-search-tr"), data['records']);
			$loadingmask.hide();

		});
	} else {
		$loadingmask.hide();
		$body.html('<tr><td colspan="3">Use the search box to search for a record<td></tr>')
	}


	//




}

function getData() {
	var ID = var_record_ID;

	$("#left-area .loadingmask").show();

	$("#whole-area .loadingmask").show();
	$.getData("/app/ab/data/form/_details", {"ID": ID}, function (data) {

		var title = "";
		if (data['details']['ID']) {
			if (data['details']['deleted'] == '1') {
				title = "Edit Deleted Record";
			} else {
				title = "Edit Record";
			}

		} else {
			title = "New Record";
		}
		document.title = "AB - Form - " + title;

		var toolbar = {
			"heading": title,
			"data"   : data
		};

		$("#scroll-container").jqotesub($("#template-form"), data);
		$("#maintoolbar").jqotesub($("#template-toolbar"), toolbar);
		$("#form-diff > article").hide();
		var type = data['settings']['type'];
		if ($.bbq.getState("type")) {
			type = $.bbq.getState("type");
		}
		var $bookingTypeBtns = $("#booking-type");
		$bookingTypeBtns.find("button[data-type='" + type + "']").trigger("click");
		//$("#form-diff-"+ type).show();

		var instance = CKEDITOR.replace("classifiedText", caption_settings).on('change', 
			$.debounce(250, function() {
				
				display_notes();
				
			})
		);
		set_upload_classified_media(data)
		dropdowns_fn(data);
		sub_placing_fn(data['details']['sub_placingID']);
		colours_fn();
		display_notes();
		account_note();
		account_lookup_history_suggestions();
		//resizeform();
		//classifiedMaths();
		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}, "data");

}
function dropdowns_fn(data) {
	$('#client').typeahead({
		"source": data['clients']
	});
	$("#accountID").select2({
		formatResult   : function (result, query, markup) {
			var $el = $(result.element);
			var $return = "";
			if ($el.attr("data-accNum")) {

				if ($el.attr("data-labelClass")) {
					$return = "<div class='accnum_in_list'><span class='label " + $el.attr("data-labelClass") + "'>" + $el.attr("data-accNum") + "<span></div>";
				} else {
					$return = "<div class='accnum_in_list'>" + $el.attr("data-accNum") + "</div>";
				}
				if ($el.attr("data-blocked") == '1') {
					$return += "<span class='label label-important blocked'>Blocked</span><span class='g'>" + $el.attr("data-account") + "</span>";
				} else {
					$return += $el.attr("data-account");
				}
			}

			return $return;
		},
		formatSelection: function (result) {

			return result.text;
		}


	});
	$("#marketerID").select2({});
	$("#categoryID").select2({});
	$("#insertTypeID").select2({});
	$("#classifiedTypeID").select2({});
	$("#placingID").select2({});
	$("#colourID").select2({});
	$("#payment_methodID").select2({});
	
	
	
}
function resizeform() {

	var pane = $(".form-body").jScrollPane(jScrollPaneOptions);
	var api = pane.data("jsp");
	//api.reinitialise();

	scrolling(api);
}

function show_checkhox_fn() {
	$("#dates_list .otherdates input:checkbox, #dates_list .otherdates input:radio").each(function () {
		var $this = $(this), $label = $this.closest(".otherdates");
		if ($this.is(":checked")) {
			$label.addClass("showit");
		} else {
			$label.removeClass("showit");
		}

	});
	$("#dates_list .otherdates.showit").show();
}
function sub_placing_fn(s) {
	var d = var_sub_placing, $sub_placingID = $("#sub_placingID"), $sub_placing_area = $("#sub_placing_area"), $placingID = $("#placingID");
	var placingID = $placingID.val();
	var selected = "";

	if (s) {
		selected = s;
	} else {
		var placingID_data = $placingID.find("option:selected").attr("data-sub-selected");
		if (placingID_data) selected = placingID_data;
	}

	var html = $.map(d, function (el, index) {
		if (el['placingID'] == placingID) {
			var selected_t = "";
			if (el['ID'] == selected) {
				selected_t = 'selected="selected"';
			}
			return '<option value="' + el['ID'] + '" data-placingID="' + el['placingID'] + '" data-rate="' + el['rate'] + '" data-force-colour="' + el['colourID'] + '" ' + selected_t + '>' + el['label'] + '</option>';
		}

	});

	if (html.length) {
		html = html.join("");
		$sub_placingID.html(html);
		$sub_placing_area.show();
		$sub_placingID.select2({});
	} else {
		$sub_placing_area.hide();
	}

}
function colours_fn() {
	var $colour_area = $("#colour_area"), $colourID = $("#colourID"), $placingID = $("#placingID"), $sub_placingID = $("#sub_placingID");
	var forceColour = "";
	var placingID_FC = $placingID.find("option:selected").attr("data-force-colour");
	var sub_placingID_FC = $sub_placingID.find("option:selected").attr("data-force-colour");

	if (placingID_FC) {
		forceColour = placingID_FC;
	}
	if (sub_placingID_FC && $("#sub_placing_area:visible").length) {
		forceColour = sub_placingID_FC;
	}
	$colourID.val(forceColour);
	if (forceColour && forceColour != 0) {
		$colour_area.hide();
	} else {
		$colour_area.show();
	}

}
function imperial2metric($number) {
	// Get rid of whitespace on both ends of the string.
	$number = ($number.trim());

	// This results in the number of feet getting multiplied by 12 when eval'd
	// which converts them to inches.
	$number = $number.replace("'", '*12');

	// We don't need the double quote.
	$number = $number.replace('"', '');

	// Convert other whitespace into a plus sign.
	$number = $number.replace(' ', '+');

	// Make sure they aren't making us eval() evil PHP code.
	if ($number.match('/[^0-9\/\.\+\*\-]/')) {
		return false;
	} else {
		
		//console.log($number)
		if (!$number){
			return "";
		}
		//console.info($number)
		// Evaluate the expression we've built to get the number of inches.
		$inches = eval("("+$number+")");

		//console.log($inches)
		
		// This is how you convert inches to meters according to Google calculator.
		//$meters = $inches * 0.0254;

		// Returns it in meters. You may then convert to centimeters by
		// multiplying by 100, kilometers by dividing by 1000, etc.
		return $inches;
	}
}
function display_notes() {
	
	var cm = $("#cm").val(), col = $("#col").val();
	
	
	if (_useImperial){
		cm = imperial2metric(cm);
	} else {
		cm = (cm.replace(/[^0-9\.]/g, ""));
	}
	
	col = col.replace(/[^0-9\.]/g, "");

	$("#cm").val(cm);
	$("#col").val(col);
	
	//console.log(cm)

	var discount = $("#discount").val(), agencyDiscount = $("#agencyDiscount").val(), InsertPO = $("#InsertPO").attr("placeholder", var_publication['printOrder']).val();
	InsertPO = (InsertPO) ? InsertPO : var_publication['printOrder'];

	var col_cm = "";
	if (col && cm) {

		$("#size-msg strong").html(col * cm);
		col_cm = cm * col;
	}

	var type = $("#booking-type button.active").attr("data-type");
	var shouldbe = "", shouldbe_e = "", exact_rate = "", string = "", msgtext = "";

	switch (type) {
		case "1":
			var rate = ($("#rate").val());

			var placingID_Rate = $("#placingID").find("option:selected").attr("data-rate");
			var sub_placingID_Rate = $("#sub_placingID").find("option:selected").attr("data-rate");

			if (placingID_Rate) {
				exact_rate = placingID_Rate;
			}
			if (sub_placingID_Rate && $("#sub_placing_area:visible").length) {
				exact_rate = sub_placingID_Rate;
			}

			if (!rate) {
				rate = exact_rate
			}
			rate = Number(rate).toFixed(2);
			exact_rate = Number(exact_rate).toFixed(2);
			shouldbe = (col_cm) * rate;
			shouldbe_e = (col_cm) * exact_rate;

			break;
		case "2":

			rate = $("#rate").val();
			exact_rate = $("#insertTypeID option:selected").attr("data-rate") || var_publication['InsertRate'];
			if (!rate) {
				rate = exact_rate
			}

			exact_rate = Number(exact_rate).toFixed(2);
			shouldbe = (InsertPO) * (rate / 1000);
			shouldbe_e = (InsertPO) * (exact_rate / 1000);

			break;
		case "3":
			classifiedMaths()
			rate = $("#rate").val();
			var $ty = $("#classifiedTypeID option:selected");
			exact_rate = $ty.attr("data-rate");
			if (!rate) {
				rate = exact_rate
			}

			exact_rate = Number(exact_rate).toFixed(2);
			var area = document.getElementById('classifiedText');
			
			
			switch ($ty.attr("data-type")){
				case "1":
					Countable.once(area, function (counter) {
						shouldbe = (counter.words) * (rate);
						shouldbe_e = (counter.words) * (exact_rate);
					})
					break;
				case "2":
					Countable.once(area, function (counter) {
						shouldbe = (counter.character) * (rate);
						shouldbe_e = (counter.character) * (exact_rate);
					})
					break;
				case "3":
					shouldbe = (1) * (rate);
					shouldbe_e = (1) * (exact_rate);
					break;
				
			}
			
			
			break;
	}
	exact_rate = Number(exact_rate).toFixed(2);
	if (rate) {

		rate = Number(rate);
		rate = rate.toFixed(2);
		//$("#rate").val(rate);
		var dif = exact_rate - rate;
		if (dif > 0) {
			msgtext = "Under: " + (exact_rate - rate).toFixed(2);
			string = '<span class="label label-warning">' + msgtext + '</span>';
		} else if (dif < 0) {
			msgtext = "Over: " + (rate - exact_rate).toFixed(2);
			string = '<span class="label label-info">' + msgtext + '</span>';
		}

	}
	string = '<span class="badge" data-fld="rate" data-val="' + exact_rate + '">' + exact_rate + '</span>' + string;
	$("#rate-msg").html(string);
	$("#rate").attr("placeholder", exact_rate).blur();

	$("#rate_fld").val(rate);

	if (discount) {
		discount = Number(discount.replace(/[^0-9\.]/g, "")).toFixed(2);
		$("#discount").val(discount);
	}
	if (agencyDiscount) {
		agencyDiscount = Number(agencyDiscount.replace(/[^0-9\.]/g, "")).toFixed(2);
		$("#agencyDiscount").val(agencyDiscount);
	}

	if (agencyDiscount && agencyDiscount != "0.00") shouldbe = shouldbe - (shouldbe * (agencyDiscount / 100));
	if (discount && discount != "0.00") shouldbe = shouldbe - (shouldbe * (discount / 100));

	var string = "";

	if (shouldbe>=0) {
		shouldbe = shouldbe.toFixed(2);
		$("#totalCost").attr("placeholder", shouldbe).blur();
		string = '<span class="badge" data-fld="totalCost" data-val="' + shouldbe + '">' + shouldbe + '</span>' + string;

	}
	if (shouldbe_e>=0) {
		shouldbe_e = shouldbe_e.toFixed(2);

	}

	var totalcost = $("#totalCost").val(), diff = "";
	if (totalcost) {
		totalcost = Number(totalcost.replace(/[^0-9\.]/g, "")).toFixed(2);
		$("#totalCost").val(totalcost);

		dif = shouldbe_e - totalcost;

		if (dif > 0) {
			msgtext = "Under: " + (shouldbe_e - totalcost).toFixed(2);
			string += '<span class="label label-warning">' + msgtext + '</span>';
		} else if (dif < 0) {
			msgtext = "Over: " + (totalcost - shouldbe_e).toFixed(2);
			string += '<span class="label label-info">' + msgtext + '</span>';
		}

	} else {

		dif = shouldbe_e - shouldbe;

		if (dif > 0) {
			msgtext = "Under: " + (shouldbe_e - shouldbe).toFixed(2);
			string += '<span class="label label-warning">' + msgtext + '</span>';
		} else if (dif < 0) {
			msgtext = "Over: " + (shouldbe - shouldbe_e).toFixed(2);
			string += '<span class="label label-info">' + msgtext + '</span>';
		}
	}
	if (shouldbe) shouldbe = Number(shouldbe).toFixed(2);
	if (shouldbe_e) shouldbe_e = Number(shouldbe_e).toFixed(2);

	$("#totalShouldbe").val(shouldbe);
	$("#totalShouldbe_e").val(shouldbe_e);

	$("#totalCost-msg").html(string);

	var $payment_method_note = $("#payment_method_note");
	if ($("#payment_methodID").val() == '1') {
		$payment_method_note.hide();
	} else {
		$payment_method_note.show();
	}

	resizeform();

}
function error_msg($fld, msg) {
	var str = '<div class="alert fielderror alert-error">' + msg + '</div>';
	if (!$fld.hasClass("control-group") && !$fld.hasClass("fieldgroup")) {
		$fld = $fld.closest(".control-group");
	}

	$fld.prepend(str);
	return false;

}

function form_submit() {
	$form = $("#booking-form");
	$(".fielderror", $form).remove();

	var available_dates = $.map($("#dates_list input:checkbox"), function (i) {
		return $(i).val();
	});
	available_dates = available_dates.join(",");

	var details = $form.data("details");
	var type = $("#booking-type button.active").attr("data-type");

	if (!type) {
		alert("Something went wrong, please select a booking type");
		return false;
	}

	var submit = true;
	if (!$("#dates_list input:checked").length) {
		submit = error_msg($("#dates_list"), "No date selected");
	}
	var $fld = "";
	$fld = $("#client");
	if (!$fld.val()) {
		submit = error_msg($fld, "<strong>Client</strong> is Required");
	}

	$fld = $("#accountID");
	if (!$fld.val()) {
		submit = error_msg($fld, "<strong>Account Number</strong> is Required");
	}
	$account = "";

	if ($("option:selected", $fld).attr("data-blocked") == '1') {
		submit = error_msg($fld, "<strong>Account</strong> is Blocked");
	}

	var cm = $("#cm").val();
	var col = $("#col").val();
	if (type == '1' && (cm == '' || col == '')) {
		var msg = "";
		if (cm == '') msg = "<strong>Cm</strong> " + msg;
		if (col == '') msg = "<strong>Columns</strong> " + msg;

		msg = msg + " is Required";
		submit = error_msg($("#sizearea"), msg);
	}

	resizeform();

	if (submit) {
		$("#pagecontent .loadingmask").show();
		var data = $form.serialize();
		var var_detailsID = var_record_ID;
		$.post("/app/ab/save/bookings/form?ID=" + var_detailsID + "&type=" + type, data, function (response) {

			//$("#record-ID").val(response[0]['ID']);
			getData();
			$("#pagecontent .loadingmask").fadeOut(transSpeed);
			$("#modal-form").jqotesub($("#template-modal-form"), response[0]).modal("show");
		});
	}

}
function account_note() {
	var $this = $("#accountID");
	var $account = $("#accountID option:selected");
	var $select = $this.data("select2");

	var $opt = $("option:selected", $this);

	var alertclass = "", alertText = "";
	if ($opt.attr("data-blocked") == '1') {
		$($select.container).addClass("select-error");
		alertclass = "alert-error";
		alertText = "Account Blocked!"
	} else {
		$($select.container).removeClass("select-error");
	}

	$("#account_remark").html("");
	if ($opt.attr("data-remark")) {
		$("#account_remark").html('<div class="alert ' + alertclass + '"><strong>' + alertText + '</strong> ' + $opt.attr("data-remark") + '</div>');
	}

}
function account_lookup_history_suggestions() {
	var type = $("#booking-type button.active").attr("data-type");
	var accNum = $("#accountID").val();
	$suggestions = $("#suggestion-area").stop(true, true).fadeOut();

	$.getData("/app/ab/data/form/account_lookup_history_suggestions", {"accNum": accNum, "limit": "4", "type": type}, function (data) {

		if (accNum) {
			$suggestions.jqotesub($("#template-suggestions"), data).stop(true, true).fadeIn();
		} else {
			$suggestions.jqotesub($("#template-suggestions-accounts"), data).stop(true, true).fadeIn();
		}
		resizeform();

	}, "suggestions");

}
function classifiedMaths(){
	var $field = $("#classifiedText") 
	var val = $field.val();

	for(var instanceName in CKEDITOR.instances)
		CKEDITOR.instances[instanceName].updateElement();
	
	
	
	var area = document.getElementById('classifiedText')
	Countable.once(area, function (counter) {
		$("#classifiedWords").val(counter.words);
		$("#classifiedCharacters").val(counter.characters);
		$("#classifiedStats").html(counter.words + " Words | " + counter.characters + " Characters");
	})
	
	
	
	//console.log($field.val())
}
function set_upload_classified_media(data) {
	
	var dspl = data.details.datein.split("-");
	var ny = dspl[0];
	var nm = dspl[1];

	var d = new Date();
	
	
	if (!ny)ny = d.getFullYear();
	if (!nm)nm = ("0"+d.getMonth()).slice(-2);
	
	
//console.log(data)
	var folder = data['details']['cID'] + "/" + data['details']['pID'] + "/classifieds/" +ny + "/"+nm+"/";
	
	
//console.log(folder)
	//return false;
	var uploader = new plupload.Uploader({
		runtimes      :'html5,gears,flash,silverlight',
		browse_button :'media-file-area-filename',
//container          :'container',
		max_file_size :'200mb',
		max_file_count:1,
		chunk_size    :"2MB",
		url           :'/app/ab/upload/?folder=' + folder,

		flash_swf_url      :'/ui/plupload/js/plupload.flash.swf',
		silverlight_xap_url:'/ui/plupload/js/plupload.silverlight.xap',
		filters            :[
//{title:"Image files", extensions:"jpg,gif,png"},
//{title:"Zip files", extensions:"zip"}
		],
		unique_names       :true
	});

	if ($("#media-file-area-filename").data("uploader")) {
		uploader.refresh();
	} else {
		$("#media-file-area-filename").data("uploader", true);

		uploader.bind('Init', function (up, params) {

		});

		uploader.bind('FilesAdded', function (up, files) {
			

			var fileCount = up.files.length, i = 0, ids = $.map(up.files, function (item) {
				return item.id;
			});

			for (i = 0; i < fileCount; i++) {
				uploader.removeFile(uploader.getFile(ids[i]));
			}

			i = 0;
		//	$('#material-file-area-filename').html('<div id="' + files[i].id + '" class="g">Uploading: ' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ')</div>');

			setTimeout(function () {
				$("#media-file-area .progress").fadeIn();
				uploader.start();
			}, 100);

		});

		uploader.bind('UploadProgress', function (up, file) {
			$("#media-file-area .progress .bar").css("width", file.percent + "%");
		});

		uploader.bind('UploadComplete', function (up, files) {
			var file = files[0];

			var $material_file_area_filename = $('#media-file-area-filename');
			
			$("#media-msg-box").html("file uploaded successfully");

			$("#classifiedMediaName").val(file.name);
			$("#classifiedMedia").val(file.target_name);
			
			$("#classifiedMediaNameDisplay").html(file.name);

			$("#media-file-area .progress").fadeOut(500, function () {
				$("#media-file-area .progress .bar").css("width", "0%");
			});
		});

		uploader.init();
	}
}