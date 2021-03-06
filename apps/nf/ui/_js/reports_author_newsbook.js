/*
 * Date: 2012/05/30 - 8:37 AM
 */
var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");
$(document).ready(function () {

	var filter = $.bbq.getState("filter");
	filter = (filter) ? filter : "*";

	$(document).on("click", "#list-filter-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		
		var filter = $("#list-filter-btns button.active").attr("data-filter");
		filter = (filter) ? filter : "*";

		$.bbq.pushState({"filter":filter});
		getData();

	});
	
	

	scrolling(api);

	if ($.bbq.getState("groupBy")) {
		$("#record-settings li[data-group-records-by].active").removeClass("active");
		$("#record-settings li[data-group-records-by='" + $.bbq.getState("groupBy") + "']").addClass("active");
	}
	if ($.bbq.getState("orderBy")) {
		$("#record-settings li[data-order-records-by].active").removeClass("active");
		$("#record-settings li[data-order-records-by='" + $.bbq.getState("orderBy") + "']").addClass("active");
	}


	$(document).on("click", "#record-settings li", function () {
		$("#log").append("clicked " + $(this).attr("data-group-records-by") + "<br>");
	});

	$(document).on("click", "#record-settings li[data-group-records-by]", function (e) {

		e.preventDefault();
		var $this = $(this);
		$("#record-settings li[data-group-records-by].active").removeClass("active");
		$this.addClass("active");
		$.bbq.pushState({"groupBy":$this.attr("data-group-records-by")});
		getData();

	});
	$(document).on("click", "#record-settings li[data-order-records-by]", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-settings li[data-order-records-by].active").removeClass("active");
		$this.addClass("active");
		$.bbq.pushState({"orderBy":$this.attr("data-order-records-by")});
		getData();

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

	$(document).on("reset", "#settings-modal form", function (e) {
		e.preventDefault();
		if (confirm("Are you sure you want to reset all these settings?")) {
			$("#settings-modal").addClass("loading");
			$.post("/app/nf/save/list_settings/?section=reports_author_newsbook&reset=columns,group,order", function () {
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
		$.post("/app/nf/save/list_settings/?section=reports_author_newsbook", {"columns":columns, "group":group, "groupOrder":order}, function () {
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
	
	
	
	
	
	
	
	
	
	
	// --------------------

	$(document).on("click", ".order-btn", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-list .order-btn").removeClass("asc desc");
		//$this.addClass("active");
		$.bbq.pushState({"order":$this.attr("data-col")});
		getData();
		$.bbq.removeState("order");
	});
	$(document).on("click", "tr.figure-month-details.record", function () {
		var $this = $(this);
		var ID = $this.attr("data-id");
		if ($this.hasClass("active")) {
			$("tr.figure-month-details.record.active").removeClass("active");
			$.bbq.removeState("dID");
		} else {
			$("tr.figure-month-details.record.active").removeClass("active");
			$this.addClass("active");
			$.bbq.pushState({"dID":ID});
		}
		getData();
	});

	$("#pub-select input:checkbox").change(function () {
		var str = $("#pub-select input:checkbox:checked").map(function () {
			return $(this).attr("data-pub");
		});
		str = $.makeArray(str);
		if (str.length > 1) {
			str = str.length + " Publications"
		} else {
			str = str[0];
		}
		$("#pub-select-label").html(str);

	});
	$(document).on("click", "#year-select button", function () {
		getData();
	});

	$(document).on("change", ".trigger_getdata input:checkbox, select.trigger_getdata", function () {
		getData();
	});
	$(document).on("click", "#combine-btn", function () {
		getData();
	});
	$(document).on("click", ".report-bottom-tabs button.back", function () {
		$.bbq.removeState("dID");
	});
	$(document).on("click", ".report-bottom-tabs button", function () {
		getData();
	});
	getData();
	$("#selectID").select2({
		

	}).on("change", function () {
			getData();
		});

});

function getData() {

	var pubs = $("#pub-select input:checkbox:checked").map(function () {
		return $(this).attr("data-id");
	});
	pubs = $.makeArray(pubs);
	pubs = pubs.join(",");

	var group = $.bbq.getState("groupBy");
	group = (group) ? group : "";
	var order = $.bbq.getState("order");
	order = (order) ? order : "";
	var groupOrder = $.bbq.getState("orderBy");
	groupOrder = (groupOrder) ? groupOrder : "";

	var years = $("#year-select button.active").map(function () {
		return $(this).attr("data-val");
	});
	years = $.makeArray(years);
	years = years.join(",");

	var ID = $("#selectID").val();

	var $combined = $("#combine-btn");
	var daterange = $("#date-picker").val();
	var combined = ($combined.length) ? ($combined.hasClass("active")) ? 1 : 0 : 'none';

	var dID = $.bbq.getState("dID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	$("#whole-area .loadingmask").show();
	var tolerance = $("#tolerance").val();

	var filter = $("#list-filter-btns button.active").attr("data-filter");
	filter = (filter) ? filter : "";


	$.getData("/app/nf/reports/data/author_newsbook/_data", {"pubs":pubs, "years":years, "daterange":daterange, "combined":combined, "ID":ID, "dID":dID, "order":order, "tolerance":tolerance, "group": group, "groupOrder": groupOrder, "filter": filter }, function (data) {


		$("#scroll-container").jqotesub($("#template-report-figures"), data);

		//console.log(data['combined']);

		if (data['tab'] == 'charts' && data.lines) {

			drawChart('chart-records', data);
			drawChart('chart-articles', data);
			drawChart('chart-cm', data);
			drawChart('chart-photos', data);



			var minDate_ = Date.parse(data['date_min']);
			var maxDate_ = Date.parse(data['date_max']);

			var minDate24 = Date.parse('t - 24 m').moveToFirstDayOfMonth();
			var maxDate24 = Date.parse('t - 1 m').moveToLastDayOfMonth();

			minDate_ = (minDate_ < minDate24) ? minDate_ : minDate24;
			maxDate_ = (maxDate_ < maxDate24) ? maxDate_ : maxDate24;

			$('#date-picker').daterangepicker({
				presetRanges     :[
					{heading:'Preset Ranges'},
					{text        : '6 Months', value: "6m",
						dateStart: function () {
							//console.log("prev From: " + prevMonth.startDate)
							return Date.parse('t - 6 m').moveToFirstDayOfMonth();
						},
						dateEnd  : function () {
							//console.log("prev To: " + prevMonth.endDate)
							return Date.parse('t -1 m').moveToLastDayOfMonth();
						}
					},
					{text        : '12 Months', value: "12m",
						dateStart: function () {
							//console.log("prev From: " + prevMonth.startDate)
							return Date.parse('t - 12 m').moveToFirstDayOfMonth();
						},
						dateEnd  : function () {
							//console.log("prev To: " + prevMonth.endDate)
							return Date.parse('t -1 m').moveToLastDayOfMonth();
						}
					},
					{text        : '24 Months', value: '24m',
						dateStart: function () {
							//console.log("prev From: " + prevMonth.startDate)
							return Date.parse('t - 24 m').moveToFirstDayOfMonth();
						},
						dateEnd  : function () {
							//console.log("prev To: " + prevMonth.endDate)
							return Date.parse('t -1 m').moveToLastDayOfMonth();
						}
					},

					{heading:'Selectable Ranges'}
				],
				//presetDates      :editions,
				presets          :{
					//specificDate:'Specific Date',
					//allDatesAfter:'All Dates After',
					dateRange:'Date Range'
				},
				posX             :null,
				posY             :null,
				arrows           :false,
				dateFormat       :'yy-mm-dd',
				rangeSplitter    :'to',
				datepickerOptions:{
					changeMonth:true,
					changeYear :true,
					minDate    :minDate_,
					maxDate    :maxDate_
				},
				onOpen           :function () {

				},
				onClose          :function () {

					setTimeout(function () {
						if ($('#date-picker').data('cur') != $('#date-picker').val()) {
							getData();
						}

					}, 400);

				}

			}).data("cur", data['daterange']);
		} else if (data['tab'] == 'records') {

			var $recordsList = $("#record-list");
			if (data['records'][0]) {
				$recordsList.jqotesub($("#template-records"), data['records']);
			} else {
				$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
			}

		}


		var ym = $.bbq.getState("ym");
		if (ym) {
			$(".figure-month-details[data-key='" + ym + "']").show();
			$("#figures-table tbody tr td[data-record='" + ym + "']").addClass("active");
		}
		var $scrollpane = $("#whole-area .scroll-pane");
		$scrollpane.jScrollPane(jScrollPaneOptionsMP);
		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}, "data");

}
function drawChart(element, data) {


	var col = "";
	switch (element) {
		case 'chart-articles':
			col = "articlesCount";
			break;
		case 'chart-photos':
			col = "photosCount";
			break;
		case 'chart-cm':
			col = "cm";
			break;
		case 'chart-records':
			col = "records";
			break;
	}


	var label = data['lines']['labels'];
	var label_d = data['lines']['labels_d'];


	data = data['lines'][col];
	legends = "";
	data = [data];

	var tangle = 0;
	if (label.length >= 20) {
		tangle = -30;
	}
	if (label.length >= 30) {
		tangle = -90;
	}

	var plot1 = $.jqplot(element, data, {
		legend      :{
			show    :(legends) ? true : false,
			labels  :legends,
			location:"sw",
			xoffset :0,
			yoffset :0
		},
		series      :{

		},
		axesDefaults:{
			tickRenderer:$.jqplot.CanvasAxisTickRenderer,
			tickOptions :{
				fontSize    :"10pt",
				formatString:"%s"

			}
		},

		axes:{
			xaxis:{
				renderer   :$.jqplot.CategoryAxisRenderer,
				ticks      :label,
				tickOptions:{
					angle:tangle,
					mark :'inside'

				}
			},
			yaxis:{
				//autoscale  :true,
				min        :0,
				tickOptions:{showLabel:false}
			}
		},

		tickRenderer:$.jqplot.CanvasAxisTickRenderer,
		grid        :{
			drawGridLines  :true, // wether to draw lines across the grid or not.
			gridLineColor  :'#f1f1f1', // *Color of the grid lines.
			background     :'#ffffff', // CSS color spec for background color of grid.
			borderColor    :'#cccccc', // CSS color spec for border around grid.
			borderWidth    :1.0, // pixel width of border around grid.
			shadow         :false, // draw a shadow for grid.
			shadowAngle    :45, // angle of the shadow.  Clockwise from x axis.
			shadowOffset   :1.5, // offset from the line of the shadow.
			shadowWidth    :3, // width of the stroke for the shadow.
			shadowDepth    :3, // Number of strokes to make when drawing shadow.
			// Each stroke offset by shadowOffset from the last.
			shadowAlpha    :0.07, // Opacity of the shadow
			renderer       :$.jqplot.CanvasGridRenderer, // renderer to use to draw the grid.
			rendererOptions:{}         // options to pass to the renderer.  Note, the default
			// CanvasGridRenderer takes no additional options.
		},

		seriesDefaults:{

			trendline:{
				show        :true, // show the trend line
				type        :"linear", // "linear", "exponential" or "exp"
				shadow      :true, // show the trend line shadow.
				lineWidth   :0.5, // width of the trend line.
				shadowAngle :45, // angle of the shadow.  Clockwise from x axis.
				shadowOffset:1.5, // offset from the line of the shadow.
				shadowDepth :3, // Number of strokes to make when drawing shadow.
				// Each stroke offset by shadowOffset from the last.
				shadowAlpha :0.07   // Opacity of the shadow
			}
		},
		highlighter   :{
			show                :true,
			sizeAdjust          :7.5,
			tooltipFormatString :'%s',
			useAxesFormatters   :true,
			tooltipAxes         :'y',
			tooltipLocation     :'n',
			tooltipOffset       :'-10',
			bringSeriesToFront  :true,
			useXTickMarks       :true,
			tooltipContentEditor:function (str, seriesIndex, pointIndex, plot) {

				var ret = str;
				

				//console.log(pub_column);
				return "<span class='s dg'>" + label_d[pointIndex] + "</span><br> <strong>" + ret + "</strong>";
			}
		}

	});
}
function cur(str) {

//	str = Number(str);
	//str = str.toFixed(2);
	str = Number(str).formatMoney(2, '.', ' ');
	return currency_sign + str;
}