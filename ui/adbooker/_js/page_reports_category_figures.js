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
	$(document).on("click","tr.figure-month-details.record",function(){
		var $this = $(this);
		var ID = $this.attr("data-id");
		if ($this.hasClass("active")){
			$("tr.figure-month-details.record.active").removeClass("active");
			$.bbq.removeState("dID");
		} else {
			$("tr.figure-month-details.record.active").removeClass("active");
			$this.addClass("active");
			$.bbq.pushState({"dID":ID});
		}
		getData();
	});

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
	$(document).on("click", ".report-bottom-tabs button.back", function () {
		$.bbq.removeState("dID");
		});
	$(document).on("click", ".report-bottom-tabs button", function () {
		getData();
	});
	getData();

	$("select#selectID").select2().on("change",function(){
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

	var ID = $("#selectID").val();


var $combined = $("#combine-btn");
	var daterange = $("#date-picker").val();
	var combined = ($combined.length)?($combined.hasClass("active"))?1:0:'none';

	var dID = $.bbq.getState("dID");

	var order = $.bbq.getState("order");
		order = (order)? order:"";

	$("#whole-area .loadingmask").show();


	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/ab/data/reports/category_figures/_data", {"pubs":pubs,"years":years,"daterange":daterange,"combined":combined,"ID":ID, "dID":dID, "order":order}, function (data) {
		data = data['data'];

		$("#scroll-container").jqotesub($("#template-report-figures"), data);

		//console.log(data['combined']);

		if (data['tab'] == 'charts') {


			drawChart('chart-income', data);
			drawChart('chart-cm', data);
			drawChart('chart-records',data);


	

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
		} else if (data['tab']=='records'){

			var $recordsList = $("#record-list");
			if (data['records'][0]){
				$recordsList.jqotesub($("#template-records"), data['records']);
			} else {
				$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
			}


		}
		var $scrollpane = $("#whole-area .scroll-pane");
		$scrollpane.jScrollPane(jScrollPaneOptionsMP);
		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}));
}

function drawChart(element, data) {
	//console.log(label.length)

	var col = "";
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