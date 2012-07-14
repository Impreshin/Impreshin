/*
 * Date: 2012/05/30 - 8:37 AM
 */
var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");
$(document).ready(function () {

	scrolling(api);


	$("#pub-select input:checkbox").change(function(){
		var str = $("#pub-select input:checkbox:checked").map(function(){
				return $(this).attr("data-pub");
		});
		str = $.makeArray(str);
		if (str.length>1){
			str = str.length + " Publications"
		} else {
			str = str[0];
		}
		$("#pub-select-label").html(str);


	});
	$(document).on("click","#year-select button",function(){
		getData();
	});

	$(document).on("change", ".trigger_getdata input:checkbox", function () {
		getData();
	});
	$(document).on("click","#combine-btn",function(){
		getData();
	});
	getData();

	$("#accountID").select2({
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


	}).on("change",function(){
			getData();
		});






});

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

	var accountID = $("#accountID").val();


var $combined = $("#combine-btn");
	var daterange = $("#date-picker").val();
	var combined = ($combined.length)?($combined.hasClass("active"))?1:0:'none';



	$("#whole-area .loadingmask").show();


	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/ab/data/reports_account_figures/_data/", {"pubs":pubs,"years":years,"daterange":daterange,"combined":combined,"accountID":accountID}, function (data) {
		data = data['data'];

		$("#scroll-container").jqotesub($("#template-report-figures"), data);

		//console.log(data['combined']);
		if (data['combined']=='1' || data['pubs']=='1'){
			drawChart('chart-income', data['lines']['labels'], data['lines']['totals']);
			drawChart('chart-cm', data['lines']['labels'], data['lines']['cm']);
			drawChart('chart-records', data['lines']['labels'], data['lines']['records']);
		} else {
			drawChart('chart-income', data['lines']['labels'], data['lines']['pubs'],'totals');
			drawChart('chart-cm', data['lines']['labels'], data['lines']['pubs'],'cm');
			drawChart('chart-records', data['lines']['labels'], data['lines']['pubs'],'records');
		}


		var $scrollpane = $("#whole-area .scroll-pane");
			$scrollpane.jScrollPane(jScrollPaneOptionsMP);

		$('#date-picker').daterangepicker({
			presetRanges     :[
				{heading:'Preset Ranges'},
				{text     :'6 Months',
					dateStart:function () {
					//console.log("prev From: " + prevMonth.startDate)
						return Date.parse('t - 6 m').moveToFirstDayOfMonth();
					},
					dateEnd:function () {
					//console.log("prev To: " + prevMonth.endDate)
						return Date.parse('t -1 m').moveToLastDayOfMonth();
					}
				},
				{text     :'12 Months',
					dateStart:function () {
					//console.log("prev From: " + prevMonth.startDate)
						return Date.parse('t - 12 m').moveToFirstDayOfMonth();
					},
					dateEnd:function () {
					//console.log("prev To: " + prevMonth.endDate)
						return Date.parse('t -1 m').moveToLastDayOfMonth();
					}
				},
				{text     :'24 Months',
					dateStart:function () {
					//console.log("prev From: " + prevMonth.startDate)
						return Date.parse('t - 24 m').moveToFirstDayOfMonth();
					},
					dateEnd:function () {
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
				minDate    :Date.parse(data['date_min']),
				maxDate    :Date.parse(data['date_max'])
			},
			onOpen           :function () {

			},
			onClose          :function () {

				setTimeout(function () {
					if ($('#date-picker').data('cur')!= $('#date-picker').val()){
						getData();
					}





				}, 400);

			}

		}).data("cur",data['daterange']);



		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}));

}
function drawChart(element, label, data, pub_column) {
	//console.log(label.length)

	var tangle = 0;
	if (label.length >= 20) {
		tangle = -30;
	}
	if (label.length >= 30) {
		tangle = -90;
	}

	/*
	 var line1 = [
	 [date1, val1],
	 [date2, val2]
	 ];
	 var line2 = [
	 [date1, val11],
	 [date2, val12]
	 ];
	 var plot = $.jqplot('chart1', [line1, line2]);
	 */
	//console.log(data);

	if (pub_column) {
		var d = [];
		var legends = [];
		for (var i in data) {
			d.push(data[i][pub_column]);
			legends.push(data[i]['pub']);
		}
		data = d;
	} else {
		legends = "";
		data = [data];
	}
	//console.log(legends);

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
				}

				//console.log(pub_column);
				return "<span class='s dg'>" + label[pointIndex] + "</span>:<br> <strong>" + ret + "</strong>";
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