/*
 * Date: 2012/05/05 - 1:00 PM
 */
var $form = $("#booking-form").data({
	clients    :var_clients,
	spots      :var_spots,
	colours    :var_colours,
	details    :var_details,
	publication:var_publication
});
var accountLookup = [];



$(document).ready(function(){




	$(document).on("click",".dates-btn",function(){
		var $this = $(".dates-btn"), $otherdates = $("#dates_list .otherdates"), $dates_list = $("#dates_list");
		if ($dates_list.hasClass("showit")){
			$dates_list.removeClass("showit");
			$this.html("More");
		} else {
			$dates_list.addClass("showit");
			$this.html("Less");
		}

		show_checkhox();
		resizeform($("form"));
	});

	$(document).on("click", "*[data-fld]", function () {
		var $this = $(this), fld = $this.attr("data-fld"), val = $this.attr("data-val");

		if (fld=="accNum"){
			$("#all_accounts *[data-accNum='"+val+"']").trigger("click");
		} else {
			$("#" + fld).val(val).trigger("change");
		}


	});
	$(document).on("click", "#booking-type button", function () {
		$.bbq.pushState({"type":$(this).attr("data-type")});
		load_form_diff();
		display_notes();
		account_lookup_history_suggestions();

	});

	$(document).on("click", ".view-record-btn", function () {
		var id = $(this).attr("data-id");
		if (id){
			$(this).closest(".modal").modal("hide");
			$.bbq.pushState({"ID":id});

			getDetails();
		}


	});




	$(document).on("change", ".display_notes", function () {
		display_notes();
	});
	$(document).on("change", "#placingID", function () {
		load_colours();
		display_notes();

	});
	$(document).on("change", "#insertTypeID", function () {
		display_notes();

	});




	$(document).on("click","#all_accounts a",function(e){
		e.preventDefault();
		var $this = $(this);
		$("#all_accounts .active").removeClass("active");
		$this.addClass("active");
		$("#accNum").val($this.attr("data-accNum")).trigger("change");
		var $account = $("#account").removeClass("btn-danger");
		$account.html($this.attr("data-account"));


		if ($this.attr("data-blocked")=='1') {
			$account.addClass("btn-danger");
		}
		$remark = $("#account_remark").html("");
		if ($this.attr("data-remark")) {
			$remark.html($this.attr("data-remark"));
		}
		//account_lookup_history_suggestions($this.attr("data-accNum"));
		submit_state();
	});

	$(document).on("submit", "#booking-form", function (e) {
		e.preventDefault();
		form_submit();
		return false;

	});
	$(document).on("reset", "#booking-form", function (e) {
		e.preventDefault();
		$form.data("details","");
		load_form();
		return false;

	});

	load_form();

	$(document).on("submit", "#modal-delete form", function (e) {
		e.preventDefault();

	var data = $(this).serialize();

		if ($("#delete_reason").val() == ""){
			alert("You must specify a reason");
		} else {
			$("input",$(this)).attr("disabled","disabled");
			$.post("/ab/save/bookings/booking_delete/?ID="+$("#record-ID").val(),data,function(r){
				alert("Booking deleted");
				document.location = "/ab/";
			});


		}



	});

});

function load_form(){

	var details = $form.data("details");
	if (details['ID']){
		$("#booking-heading").html("Edit Booking");
		$("#booking-type button.active").removeClass("active");
		$("#booking-type button[data-type='"+ details['typeID'] +"']").addClass("active");

	} else {
		$("#booking-heading").html("New Booking");
		var type = $.bbq.getState("type");
			type = (type)?type: "";
		if (type){
			$("#booking-type button.active").removeClass("active");
			$("#booking-type button[data-type='" + type + "']").addClass("active");
		}
		$form.data("details")['remarkTypeID'] = '1';

	}




	$form.jqotesub($("#template-form"), details);
	load_form_diff();


	$(".form-body", $form).css({"top":$(".form-header", $form).outerHeight(), "bottom":$(".form-footer", $form).outerHeight()});



	//console.log(clients);
	$("#client").typeahead({
		source:$form.data("clients")
	});
	$("#colourSpot").typeahead({
		source:$form.data("spots")
	});


	$("#whole-area .loadingmask").fadeOut(transSpeed);




	show_checkhox();
	load_colours();
	account_lookup_history_suggestions();
	submit_state();
	display_notes();





	$(document).on("change", "#accountID", function () {
		var $this = $(this);
		var $select = $this.data("select2");

		var $opt = $("option:selected", $this);

		if ($opt.attr("data-blocked") == '1') {
			$($select.container).addClass("select-error");
		} else {
			$($select.container).removeClass("select-error");
		}

		$("#account_remark").html("");
		if ($opt.attr("data-remark")) {
			$("#account_remark").html($opt.attr("data-remark"))
		}
		account_lookup_history_suggestions();

	});





}

function load_form_diff() {
	var type = $("#booking-type button.active").attr("data-type");

	var_details['printOrder'] = $form.data("publication")['printOrder'];
	if ($("#template-form-" + type).length) $("#form-diff").jqotesub($("#template-form-" + type), var_details);

	$("#accountID").select2({
		formatResult   :function (result, query, markup) {
			var $el = $(result.element);
			var $return = "";
			if ($el.attr("data-accNum")){


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
		formatSelection:function (result) {


			return result.text;
		}


	});
	if (type=='1') {
		$("#placingID").select2({});
		load_colours();
	} else if (type=='2'){
		$("#insertTypeID").select2({});
	}
	$("#marketerID").select2({});
	$("#categoryID").select2({});


}
function load_colours(){
	var colours = $form.data("colours");
	var selectedID = $form.data("details")['colourID'];
	var place = $("#placingID").val();

	var str = $.map(colours,function(v, i){
		if (i == place || i == ''){

			var selected = "";
			var item_records = $.map(v['records'],function(v,i){
				if (v['ID']== selectedID) {
					selected = 'selected="selected"';
				} else {
					selected = "";
				}
				return '<option value="'+v['ID']+'" data-colour="'+v['colour']+'" data-rate="'+v['rate']+'" '+selected+'>'+v['label']+'</option>';
			});


			var item = '<optgroup label="'+v['place']+'">';
				item += item_records.join("");
				item += '</optgroup>';
			return item;
		}

	});
	str = str.join("");
	var $colourblock = $("#colourID");
	$colourblock.html("");
	if (str){
		$colourblock.html(str).closest(".control-group").show();
	} else {
		$colourblock.html("").closest(".control-group").hide();
	}


}

function show_checkhox(){
	$("#dates_list .otherdates input:checkbox, #dates_list .otherdates input:radio").each(function(){
		var $this = $(this), $label = $this.closest(".otherdates");
		if ($this.is(":checked")){
			$label.addClass("showit");
		} else {
			$label.removeClass("showit");
		}

	});
	$("#dates_list .otherdates.showit").show();
}
function resizeform(form){

	var pane = $(".form-body", form).jScrollPane(jScrollPaneOptions);
	var api = pane.data("jsp");
	//api.reinitialise();

	scrolling(api);
}

function account_lookup_history_suggestions(){
	var type = $("#booking-type button.active").attr("data-type");
	var accNum = $("#accountID").val();
	$suggestions = $("#suggestion-area").stop(true, true).fadeOut();
	accountLookup.push($.getJSON("/ab/data/form/account_lookup_history_suggestions", {"accNum":accNum, "limit":"4", "type":type}, function (data) {
		data = data['data'];
		if (accNum) {
			$suggestions.jqotesub($("#template-suggestions"), data).stop(true, true).fadeIn();
		} else {
			$suggestions.jqotesub($("#template-suggestions-accounts"), data).stop(true, true).fadeIn();
		}
		resizeform();

	}));








}
function display_notes(){
	var type = $("#booking-type button.active").attr("data-type");
	var $this = $(this), rate = "";
	switch(type){
		case "1":
			rate = $("#placingID option:selected").attr("data-rate");
			break;
		case "2":
			rate = $("#insertTypeID option:selected").attr("data-rate") || $form.data("publication")['InsertRate'];
			break;
	}
	$(".alert",$form).remove();




	var colour = $("#colourID option:selected").attr("data-colour");
	$("#colour").val(colour);
	var $colourSpotarea = $("#colourSpot-area");
	if (colour == "Spot") {
		$colourSpotarea.show();
	} else {
		$colourSpotarea.hide();
	}

	display_notes_rate();
	if (type=="1") display_notes_size();
	display_notes_cost();
}
function display_notes_rate(){
	var $item = $("#rate"), val = $item.val(), string = "", msgtext = "", colour_rate = $("#colourID option:selected").attr("data-rate");

	var type = $("#booking-type button.active").attr("data-type");
	switch (type) {
		case "1":
			shouldbe = (colour_rate) ? colour_rate : $("#placingID option:selected").attr("data-rate");
			break;
		case "2":
			shouldbe = $("#insertTypeID option:selected").attr("data-rate") || $form.data("publication")['InsertRate'];
			break;
	}

	val = val.replace(/[^0-9\.]/g, "");
	shouldbe = Number(shouldbe).toFixed(2);
	if (val){

		val = Number(val);
		val = val.toFixed(2);
		$item.val(val);
		var dif = shouldbe - val;
		if (dif>0){
			msgtext = "Under: "+ (shouldbe - val).toFixed(2);
			string = '<span class="label label-warning">' + msgtext + '</span>';
		} else if (dif<0){
			msgtext = "Over: "+(val-shouldbe).toFixed(2);
			string = '<span class="label label-info">' + msgtext + '</span>';
		}

	}
	string = '<span class="badge" data-fld="rate" data-val="' + shouldbe + '">' + shouldbe + '</span>' + string;
	$("#rate-msg").html(string);
	$("#rate").attr("placeholder", shouldbe).blur();
}
function display_notes_size(){
	var cm = $("#cm").val(), col = $("#col").val();
		cm = cm.replace(/[^0-9\.]/g, "");
		col = col.replace(/[^0-9\.]/g, "");

	$("#cm").val(cm);
	$("#col").val(col);
	if (col && cm){

		$("#size-msg strong").html(col * cm);
	}


}
function display_notes_cost(){
	var
		cm = $("#cm").val(),
		col = $("#col").val(),
		discount = $("#discount").val(),
		agencyDiscount = $("#agencyDiscount").val(),
		colour_rate = $("#colourID option:selected").attr("data-rate");
		InsertPO = $("#InsertPO").val();

	InsertPO = (InsertPO)? InsertPO: $form.data("publication")['printOrder'];

	if (cm) cm = cm.replace(/[^0-9\.]/g, "");
	if (col) col = col.replace(/[^0-9\.]/g, "");


	var col_cm = cm * col;

	var type = $("#booking-type button.active").attr("data-type");
	var shouldbe, shouldbe_e, exact_rate;
	switch (type) {
		case "1":
			rate = ($("#rate").val());
			exact_rate = (colour_rate) ? colour_rate : $("#placingID option:selected").attr("data-rate");
			if (!rate){
				rate = exact_rate
			}
			rate = Number(rate).toFixed(2);
			exact_rate = Number(exact_rate).toFixed(2);
			shouldbe = (col_cm) * rate;
			shouldbe_e = (col_cm) * exact_rate;


			break;
		case "2":

			rate = $("#rate").val() ;
			exact_rate = $("#insertTypeID option:selected").attr("data-rate") || $form.data("publication")['InsertRate'];
			if (!rate) {
				rate = exact_rate
			}

			exact_rate = Number(exact_rate).toFixed(2);
			shouldbe = (InsertPO) * (rate/1000);
			shouldbe_e = (InsertPO) * (exact_rate / 1000);




			break;
	}
	$("#rate_fld").val(rate);





	if (discount){
		discount = Number(discount.replace(/[^0-9\.]/g, "")).toFixed(2);
		$("#discount").val(discount);
	}
	if (agencyDiscount){
		agencyDiscount = Number(agencyDiscount.replace(/[^0-9\.]/g, "")).toFixed(2);
		$("#agencyDiscount").val(agencyDiscount);
	}










		if (agencyDiscount && agencyDiscount != "0.00" ) shouldbe = shouldbe - (shouldbe * (agencyDiscount / 100)) ;
		if (discount && discount != "0.00") shouldbe = shouldbe - (shouldbe * (discount / 100)) ;

	var string="";


	if (shouldbe){
		shouldbe = shouldbe.toFixed(2);
		$("#totalCost").attr("placeholder", shouldbe).blur();
		string = '<span class="badge" data-fld="totalCost" data-val="' + shouldbe + '">' + shouldbe + '</span>' + string;

	}
	if (shouldbe_e){
		shouldbe_e = shouldbe_e.toFixed(2);


	}



	var totalcost = $("#totalCost").val(), diff = "";
	if (totalcost){
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
	resizeform();
}
function submit_state() {
	var submit = false, $submit = $("form button[type='submit']");
	if ($("#account").hasClass("btn-danger")) submit = false;

	if (submit) {
		//$submit.removeAttr("disabled");
	} else {
		//$submit.attr("disabled", "disabled");
	}
}
function form_submit(){
	$(".alert", $form).remove();

	var available_dates = $.map( $("#dates_list input:checkbox"),function(i){
		return $(i).val();
	});
	available_dates = available_dates.join(",");


	var details = $form.data("details");
	var type = $("#booking-type button.active").attr("data-type");

	if (!type){
		alert("somehting went wrong, please select a booking type");
		return false;
	}

	var submit = true;
	if (!$("#dates_list input:checked").length){
		submit = error_msg($("#dates_list"),"No date selected");
	}
	var $fld = "";
	$fld = $("#client");
	if (!$fld.val()){
		submit = error_msg($fld,"<strong>Client</strong>, is Required");
	}

	$fld = $("#accountID");
	if (!$fld.val()){
		submit = error_msg($fld,"<strong>Account Number</strong>, is Required");
	}
	$account = "";


	if ($("option:selected",$fld).attr("data-blocked") == '1'){
		submit = error_msg($fld, "<strong>Account</strong>, is Blocked");
	}

	var cm = $("#cm").val();
	var col = $("#col").val();
	if (type=='1' && (cm=='' || col=='')){
		var msg = "";
		if (cm=='') msg = "<strong>Cm</strong>, "+msg;
		if (col=='') msg = "<strong>Columns</strong>, "+msg;

		msg = msg + " is Required";
		submit = error_msg($("#sizearea"), msg);
	}





	resizeform();

	if (submit){
		$("#pagecontent .loadingmask").show();
		var data = $form.serialize();

		$.post("/ab/save/bookings/form?ID=" + details['ID'] + "&type=" + type, data, function (response) {

			$("#pagecontent .loadingmask").fadeOut(transSpeed);
			$("#modal-form").jqotesub($("#template-modal-form"), response[0]).modal("show");
		});
	}

}
function error_msg($fld,msg){
	var str = '<div class="alert alert-error">'+msg+'</div>';
	if (!$fld.hasClass("control-group") && !$fld.hasClass("fieldgroup")) {
		$fld = $fld.closest(".control-group");
	}

	$fld.prepend(str);
	return false;

}
