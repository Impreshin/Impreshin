/*
 * Date: 2012/05/30 - 8:37 AM
 */
var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {


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
	$(document).on("change",".trigger_getdata input:checkbox",function(){
		getData();
	});
	getData();









});
function drawChart(element,title,label,data){
	//console.log(label.length)

	var tangle = 0;
	if (label.length >= 20){
		tangle = -30;
	}
	if (label.length >= 30) {
		tangle = -90;
	}

	var plot1 = $.jqplot(element, [data], {
		series:[
			{showMarker:true}
		],
		axesDefaults:{
			tickRenderer:$.jqplot.CanvasAxisTickRenderer,
			tickOptions :{
				fontSize:"10pt"

			}
		},


		axes:{
			xaxis:{
				renderer   :$.jqplot.CategoryAxisRenderer,
				ticks      :label,
				tickOptions:{
					angle:tangle,
					mark:'inside'

				}
			},
			yaxis:{
				//autoscale  :true,
				min: 0,
				tickOptions:{showLabel:false}
			}
		},


		tickRenderer:$.jqplot.CanvasAxisTickRenderer,
		grid :{
			drawGridLines  :true, // wether to draw lines across the grid or not.
			gridLineColor  :'#e1e1e1',    // *Color of the grid lines.
			background     :'#ffffff', // CSS color spec for background color of grid.
			borderColor    :'#cccccc', // CSS color spec for border around grid.
			borderWidth    :1.0, // pixel width of border around grid.
			shadow         :false, // draw a shadow for grid.
			shadowAngle    :45, // angle of the shadow.  Clockwise from x axis.
			shadowOffset   :1.5, // offset from the line of the shadow.
			shadowWidth    :3, // width of the stroke for the shadow.
			shadowDepth    :3, // Number of strokes to make when drawing shadow.
			// Each stroke offset by shadowOffset from the last.
			shadowAlpha    :0.07 ,          // Opacity of the shadow
			renderer       :$.jqplot.CanvasGridRenderer, // renderer to use to draw the grid.
			rendererOptions:{}         // options to pass to the renderer.  Note, the default
			// CanvasGridRenderer takes no additional options.
		},
		legend:{location:"w"},
		seriesDefaults:{
			trendline:{
				show        :true, // show the trend line
				color       :"#999999", // CSS color spec for the trend line.
				label       :"", // label for the trend line.
				type        :"linear", // "linear", "exponential" or "exp"
				shadow      :true, // show the trend line shadow.
				lineWidth   :1, // width of the trend line.
				shadowAngle :45, // angle of the shadow.  Clockwise from x axis.
				shadowOffset:1.5, // offset from the line of the shadow.
				shadowDepth :3, // Number of strokes to make when drawing shadow.
				// Each stroke offset by shadowOffset from the last.
				shadowAlpha :0.07   // Opacity of the shadow
			}
		},
		highlighter:{
			show      :true,
			sizeAdjust:7.5,
			tooltipFormatString : '%s',
			useAxesFormatters: true,
			tooltipAxes:'both'
		}

	});
}

function getData() {

	var pubs = $("#pub-select input:checkbox:checked").map(function () {
		return $(this).attr("data-id");
	});
	pubs = $.makeArray(pubs);
	pubs = pubs.join(",");

	var years = $("#report-years input:checkbox:checked").map(function () {
		return $(this).val();
	});
	years = $.makeArray(years);
	years = years.join(",");



	var daterange = $("#date-picker").val();


	$("#whole-area .loadingmask").show();


	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/ab/data/reports_publication_figures/_data/", {"pubs":pubs,"years":years,"daterange":daterange}, function (data) {
		data = data['data'];

		$("#scroll-container").jqotesub($("#template-report-figures"), data);

		drawChart('chart-income', 'Income', data['lines']['labels'], data['lines']['totals']);
		drawChart('chart-cm', 'Cm', data['lines']['labels'], data['lines']['cm']);
		drawChart('chart-records', 'Records', data['lines']['labels'], data['lines']['records']);

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
				changeYear :true
				//minDate    :earliestDate,
				//maxDate    :latestDate
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