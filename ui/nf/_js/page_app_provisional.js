/*
 * Date: 2012/03/06 - 2:45 PM
 */
var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");
$(document).ready(function () {

	scrolling(api);


	var stage = $.bbq.getState("stage");
	stage = (stage) ? stage : "all";
	var status = $.bbq.getState("status");
	status = (status) ? status : "*";

	$("select#authorID").select2().on("change", function () {
		getList();
	});



	if ($.bbq.getState("modal")=="settings"){
		$("#settings-modal").modal('show');
	}

	if ($.bbq.getState("stage")) {
		$("#list-stage-btns button[data-stage].active").removeClass("active");
		$("#list-stage-btns button[data-stage='"+ stage+"']").addClass("active");
	}
	if ($.bbq.getState("status")) {
		$("#list-status-btns button[data-status].active").removeClass("active");
		$("#list-status-btns button[data-status='"+ status+"']").addClass("active");
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
	$(document).on("click", "#list-stage-btns button, #list-status-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		var stage = $("#list-stage-btns button.active").attr("data-stage");
		stage = (stage) ? stage : "all";
		var status = $("#list-status-btns button.active").attr("data-status");
		status = (status) ? status : "*";


		$.bbq.pushState({"stage":stage,"status":status});
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
			$.post("/nf/save/list_settings/?section=provisional&reset=columns,group,order", function () {
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
		$.post("/nf/save/list_settings/?section=provisional",{"columns":columns,"group":group,"groupOrder":order},function(){
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

function getList(settings) {
	var ID = $.bbq.getState("ID");
	var group = $.bbq.getState("groupBy");
	group = (group)? group:"";
	var order = $.bbq.getState("order");
	order = (order)? order:"";
	var groupOrder = $.bbq.getState("orderBy");
	groupOrder = (groupOrder)? groupOrder:"";

	var stage = $("#list-stage-btns button.active").attr("data-stage");
	stage = (stage)? stage: "";
	var status = $("#list-status-btns button.active").attr("data-status");
	status = (status)? status: "";

	var authorID = $("#authorID").val();
	authorID = (authorID)? authorID: "";



	var orderingactive = (order)?true:false;

	$("#whole-area .loadingmask").show();
	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/nf/data/provisional/_list",{"group": group,"groupOrder":groupOrder, "stage":stage, "status":status, "order": order, "authorID":authorID},function(data){
		data = data['data'];




		var $recordsList = $("#record-list");
		if (data['list'][0]){
			$recordsList.jqotesub($("#template-records"), data['list']);
		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
		}

		//$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);



		var stages = data['stats']['stages'];
		var $list_stage_btns = $("#list-stage-btns");
		$.each(stages,function(k,v){
			$list_stage_btns.find("button[data-stage='"+k+"'] span.count").text("("+v['count']+")");
		});



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

		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}));


}
