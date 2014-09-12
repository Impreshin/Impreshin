var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");
$(document).ready(function () {

	$("#marketerID").select2();
	$(document).on("change", "#marketerID", function (e) {
		e.preventDefault();
		
		getList();
	});
	
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

	$(document).on("click", ".pagination a", function (e) {
		e.preventDefault();
		var $this = $(this).parent();
		$.bbq.pushState({"page":$this.attr("data-page")});
		getList();
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

	$(document).on('hide', '#ab-details-modal', function () {
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

	$(document).on("click", "#toolbar-stats-link", function (e) {
		if (!$(e.target).closest("#toolbar-stats-pane").get(0)) {
			$("#toolbar-stats-pane").slideToggle(transSpeed);
		}

	});
	$(document).on("reset", "#settings-modal form", function (e) {
		e.preventDefault();
		if (confirm("Are you sure you want to reset all these settings?")) {
			$("#settings-modal").addClass("loading");
			$.post("/app/ab/save/list_settings/?section=marketer_records&reset=columns,group,order", function () {
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
		$.post("/app/ab/save/list_settings/?section=marketer_records", {"columns":columns, "group":group, "groupOrder":order}, function () {
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

	var thisMonth = {
		"startDate":Date.parse('today').moveToFirstDayOfMonth(),
		"endDate"  :Date.parse('today').moveToLastDayOfMonth()
	};
	var prevMonth = {
		"startDate":Date.parse('- 1month').moveToFirstDayOfMonth(),
		"endDate"  :Date.parse('- 1month').moveToLastDayOfMonth()
	};

	//date_range
	var earliestDate = date_range.earliestDate;
	earliestDate = Date.parse(earliestDate);
	var latestDate = date_range.latestDate;
	latestDate = Date.parse(latestDate);

	$('#date-picker').daterangepicker({
		presetRanges     :[
			{heading:'Preset Ranges'},
			{text     :'This Month', dateStart:function () {
				//console.log("this From: " + thisMonth.startDate)
				return thisMonth.startDate;
			}, dateEnd:function () {
				//console.log("this To: " + thisMonth.endDate)
				return thisMonth.endDate;
			} },
			{text     :'Previous Month', dateStart:function () {
				//console.log("prev From: " + prevMonth.startDate)
				return prevMonth.startDate;
			}, dateEnd:function () {
				//console.log("prev To: " + prevMonth.endDate)
				return prevMonth.endDate;
			} },

			{heading:'Selectable Ranges'}
		],
		presetDates      :editions,
		presets          :{
			//specificDate:'Specific Date',
			allDatesAfter:'All Dates After',
			dateRange    :'Date Range'
		},
		posX             :null,
		posY             :null,
		arrows           :false,
		dateFormat       :'yy-mm-dd',
		rangeSplitter    :'to',
		datepickerOptions:{
			changeMonth:true,
			changeYear :true,
			minDate    :earliestDate,
			maxDate    :latestDate
		},
		onOpen           :function () {

		},
		onClose          :function () {

			setTimeout(function () {

				var $form = $("#search-form");
				var val = $("#date-picker").val();
				if (val != $form.attr("data-date-picker")) {
					$form.trigger("submit");
				}

			}, 400);

		}

	});
	$(document).on("submit", "#search-form", function (e) {
		e.preventDefault();

		getList();

		return false;
	});

});

function getList(settings) {

	var marketerID = $("#marketerID").val();
	var dates = $("#date-picker").val();
	$("#search-form").attr("data-date-picker", dates);

	var ID = $.bbq.getState("ID");
	var group = $.bbq.getState("groupBy");
	group = (group) ? group : "";
	var order = $.bbq.getState("order");
	order = (order) ? order : "";
	var groupOrder = $.bbq.getState("orderBy");
	groupOrder = (groupOrder) ? groupOrder : "";

	var page = $.bbq.getState("page");
	page = (page) ? page : "";

	var highlight = $("#list-highlight-btns button.active").attr("data-highlight");
	highlight = (highlight) ? highlight : "";
	var filter = $("#list-filter-btns button.active").attr("data-filter");
	filter = (filter) ? filter : "";

	var orderingactive = (order) ? true : false;

	$("#whole-area .loadingmask").show();

	var $search_stats = $("#search-stats").html("Searching");

	$.getData("/app/ab/data/marketer_records/_list", {"group":group, "groupOrder":groupOrder, "highlight":highlight, "filter":filter, "order":order, "marketerID":marketerID, "dates":dates, "page":page}, function (data) {


		var $recordsList = $("#record-list");
		var $pagenation = $("#pagination");
		var bottomChanges = 0;
		if (data['list'][0]) {
			$recordsList.jqotesub($("#template-records"), data['list']);

			if (data['pagination']['pages'].length > 1) {
				$pagenation.jqotesub($("#template-records-pagination"), data['pagination']).stop(true, true).fadeIn(transSpeed);
				bottomChanges = $pagenation.outerHeight() + bottomChanges;

			} else {
				$pagenation.stop(true, true).fadeOut(transSpeed)
			}

		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
		}

		$search_stats.html("Result:  <strong>" + data['stats']['records'] + "</strong> records");

		

		var order = data['order']['c'];
		$(".order-btn[data-col='" + order + "'] .indicator", $recordsList).show();

		if ($.bbq.getState("ID")) {
			$("#record-list .record.active").removeClass("active");
			$("#record-list .record[data-ID='" + ID + "']").addClass("active");

			var api = $("#whole-area .scroll-pane").data("jsp");
			if ($("#record-list .record[data-ID='" + ID + "']").length) {
				api.scrollToElement("#record-list .record[data-ID='" + ID + "']", false, false);
			}

			if (!$("#ab-details-modal").is(":visible")) {
				getDetails();
			}
		}
		
		

		scrollwindow(orderingactive,settings);

		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}, "data");

}
function scrollwindow(orderingactive,settings){
	var $scrollpane = $("#whole-area .scroll-pane");
	if (orderingactive) {
		$scrollpane.jScrollPane(jScrollPaneOptionsMP);
	} else {
		if (settings && settings.maintain_position) {
			$scrollpane.jScrollPane(jScrollPaneOptionsMP);
		} else {
			$scrollpane.jScrollPane(jScrollPaneOptions);
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
}
