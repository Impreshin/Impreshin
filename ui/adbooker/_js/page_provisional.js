/*
 * Date: 2012/03/06 - 2:45 PM
 */
var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");
$(document).ready(function () {

	var highlight = $.bbq.getState("highlight");
	highlight = (highlight) ? highlight : "checked";
	var filter = $.bbq.getState("filter");
	filter = (filter) ? filter : "*";

	if ($.bbq.getState("modal")=="settings"){
		$("#settings-modal").modal('show');
	}

	if ($.bbq.getState("highlight")) {
		$("#list-highlight-btns button[data-highlight].active").removeClass("active");
		$("#list-highlight-btns button[data-highlight='"+ highlight+"']").addClass("active");
	}
	if ($.bbq.getState("filter")) {
		$("#list-filter-btns button[data-filter].active").removeClass("active");
		$("#list-filter-btns button[data-filter='"+ filter+"']").addClass("active");
	}


	if ($.bbq.getState("groupBy")){
		$("#record-settings li[data-group-records-by].active").removeClass("active");
		$("#record-settings li[data-group-records-by='"+ $.bbq.getState("groupBy")+"']").addClass("active");
	}
	if ($.bbq.getState("orderBy")) {
		$("#record-settings li[data-order-records-by].active").removeClass("active");
		$("#record-settings li[data-order-records-by='" + $.bbq.getState("orderBy") + "']").addClass("active");
	}
	getList();
//$("#whole-area .loadingmask").show();
	$("#pageheader li ul a").click(function () {
		$("#pagecontent").css({"opacity":0.5});
		$("#pageheader li.active").removeClass("active");
		$(this).closest(".nav > li").addClass("active");
		$(this).closest("li").addClass("active");
	});
	$('#details-modal').bind("shown", function () {
		var tab = $.bbq.getState("details-tab"), $details_modal = $('#details-modal');
		if (!tab) tab = "details-pane-details";
		$('.nav-tabs li a[href="#' + tab + '"]', $details_modal).parent().addClass("active");
		$('#' + tab + '', $details_modal).addClass("active");
		$("#details-modal .tab-pane.active .scroll-pane").jScrollPane(jScrollPaneOptions);

	});

	$(document).on("click", "#record-settings li[data-group-records-by]", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-settings li[data-group-records-by].active").removeClass("active");
		$this.addClass("active");
		$.bbq.pushState({"groupBy":$this.attr("data-group-records-by")});
		getList();

	});
	$(document).on("click", "#record-settings li[data-order-records-by]", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-settings li[data-order-records-by].active").removeClass("active");
		$this.addClass("active");
		$.bbq.pushState({"orderBy":$this.attr("data-order-records-by")});
		getList();

	});
	$(document).on("click", "#list-settings", function (e) {
		e.preventDefault();
		var $this = $(this);

		$this.addClass("active");
		$.bbq.pushState({"modal":"settings"});
		$("#settings-modal").modal('show');

	});
	$(document).on('hide', '#settings-modal', function () {
		$.bbq.removeState("modal");
		$("#list-settings").removeClass("active");
	});
	$(document).on('shown', '#settings-modal', function () {
		$("#settings-modal .modal-body .scroll-pane").jScrollPane(jScrollPaneOptions);

		if ($.bbq.getState("groupBy")) {
			$("#settings-records-group button[data-group-records-by].active").removeClass("active");
			$("#settings-records-group button[data-group-records-by='" + $.bbq.getState("groupBy") + "']").addClass("active");
		}
		if ($.bbq.getState("orderBy")) {
			$("#settings-records-order button[data-order-records-by].active").removeClass("active");
			$("#settings-records-order button[data-order-records-by='" + $.bbq.getState("orderBy") + "']").addClass("active");
		}

	});
	$(document).on("click", "#list-highlight-btns button, #list-filter-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		var highlight = $("#list-highlight-btns button.active").attr("data-highlight");
		highlight = (highlight) ? highlight : "checked";
		var filter = $("#list-filter-btns button.active").attr("data-filter");
		filter = (filter) ? filter : "*";


		$.bbq.pushState({"highlight":highlight,"filter":filter});
		getList();

	});



	$(document).on('click', '.scrolllinks a', function (e) {
		e.preventDefault();
		var $this = $(this), scrollto = $this.attr("rel");

		api.scrollToElement("[data-heading='" + scrollto + "']", true, true);

	});

	$(document).on("click","#toolbar-stats-link", function (e) {
		if (!$(e.target).closest("#toolbar-stats-pane").get(0)) {
			$("#toolbar-stats-pane").slideToggle(transSpeed);
		}

	});
	$(document).on("reset", "#settings-modal form", function (e) {
		e.preventDefault();
		if (confirm("Are you sure you want to reset all these settings?")){
			$("#settings-modal").addClass("loading");
			$.post("/ab/save/list_settings?reset=columns,group,order", function () {
				$.bbq.removeState("orderBy","groupBy");
				window.location.reload();
			});
		}


	});
	$(document).on("submit", "#settings-modal form", function (e) {
		e.preventDefault();
		var $this = $(this);

		var columns = [];
		$("#selected-columns div").each(function(){
			var $thisC = $(this);

			columns.push($thisC.attr("data-column"));

		});
		columns = columns.join(",");
		var group = $("#settings-records-group button.active").attr("data-group-records-by");
		var order = $("#settings-records-order button.active").attr("data-order-records-by");
		//console.log(columns);

		$("#settings-modal").addClass("loading");
		$.post("/ab/save/list_settings",{"columns":columns,"group":group,"order":order},function(){
			$("#settings-modal").removeClass("loading");
			if (confirm("Settings Saved\n\nReload new settings now?")){
				$.bbq.removeState("modal");
				$.bbq.pushState({groupBy: group,orderBy: order});
				window.location.reload();
			}
		});


	});

	$("#selected-columns, #available-columns").sortable({
		connectWith:".connectedSortable",
		containment:".scroll-pane",
		update:function (event, ui) {
			$(this).closest(".scroll-pane").jScrollPane(jScrollPaneOptionsMP);

		}
	}).disableSelection();
	//

});

function getList() {
	var ID = $.bbq.getState("ID");
	var group = $.bbq.getState("groupBy");
	group = (group)? group:"";
	var order = $.bbq.getState("orderBy");
	order = (order)? order:"";

	var highlight = $("#list-highlight-btns button.active").attr("data-highlight");
	highlight = (highlight)? highlight: "";
	var filter = $("#list-filter-btns button.active").attr("data-filter");
	filter = (filter)? filter: "";


	$("#whole-area .loadingmask").show();
	listRequest.push($.getJSON("/ab/data/bookings",{"group": group,"order":order, "highlight": highlight, "filter": filter},function(data){
		data = data['data'];




		var $recordsList = $("#record-list");
		if (data['list'][0]){
			$recordsList.jqotesub($("#template-records"), data['list']);
		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
		}

		$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);

		api.reinitialise();



		if (ID) {
			$("#record-list .record.active").removeClass("active");
			$("#record-list .record[data-ID='" + ID + "']").addClass("active");
			getDetails();
		}
		var goto = $.bbq.getState("scrollTo");
		if (goto) {
			if ($("#record-list .record[data-ID='" + goto + "']").length) {
				console.log("scroll: " + goto);
				console.log(api);
				api.scrollToElement("#record-list .record[data-ID='" + goto + "']", false, true);

			}

			//$.bbq.removeState("scrollTo");
		} else {

		}

		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}));


}
