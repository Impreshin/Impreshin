/*
 * Date: 2012/05/30 - 8:37 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {

	if ($.bbq.getState("details")){
		getDetails_right();
	}

	scrolling(left_pane);

	$(document).on("scroll", "#left-area .scroll-pane",function(){
		visible_pages();
	});

	$(document).on("change","#placingID",function(){
		getList();

	});

	$(document).on("click","#reload-btn",function(){
		getList();
		load_pages();
	});








	$(document).on("click",".page_section",function(){
		var $this = $(this);
		$.bbq.removeState("details");
		$.bbq.pushState({details:"section-"+$this.attr("data-id")});
		getDetails_right();
	});
	$(document).on("click",".pagenr",function(){
		var $this = $(this);
		$.bbq.removeState("details");
		$.bbq.pushState({details:"page-"+$this.attr("data-page")});
		getDetails_right();
	});

	$(document).on("click", ".close-right-over", function () {
		showList();
	});

	$(document).on("click","#dummy-bottom .page",function(){
		var $this=$(this);
		var page = "#dummy-area .pages[rel='" + $this.attr("data-page_nr") + "']";
		if ($("#dummy-area .pages[rel='" + $this.attr("data-page_nr") + "']").length){
			left_pane.scrollToElement(page, true, true);
		}


	});
	$(document).on("click","#dummy-bottom .page.visible",function(){
		var $this=$(this);

		$.bbq.removeState("details");
		$.bbq.pushState({details:"page-" + $this.attr("data-page_nr")});
		getDetails_right();

	});
	$(document).on("click","#record-details-bottom > article",function(){
		var $this = $(this);
		$.bbq.pushState({"ID":$this.attr("data-id")});
		getDetails();
	});


	$(document).on("click","#record-list .record, .pages .record",function(){
		var $this = $(this);
		getDetails_small($this.attr("data-id"));
	});

	$(document).on('hide', '#details-modal', function () {
		var s = {
			maintain_position:true
		};
		//getList(s);
	});


	$(document).on('submit', '#pages_settings_form', function (e) {
		e.preventDefault();
		var $this = $(this);
		var page = $.bbq.getState("details");
		page = (page.match(/\d+/));
		page = page.join("");

		var section = $("#sectionID",$this).val();
		var colour = $("#colours button.active",$this).attr("data-colour");



		var data = {
			"page":page,
			"sectionID":section,
			"colour":colour
		};

		activityRequest.push($.post("/ab/save/layout/_page", data, function (response) {
			var s = {
				maintain_position: true
			};
			load_pages(s);
		}));

		return false;
	});
	$(document).on('change', '#pages_settings_form select', function () {
		$(this).closest("form").trigger("submit");
	});

	$(document).on('click', '#pages_settings_form button', function () {
		$(this).closest("form").trigger("submit");
	});






	getList();
	load_pages();
});
function dummy_resize(settings){
	$("#dummy-area").css("bottom", $("#dummy-bottom").outerHeight() + 42);
	if (settings && settings.maintain_position) {
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptionsMP);
	} else {
		left_pane.reinitialise();
	}

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


function getList(){
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

			tr_draggable($recordsList);

		} else {
			$recordsList.html('<tr><td class="c no-records">No Records Found</td></tr>')
		}

		$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);


		$("#right-area .loadingmask").fadeOut(transSpeed);
		records_list_resize();
	}));
}

function tr_draggable($parent){
	$("tr.record", $parent).draggable({
			opacity    :0.5,
			helper     :function (e) {
				var $target = $(e.target).closest("tr.record");
				var cm = $target.attr("data-cm");
				var col = $target.attr("data-col");

				var width = colSize * col, offsetX = width / 2;
				var height = cmSize * cm, offsetY = height / 2;
				;

				return '<div class="dragablethingy" style="width: ' + width + 'px; height: ' + height + 'px; margin-left: -' + offsetX + 'px; margin-top: -' + offsetY + 'px;">t</div>'
			},
			cursorAt   :{left:0, top:0},
			containment:false,
			zIndex     :2710,
			appendTo   :'body',
			//snap:true,
			//snapMode:"outer",
			//revert: 'invalid',
			stop       :function (event, ui) {

			},
			revert     :'invalid'
		});
}
function load_pages(settings){
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
		$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);

		tr_draggable($("#pages-area"));
		$("#pages-area .pages").droppable({
			accept     :"tr.record",
			greedy:true,
			tolerance: "pointer",
			over:function (event, ui) {
				var $this = $(this);
				var $page = $(this).parent();
				var $dragged = ui.draggable;
				$this.addClass("pagehover");

			},
			out: function(event, ui){
				$(this).removeClass("pagefull").removeClass("pagehover")
			},
			drop       :function (event, ui) {
				var $this = $(this)
				var $page = $(this).parent()
				var $dragged = $(ui.draggable)

				$this.removeClass("pagefull, pagehover");

				drop($dragged.attr("data-id"),$this.attr("rel"), $dragged);
				console.log("dropped onto:"+ $this.attr("rel"))
				console.log("dragged:"+ $dragged.attr("data-id"))
			}
		});


		$("#left-area .loadingmask").fadeOut(transSpeed);
		dummy_resize(settings);
	}));
}

function visible_pages(){
	var t = new Array();
	$("#dummy-bottom .page.visible").removeClass("visible");
	$("#dummy-area .pages:visible").each(function(){
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
function getDetails_small(ID){
	detailsRequest.push($.getJSON("/ab/data/details/?r=" + Math.random(), {"ID":ID}, function (data) {
		data = data['data'];
		$("#record-list .record.active").removeClass("active");
		$("#record-list .record[data-ID='" + ID + "']").addClass("active");
		$('#record-details-bottom').jqotesub($("#template-details-bottom"), data);

		records_list_resize();
	}));
}
function getDetails_right(){
	var section = $.bbq.getState("details");
	var ID = section;
		ID = ID.replace(/page-/,"");
		ID = ID.replace(/section-/,"");
	var str = "-"+ID;
		section = section.replace(str,"");
	var $rightsideOver = $('#rightsideOver');
	$rightsideOver.html("").stop(true,true).fadeIn(transSpeed);
	$("#right-area .loadingmask").show();



	$.getJSON("/ab/data/layout/_details_"+section+"/?r=" + Math.random(), {"val":ID}, function (data) {
		data = data['data'];


		switch(section){
			case "page":
				$rightsideOver.jqotesub($("#template-right-page"), data);
				break;
			case "section":
				$rightsideOver.jqotesub($("#template-right-section"), data);
				break;
		}

		$(".body", $rightsideOver).css({"top":$(".header", $rightsideOver).outerHeight(),"bottom":$(".footer", $rightsideOver).outerHeight()+42}).jScrollPane(jScrollPaneOptions);

		$("#right-area .loadingmask").fadeOut(transSpeed);
	});

}

function showList(){
	$.bbq.removeState("details");
	$("#rightsideOver").stop(true, true).fadeOut(transSpeed);
	$("#right-area .loadingmask").fadeOut(transSpeed);

}

function drop(ID,page,$dragged){

	listRequest.push($.post("/ab/save/layout/_drop/?ID="+ID, {"page":page}, function (data) {
		data = data['data'];
		$dragged.remove();
		$("#page-" + page).jqotesub($("#template-spreads-page"), data);
		tr_draggable($("#page-" + page));
		console.log("replace: " + page);
	}));






}