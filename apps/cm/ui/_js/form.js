
$(document).ready(function () {
	$(document).on("click", ".view-record-btn", function () {
		var id = $(this).attr("data-id");
		if (id) {
			$(this).closest(".modal").modal("hide");
			$.bbq.pushState({"ID": id});

			getDetails();
		}

	});
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

	$(document).on('change','#add-new-contact-field-select',function(e) {
		//$("#add-new-contact-field-value").focus();

	})
	$(document).on('select2-close','#add-new-contact-field-select',function(e) {
		
		setTimeout(function () {
			$("#add-new-contact-field-value").focus();
		}, 200);
		
		

	})
	$(document).on('keydown','#add-new-contact-field-value',function(e){
		var key = e.which;
		//	console.log(key)
		if(key == 13){
			e.preventDefault();
			$("#add-new-contact-field-btn").trigger("click");
			return false;
		}

	});
	
	
	$(document).on("click", "#add-new-contact-field-btn", function () {
		var $this = $(this);
		var $row = $this.closest("table");


		
		var ID = "n"+ ($(".contact-details-block li").length + 1)
		var data = {
			"ID":ID,
			"catID":$row.find("select").val(),
			"value":$row.find("td>input").val(),
			"group":""
		}
		$("#new-contact-details-block-area .contact-details-block").jqoteapp($("#template-contact-item"), data);
		$("#contact-details-cat-"+ID).select2({
			formatResult: format_details_select2,
			formatSelection: format_details_select2,
			escapeMarkup: function(m) { return m; }
		});
		showDragBlocks();
		$row.find("select").select2('open');
		$row.find("td>input").val("").trigger("focus");
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


		var $details_area = $("#contact-details-groups");
		$details_area.jqotesub($("#template-contact-group"), data['details']['details']);
		
		
		
		$("select.details-select").select2({
			formatResult: format_details_select2,
			formatSelection: format_details_select2,
			escapeMarkup: function(m) { return m; }
		});

		sorting()
		$( "#drag-new-contacts-group" ).droppable({
			hoverClass: "ui-state-highlight",
			activeClass: "ui-state-default",
			drop: function( event, ui ) {
				var group = prompt("Please enter a group name", "test");
			
				
				if (group){
					if (!$("fieldset[data-label='"+group+"']").length){
						var $group = $("<fieldset data-label='"+group+"'><legend></legend><ul class='nav contact-details-block'></ul></fieldset>");
						$group.appendTo("#contact-details-groups");
					}

					$group = $("fieldset[data-label='"+group+"']");
					$group.find("legend").text(group);
					
					var $item = $(ui.draggable).detach();
					
					var vals_id = $item.attr("data-id");
					var vals_se = $item.find("select.details-select").select2("destroy").val();
					var vals_in = $item.find("input[name^='contact-details-val']").val();
					
					var html = $item = $item.html();
				//	console.log($item);

					$item = $("<li data-id='"+vals_id+"'>"+html+"</li>");

					$item.appendTo($group.find(".nav"));
					$("#contact-details-cat-"+vals_id).val(vals_se).select2({
						formatResult: format_details_select2,
						formatSelection: format_details_select2,
						escapeMarkup: function(m) { return m; }
					});
					$("#contact-details-val-"+vals_id).val(vals_in);
					//$("#contact-details-gro-"+vals_id).val(group);
					

					
					//.append($item);



					
					sorting();
					sortingCleanup();
					resizeform();
				}
					
			}
		});

		showDragBlocks();
		formLoaded(data);
		resizeform();
		//setTimeout(resizeform, 1000)
		$("#whole-area .loadingmask").fadeOut(transSpeed,function(){}());
	}, "form_data");

}
function showDragBlocks(){
	var $blocks = $(".contact-details-block, #drag-new-contacts-group")
	var l = $("li",$blocks).length;
	if (l){
		$blocks.show();
	} else {
		$blocks.hide();
		
	}
}
function sorting(){
	$("ul.contact-details-block").sortable({
		connectWith: ".contact-details-block",
		placeholder: "ui-state-highlight",
		//helper: "clone",
		revert: 'invalid',
		axis: "y",
		update:function( event, ui ){
			sortingCleanup();
		}
	})
}
function sortingCleanup(){
	$("#contact-details-groups ul.contact-details-block:empty").closest("fieldset").remove();
	$("ul.contact-details-block li").each(function(){
		var $this = $(this);
		$this.find("input[name^='contact-details-gro']").val($this.closest("fieldset").attr("data-label"))
	})
	
	//$("#contact-details-block-area select").select2("destroy").select2();;
	
}

function formLoaded(data) {
	
	var pane = $(".form-body").jScrollPane(jScrollPaneOptionsMP);
	var api = pane.data("jsp");
	var $cm = $("#cm-block");
	var body = "";
	


	//$("select.select2").select2();


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
	
	if ($("#add-new-contact-field-value").val() != ""){
		submit = false;
		//$("#add-new-contact-field-btn").trigger("click");
		var $row = $("#add-new-contact-field-btn").trigger("click").closest("table");
		$row.find("select").select2('close');
		$form.trigger("submit");
		
	}




	if (submit) {
		$("#pagecontent .loadingmask").show();
		var data = $form.serialize();
		$.post("/app/cm/save/form/form?ID=" + var_record_ID + "&type=" + type , data, function (response) {


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
				console.log(response)
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
function format_details_select2(item) {
	if (!item.id) return item.text; // optgroup
	//console.log(item)
	return "<i class='g " + $(item.element).attr("data-icon") + "' style='margin-right:10px'></i>" + item.text;
}