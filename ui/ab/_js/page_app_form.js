/*
 * Date: 2013/02/19 - 2:31 PM
 */
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {
	getData(detailsID);

	$(document).on("click", ".view-record-btn", function () {
		var id = $(this).attr("data-id");
		if (id) {
			$(this).closest(".modal").modal("hide");
			$.bbq.pushState({"ID": id});

			getDetails();
		}

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

		show_checkhox();
		resizeform($("form"));
	});



});

function getData(ID){

	$("#left-area .loadingmask").show();

	$("#whole-area .loadingmask").show();
	for (var i = 0; i < detailsRequest.length; i++) detailsRequest[i].abort();
	detailsRequest.push($.getJSON("/ab/data/form/_details", {"ID": ID}, function (data) {
		data = data['data'];
		var title = "";
		if (data['details']['ID']){
			title = "Edit Record";
		} else {
			title = "New Record";
		}
		document.title = "AB - Form - " + title;

		var toolbar = {
			"heading": title,
			"types":data['types'],
			"data":data
		};

		$("#scroll-container").jqotesub($("#template-form"), data);
		$("#maintoolbar").jqotesub($("#template-toolbar"), toolbar);


		dropdowns(data);
		resizeform();
		$("#whole-area .loadingmask").fadeOut(transSpeed);

	}));

}
function dropdowns(data){
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
}
function resizeform() {

	var pane = $(".form-body").jScrollPane(jScrollPaneOptions);
	var api = pane.data("jsp");
	//api.reinitialise();

	scrolling(api);
}

function show_checkhox() {
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