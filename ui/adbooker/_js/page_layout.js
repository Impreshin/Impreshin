/*
 * Date: 2012/05/30 - 8:37 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#right-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {

	scrolling(left_pane);

	$(document).on("scroll", "#left-area .scroll-pane",function(){
		visible_pages();
	});

	$(document).on("change","#placingID",function(){
		load_list();

	});

	$(document).on("click","#reload-btn",function(){
		load_list();
		load_pages();
	});

	$(document).on("click","#dummy-bottom .page",function(){
		var $this=$(this);
		var page = "#dummy-area article.pages[rel='" + $this.attr("data-page_nr") + "']";
		if ($("#dummy-area article.pages[rel='" + $this.attr("data-page_nr") + "']").length){
			left_pane.scrollToElement(page, true, true);
		}


	});

	load_list();
	load_pages();
});
function dummy_resize(){
	$("#dummy-area").css("bottom", $("#dummy-bottom").outerHeight() + 42);
	left_pane.reinitialise();
	//visible_pages();

}
function records_list_resize(){
	$("#record-list-middle").css("bottom", $("#record-details-bottom").outerHeight() + 42);
	right_pane.reinitialise();
}
function PadDigits(n, totalDigits) {
	n = n.toString();
	var pd = '';
	if (totalDigits > n.length) {
		for (i = 0; i < (totalDigits - n.length); i++) {
			pd += '&nbsp';
		}
	}
	return  n.toString()+ pd;
}


function load_list(){
	var placingID = $("#placingID").val();

	$("#right-area .loadingmask").show();
	listRequest.push($.getJSON("/ab/data/layout/_list/", {"placingID":placingID}, function (data) {
		data = data['data'];


		var placings = $.map(data['placing'],function(record){
			var selected = "";
			if (data['placingID']==record['ID']) {
				selected = 'selected="selected"';
			} else {
				selected = "";
			}
			var padding = "";


			var recordcount = record['recordCount'];
			if (recordcount=='0'){
				padding = "";
			} else {

				padding = ' (' + record['recordCount'] + ')';
			}
			padding = PadDigits(padding,10);

			return '<option value="'+record['ID']+'" '+selected+'>' + padding  + record['placing'] + '</option>';
		});
		placings = placings.join("");


		$("#placingID").html(placings);


		var $recordsList = $("#record-list tbody");
		if (data['records'][0]){
			$recordsList.jqotesub($("#template-records-list"), data['records']);
		} else {
			$recordsList.html('<tr><td class="c no-records">No Records Found</td></tr>')
		}




		$("#right-area .loadingmask").fadeOut(transSpeed);
		records_list_resize();
	}));
}
function load_pages(){
	var placingID = $("#placingID").val();

	$("#left-area .loadingmask").show();
	listRequest.push($.getJSON("/ab/data/layout/_pages/", {"placingID":placingID}, function (data) {
		data = data['data'];

		var $recordsList = $("#pages-area");
		if (data['spreads'][0]) {
			//console.log(data['spreads']);
			$recordsList.jqotesub($("#template-spreads"), data['spreads']);
			$("#dummy-bottom").jqotesub($("#template-spreads-bottom"), data['spreads']);
		} else {
			$recordsList.html('<tr><td class="c no-records">No Records Found</td></tr>')
		}

		$("#left-area .loadingmask").fadeOut(transSpeed);
		dummy_resize();
	}));
}

function visible_pages(){
	var t = new Array();
	$("#dummy-bottom .page.visible").removeClass("visible");
	$("#dummy-area article.pages:visible").each(function(){
		var $this = $(this);
		if (isScrolledIntoView($this)) {
			$("#dummy-bottom .page[data-page_nr='" + $this.attr("rel")+ "']").addClass("visible");
			t.push($this.attr("rel"));
		}
	});
//	console.log(t);

}
function isScrolledIntoView(elem) {
	var docViewTop = $(window).scrollTop();
	var docViewBottom = docViewTop + $(window).height();

	var elemTop = $(elem).offset().top;
	var elemBottom = elemTop + $(elem).height();

	return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom) && (elemBottom <= docViewBottom) && (elemTop >= docViewTop) );
}