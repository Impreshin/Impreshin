/*
 * Date: 2012/03/06 - 2:45 PM
 */
var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");


$(document).ready(function () {
	$("#stageID").select2();
	scrolling(api);

	var stage = $.bbq.getState("stage");
	stage = (stage) ? stage : "*";
	var filter = $.bbq.getState("filter");
	filter = (filter) ? filter : "*";
	
	var checkbox = $.bbq.getState("check");
	checkbox = (checkbox) ? checkbox : "0";
	
	

	if ($.bbq.getState("modal") == "settings") {
		$("#settings-modal").modal('show');
	}

	if ($.bbq.getState("stage")) {
		$("#list-stage-btns button[data-stage].active").removeClass("active");
		$("#list-stage-btns button[data-stage='" + stage + "']").addClass("active");
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
	if ($.bbq.getState("check")=='1') {
		$("#checkbox-button").addClass("active");
	}
	getList();
//$("#whole-area .loadingmask").show();
	$("#pageheader li ul a").click(function () {
		$("#pagecontent").css({"opacity":0.5});
		$("#pageheader li.active").removeClass("active");
		$(this).closest(".nav > li").addClass("active");
		$(this).closest("li").addClass("active");
	});

	$(document).on('hide', '#nf-details-modal', function () {
		var s = {
			maintain_position:true
		};
		getList(s);
	});

	$("#log").append("starting<br>");
	$(document).on("click", "#record-settings li", function () {
		$("#log").append("clicked " + $(this).attr("data-group-records-by") + "<br>");
	});
	$(document).on("click", "#checkbox-button", function (e) {
		e.preventDefault();
		var $this = $(this);
		var checkbox = '0';
		if ($this.hasClass("active")){
			$this.removeClass("active");
			checkbox = '0'
		} else {
			$this.addClass("active");
			checkbox = '1';
		}
		
		$.bbq.pushState({"check":checkbox});
		var s = {
			maintain_position:true
		};
		getList(s);

	});
	


	$(document).on('submit', '.checkbox-menu form', function (e) {
		e.preventDefault();
		var $this = $(this);
		var section = $this.attr("data-section");
		var msg = "";
		var data = $this.serializeArray();
		//data = data?data:[];
		
		var ids = $("#record-list .checkbox-record input:checkbox:checked").map(function(){
			return $(this).val();
		}).get();

		//console.info(ids);
		data.push({ name: "ids", value: ids });
		
		
		
		
		switch(section){
			case "archive":
				msg = "Are you sure you want to archive these "+ ids.length +" articles?";
				break;
			case "addnewsbook":
				msg = "Are you sure you want to add these "+ ids.length +" articles to the newsbook?\nAll photos will be added aswell, use the details pane to fine tune";
				break;
		}
		
		var dp = true;
		if (msg){
			dp = confirm(msg);
			
		}
		if (dp){
			$("#whole-area .loadingmask").show();
			$.post("/app/nf/save/checkboxes/"+section+"?r="+Math.random(),data,function(r){
				var s = {
					maintain_position:true
				};
				getList(s);
				
			});
			
			
			
			
		}
		
		
		
		
		
		
		return false;
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

		getList();
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
		//console.log($("#list-settings").length);
		e.preventDefault();
		var $this = $(this);

		//console.log("settings clicked");
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
	$(document).on("click", "#list-stage-btns button, #list-filter-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		var stage = $("#list-stage-btns button.active").attr("data-stage");
		stage = (stage) ? stage : "*";
		var filter = $("#list-filter-btns button.active").attr("data-filter");
		filter = (filter) ? filter : "*";

		$.bbq.pushState({"stage": stage, "filter":filter});
		getList();

	});
	$(document).on("change", "#stageID", function (e) {
		e.preventDefault();
		var $this = $(this);


		getList();

	});

	$searchform = $("#search-box form");
	$searchbox = $searchform.find(".search-query");
	$(document).bind('keyup', 'ctrl+f', function (e) {
		e.preventDefault();

		$searchform.toggle("slide", { direction:"right" }, 1000, function () {
			if ($(this).is(":visible")) {
				$searchform.find(".search-query").focus();
			} else {
				$searchbox.val("");
				getList();
			}
		});
		return false;
	});

	$(document).on("submit", "#search-box form", function (e) {
		e.preventDefault();
		getList();
	});

	if ($searchbox.val()) {
		$searchform.stop(true, true).show("slide", { direction:"right" }, 1000, function () {
		});
	}

	$(document).on('click', '#search-box-toggle', function (e) {
		$searchform.toggle("slide", { direction:"right" }, 1000, function () {
			if ($(this).is(":visible")) {
				$searchform.find(".search-query").focus();
			} else {
				$searchbox.val("");
				getList();
			}
		});
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
			$.post("/app/nf/save/list_settings/?section=provisional&reset=columns,group,order", function () {
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
		$.post("/app/nf/save/list_settings/?section=provisional", {"columns":columns, "group":group, "groupOrder":order}, function () {
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

});

function getList(settings) {
	var ID = $.bbq.getState("ID");
	var group = $.bbq.getState("groupBy");
	group = (group) ? group : "";
	var order = $.bbq.getState("order");
	order = (order) ? order : "";
	var groupOrder = $.bbq.getState("orderBy");
	groupOrder = (groupOrder) ? groupOrder : "";
	var checkbox = ($("#checkbox-button").hasClass("active"))?"1":"0";
	

	var stage = $("#list-stage-btns button.active").attr("data-stage");
	stage = (stage) ? stage : "";
	var filter = $("#list-filter-btns button.active").attr("data-filter");
	filter = (filter) ? filter : "";

	var search = $("#record-search").val();
	search = (search) ? search : "";

	var orderingactive = (order) ? true : false;


	var stageID = $("#stageID").val();

	$("#maintoolbar-date").html('Loading...');

	$("#whole-area .loadingmask").show();


	$.getData("/app/nf/data/provisional/_list", {"group": group, "groupOrder": groupOrder, "stage": stage, "filter": filter, "order": order, "search": search, "stageID": stageID}, function (data) {
		var $recordsList = $("#record-list");
		var $scrollpane = $("#whole-area .scroll-pane");
		var $checkboxMenu = $("#checkbox-menu");
		
		$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);
		if (data['list'][0]) {
			if (checkbox =='1'){
				$("#provisional-stats-bar").jqotesub($("#template-provisional-checkbox-bar"), data);
				
				$recordsList.jqotesub($("#template-records-checkbox"), data['list']);
				
				
				show_selected_count();
				//$recordsList.jqoteapp($("#template-records-checkbox-menu"),data);
			} else {
				$recordsList.jqotesub($("#template-records"), data['list']);
				
				$checkboxMenu.hide();
			}
			
		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
		}

		

		
		

		setTimeout(function(){
			if (orderingactive) {
				$scrollpane.jScrollPane(jScrollPaneOptionsMP);
			} else {
				if (settings && settings.maintain_position) {
					$scrollpane.jScrollPane(jScrollPaneOptionsMP);
				} else {
					$scrollpane.jScrollPane(jScrollPaneOptions);
				}

			}
		}, 400)
		
		
		

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
					setTimeout(function(){
						api.scrollToElement("#record-list .record[data-ID='" + goto + "']", true, true);
					}, 500)
					
				}

			}
			$.bbq.removeState("scrollTo");
		}

		$("#whole-area .loadingmask").fadeOut(transSpeed);
	},"list");


}
