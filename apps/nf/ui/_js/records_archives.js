/*
 * Date: 2012/03/06 - 2:45 PM
 */
var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");
$(document).ready(function () {

	scrolling(api);

	var highlight = $.bbq.getState("highlight");
	highlight = (highlight) ? highlight : "checked";
	var filter = $.bbq.getState("filter");
	filter = (filter) ? filter : "*";

	if ($.bbq.getState("modal") == "settings") {
		$("#settings-modal").modal('show');
	}

	if ($.bbq.getState("highlight")) {
		$("#list-highlight-btns button[data-highlight].active").removeClass("active");
		$("#list-highlight-btns button[data-highlight='" + highlight + "']").addClass("active");
	}
	if ($.bbq.getState("filter")) {
		$("#list-filter-btns button[data-filter].active").removeClass("active");
		$("#list-filter-btns button[data-filter='" + filter + "']").addClass("active");
	}

	if ($.bbq.getState("groupBy")) {
		$("#record-settings li[data-group-records-by].active").removeClass("active");
		$("#record-settings li[data-group-records-by='" + $.bbq.getState("groupBy") + "']").addClass("active");
	}
	if ($.bbq.getState("orderBy")) {
		$("#record-settings li[data-order-records-by].active").removeClass("active");
		$("#record-settings li[data-order-records-by='" + $.bbq.getState("orderBy") + "']").addClass("active");
	}
	getList();
//$("#whole-area .loadingmask").show();



	$(document).on("click", "#reload-btn", function () {
		getList();
	});

	$(document).on("click", "#action-btn", function (e) {

		var ids = $(".record input[type='checkbox']:checked").map(function(i, el) { 
			return $(el).attr("value"); 
		}).get();
		
		if (ids.length){
			if (confirm("Are you sure you want to archive these records?")){
				$("#whole-area .loadingmask").show();
				$.post("/app/nf/save/articles/archive_mass",{"aIDs":ids},function(){
					getList();
				})				
			}
		}
	});
	
	
	
	
	
	$(document).on("click", "#record-settings li[data-group-records-by]", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-settings li[data-group-records-by].active").removeClass("active");
		$this.addClass("active");
		$.bbq.pushState({"groupBy":$this.attr("data-group-records-by")});
		getList();

	});
	$(document).on("click", ".order-btn", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-list .order-btn").removeClass("asc desc");
		//$this.addClass("active");
		$.bbq.pushState({"order":$this.attr("data-col")});
		var s = {
			maintain_position:true
		};
		getList(s);
		$.bbq.removeState("order");

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

	$(document).on('hide', '#nf-details-modal', function () {
		var s = {
			maintain_position:true
		};
		getList(s);
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

		$.bbq.pushState({"highlight":highlight, "filter":filter});
		getList();

	});

	$(document).on('click', '.scrolllinks a', function (e) {
		e.preventDefault();
		var $this = $(this), scrollto = $this.attr("rel");

		api.scrollToElement("[data-heading='" + scrollto + "']", true, true);

	});

	
	$(document).on("reset", "#settings-modal form", function (e) {
		e.preventDefault();
		if (confirm("Are you sure you want to reset all these settings?")) {
			$("#settings-modal").addClass("loading");
			$.post("/app/nf/save/list_settings/?section=records_archives&reset=columns,group,order", function () {
				$.bbq.removeState("orderBy", "groupBy");
				window.location.reload();
			});
		}

	});
	$(document).on("submit", "#settings-modal form", function (e) {
		e.preventDefault();
		var $this = $(this);

		var columns = [];
		$("#selected-columns li").each(function () {
			var $thisC = $(this);

			columns.push($thisC.attr("data-column"));

		});
		columns = columns.join(",");
		var group = $("#settings-records-group button.active").attr("data-group-records-by");
		var order = $("#settings-records-order button.active").attr("data-order-records-by");
		//console.log(columns);

		$("#settings-modal").addClass("loading");
		$.post("/app/nf/save/list_settings/?section=records_archives", {"columns":columns, "group":group, "groupOrder":order}, function () {
			$("#settings-modal").removeClass("loading");
			if (confirm("Settings Saved\n\nReload new settings now?")) {
				$.bbq.removeState("modal");
				$.bbq.pushState({groupBy:group, orderBy:order});
				window.location.reload();
			}
		});

	});

	$("#selected-columns, #available-columns").sortable({
		connectWith:".connectedSortable",
		containment:".scroll-pane",
		zIndex     :99999,
		update     :function (event, ui) {
			$(this).closest(".scroll-pane").jScrollPane(jScrollPaneOptionsMP);

		}
	}).disableSelection();
	//

	

	
	$("#dID").select2();
	$(document).on("change", "#dID", function (e) {
		e.preventDefault();

		getList();

		return false;
	});

});

function getList(settings) {

	var dID = $("#dID").val();

	var ID = $.bbq.getState("ID");
	var group = $.bbq.getState("groupBy");
	group = (group) ? group : "";
	var order = $.bbq.getState("order");
	order = (order) ? order : "";
	var groupOrder = $.bbq.getState("orderBy");
	groupOrder = (groupOrder) ? groupOrder : "";

	

	var highlight = $("#list-highlight-btns button.active").attr("data-highlight");
	highlight = (highlight) ? highlight : "";
	var filter = $("#list-filter-btns button.active").attr("data-filter");
	filter = (filter) ? filter : "";

	var orderingactive = (order) ? true : false;

	$("#whole-area .loadingmask").show();

	var $search_stats = $("#search-stats").html("Searching");

	$.getData("/app/nf/data/records_archives/_list", {"group":group, "groupOrder":groupOrder, "highlight":highlight, "filter":filter, "order":order, "dID":dID}, function (data) {


		var $recordsList = $("#record-list");
		
		var bottomChanges = 0;
		if (data['list'][0]) {
			$recordsList.jqotesub($("#template-records"), data['list']);

			

		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
		}

		$search_stats.html("Result:  <strong>" + data['stats']['records'] + "</strong> records");

		var $scrollpane = $("#whole-area .scroll-pane");

		$scrollpane.css("bottom", bottomChanges);
		if (orderingactive) {
			$scrollpane.jScrollPane(jScrollPaneOptionsMP);
		} else {
			if (settings && settings.maintain_position) {
				$scrollpane.jScrollPane(jScrollPaneOptionsMP);
			} else {
				$scrollpane.jScrollPane(jScrollPaneOptions);
			}

		}

		var order = data['order']['c'];
		$(".order-btn[data-col='" + order + "'] .indicator", $recordsList).show();

		if ($.bbq.getState("ID")) {
			$("#record-list .record.active").removeClass("active");
			$("#record-list .record[data-ID='" + ID + "']").addClass("active");

			var api = $("#whole-area .scroll-pane").data("jsp");
			if ($("#record-list .record[data-ID='" + ID + "']").length) {
				api.scrollToElement("#record-list .record[data-ID='" + ID + "']", false, false);
			}

			if (!$("#nf-details-modal").is(":visible")) {
				getDetails();
			}
		}
		var goto = $.bbq.getState("scrollTo");
		if (goto) {
			if ($("#record-list .record[data-ID='" + goto + "']").length) {
				var api = $scrollpane.data("jsp");
				if ($("#record-list .record[data-ID='" + goto + "']").length && api) {
					api.scrollToElement("#record-list .record[data-ID='" + goto + "']", true, true);
				}

			}
			$.bbq.removeState("scrollTo");
		}

		action_btn_state();

		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}, "data");

}

$(document).on("change",".record input[type='checkbox']",function(){
	action_btn_state();
});
function action_btn_state(){
	var $btn = $("#action-btn").prop("disabled",true);
	if ($(".record input[type='checkbox']:checked").length!=0){
		$btn.prop("disabled",false)
	}
	
	
}
