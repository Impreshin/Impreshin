/*
 * Date: 2012/05/30 - 8:37 AM
 */
var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");
$(document).ready(function () {

	scrolling(api);

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

	$("#dir").click(function () {
		var $this = $(this);
		if ($this.attr("data-dir") == 'u') {
			$this.attr("data-dir", "d");
		} else {
			$this.attr("data-dir", "u");
		}

		dir();
		getData();
	});
	dir();

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
		formatResult   :function (result, query, markup) {
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
		formatSelection:function (result) {

			return result.text;
		}


	}).on("change", function () {
			getData();
		});

});
function dir() {
	var $this = $("#dir");
	var $icon = $this.find("i");
	$icon.removeClass("icon-thumbs-up icon-thumbs-down");
	if ($this.attr("data-dir") == 'u') {
		$icon.addClass("icon-thumbs-up");
		$this.attr("title", "Over-Charged");
	} else {
		$icon.addClass("icon-thumbs-down");
		$this.attr("title", "Under-Charged");
	}
}
function getData() {

	var pubs = $("#pub-select input:checkbox:checked").map(function () {
		return $(this).attr("data-id");
	});
	pubs = $.makeArray(pubs);
	pubs = pubs.join(",");

	var years = $("#year-select button.active").map(function () {
		return $(this).attr("data-val");
	});
	years = $.makeArray(years);
	years = years.join(",");

	var selectID = $("#selectID").val();
	var dir = $("#dir").attr("data-dir");

	var $combined = $("#combine-btn");
	var daterange = $("#date-picker").val();
	var combined = ($combined.length) ? ($combined.hasClass("active")) ? 1 : 0 : 'none';

	var dID = $.bbq.getState("dID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	$("#whole-area .loadingmask").show();
	var tolerance = $("#tolerance").val();

	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/ab/data/reports/account_discounts/_data", {"pubs":pubs, "years":years, "daterange":daterange, "combined":combined, "ID":selectID, "dir":dir, "dID":dID, "order":order, "tolerance":tolerance}, function (data) {
		data = data['data'];

		$("#scroll-container").jqotesub($("#template-report-figures"), data);

		//console.log(data['combined']);

		if (data['tab'] == 'charts') {

			drawChart('chart-percent', data);
			drawChart('chart-o_u_charged', data);
			drawChart('chart-records', data);

			var minDate_ = Date.parse(data['date_min']);
			var maxDate_ = Date.parse(data['date_max']);

			var minDate24 = Date.parse('t - 24 m').moveToFirstDayOfMonth();
			var maxDate24 = Date.parse('t - 1 m').moveToLastDayOfMonth();

			minDate_ = (minDate_ < minDate24) ? minDate_ : minDate24;
			maxDate_ = (maxDate_ < maxDate24) ? maxDate_ : maxDate24;

			$('#date-picker').daterangepicker({
				presetRanges     :[
					{heading:'Preset Ranges'},
					{text        :'6 Months',
						dateStart:function () {
							//console.log("prev From: " + prevMonth.startDate)
							return Date.parse('t - 6 m').moveToFirstDayOfMonth();
						},
						dateEnd  :function () {
							//console.log("prev To: " + prevMonth.endDate)
							return Date.parse('t -1 m').moveToLastDayOfMonth();
						}
					},
					{text        :'12 Months',
						dateStart:function () {
							//console.log("prev From: " + prevMonth.startDate)
							return Date.parse('t - 12 m').moveToFirstDayOfMonth();
						},
						dateEnd  :function () {
							//console.log("prev To: " + prevMonth.endDate)
							return Date.parse('t -1 m').moveToLastDayOfMonth();
						}
					},
					{text        :'24 Months',
						dateStart:function () {
							//console.log("prev From: " + prevMonth.startDate)
							return Date.parse('t - 24 m').moveToFirstDayOfMonth();
						},
						dateEnd  :function () {
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
	}));

}
function drawChart(element, data) {
	//console.log(label.length)

	var col = "";
	// percent, o_u_charged
	switch (element) {
		case 'chart-income':
			col = "totals";
			break;
		case 'chart-cm':
			col = "cm";
			break;
		case 'chart-records':
			col = "records";
			break;
		case 'chart-percent':
			col = "percent";
			break;
		case 'chart-o_u_charged':
			col = "totals";
			break;
	}
	var label = data['lines']['labels'];
	var label_d = data['lines']['labels_d'];

	if (data['combined'] == '1' || data['pubs'] == '1') {
		data = data['lines'][col];
		legends = "";
		data = [data];
	} else {
		data = data['lines']['pubs'];
		var d = [];
		var legends = [];
		for (var i in data) {
			d.push(data[i][col]);
			legends.push(data[i]['pub']);
		}
		data = d;
	}
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
				switch (element) {
					case 'chart-income':
						ret = cur(Number(ret));
						break;
					case 'chart-cm':
						ret = ret + ' cm';
						break;
					case 'chart-records':
						ret = ret + '';
						break;
					case 'chart-percent':
						ret = ret + ' %';
						break;
					case 'chart-o_u_charged':
						ret = cur(Number(ret));
						break;
				}

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