var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#right-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {

	if ($.bbq.getState("search")){
		$("#search").val($.bbq.getState("search"))
	}

	if ($("#search").val()){
		getSearch();
	} else {
		getLeft();
	}
	

	
	getRight();
//$("#whole-area .loadingmask").show();

	$(document).on('hide', '#cm-details-modal', function () {
		var s = {
			maintain_position:true
		};
		getLeft(s);
		getRight(s);
	});
	
	$(document).on('click',  "#btns-feed-days button", function () {
		
		getRight();
	});
	$(document).on('click',  "#btns-tabs button", function () {
		$.bbq.removeState("search");
		$("#search").val("")
		getLeft();
	});

	$(document).on("submit", "#search-form-toolbar", function (e) {
		e.preventDefault();
		var val = $("#search").val();
		if (val){
			$.bbq.pushState({"search":val});
			getSearch();
		} else {
			$.bbq.removeState("search")
			getLeft();
		}
		

	});
	$(document).on("reset", "#search-form-toolbar", function (e) {
		e.preventDefault();
		$.bbq.removeState("search");
		$("#search").val("");
		getLeft();

	});

	$(document).on("click", ".view-record-btn", function () {
		var id = $(this).attr("data-link-id");
		if (id) {
			$.bbq.pushState({"ID": id});

			getDetails();
		}

	});

	$(document).on("click", ".ticker-feed-list a", function (e) {
		e.preventDefault();
		var $this = $(this);
		var id = $this.attr("data-link-id");
		var record = $this.attr("data-id");
		var type = $this.attr("data-type");
		var pane = "details-pane-details";
		if (type=="note"){
			pane = "details-pane-notes";
		}
		if (id) {
			
			$.bbq.pushState({
				"ID": id,
				"details-tab": pane,
				"scroll":record
			});

			getDetails();
		}

	});





	

});

function getLeft(settings) {
	

	var tab = $("#btns-tabs button.active").attr("data-tab");
	

	$("#search").css("width","156px");
	$("#search-form-toolbar button[type='reset']").hide();
	$("#left-area .loadingmask").show();


	$.getData("/app/cm/data/front/left", {"tab": tab}, function (data) {
		var $scrollpane = $("#whole-area .scroll-pane");

		
		
		
		$("#calendar-area").jqotesub($("#template-left-"+data['tab']), data['data']);


		

		

		
		if ($.bbq.getState("ID")) {
			

			if (!$("#cm-details-modal").is(":visible")) {
				getDetails();
			}
		}

		if (data['tab']=='1'){
			calendar(data);
		}

		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		$("#left-area .loadingmask").fadeOut(transSpeed);

		

		$("#btns-tabs button.active").removeClass("active");
		$("#btns-tabs button[data-tab='"+data['tab']+"']").addClass("active");
		
		
		
		
		
	},"left");


}
function getRight(settings) {
	

	var feed_days = $("#btns-feed-days button.active").attr("data-days");

	$("#right-area .loadingmask").show();


	$.getData("/app/cm/data/front/feed", {"feed_days": feed_days}, function (data) {
		var $scrollpane = $("#whole-area .scroll-pane");
		
		
		
		if (data['data'].length){
			$("#ticker-area").jqotesub($("#template-ticker"), data['data']);
		}	 else {
			$("#ticker-area").html('<div class="no-records-found">No records found</div>');
		}
		


		

		

		$('#right-area .nav-list.ticker-feed-list li').each(function(){
			var $this = $(this);
			$this.popover({
				placement: 'left',
				trigger: 'hover',
				html: true,
				content: $this.find(".popover-text").html(),
				container:'body',
				title: $this.find(".popover-title").html()
			})
		});


		if ($.bbq.getState("ID")) {
			

			if (!$("#cm-details-modal").is(":visible")) {
				getDetails();
			}
		}


	
		
		
		
		$("#right-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		$("#right-area .loadingmask").fadeOut(transSpeed);
		
		
		
	},"right");


}
function getSearch(settings) {


	

	var search = $.bbq.getState("search")||$("#search").val();
	//if ()

	if (search){
		$("#search").css("width","123px");
		$("#search-form-toolbar button[type='reset']").show();
	} else {
		$("#search").css("width","156px");
		$("#search-form-toolbar button[type='reset']").hide();
	}

	$("#left-area .loadingmask").show();


	$.getData("/app/cm/data/front/search", {"search":search}, function (data) {
		var $scrollpane = $("#whole-area .scroll-pane");





		$("#calendar-area").jqotesub($("#template-left-search"), data);







		if ($.bbq.getState("ID")) {


			if (!$("#cm-details-modal").is(":visible")) {
				getDetails();
			}
		}


		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		$("#left-area .loadingmask").fadeOut(transSpeed);



		$("#btns-tabs button.active").removeClass("active");
		





	},"left");


}
function calendar(data){
	
	
	
	var curDate = moment().format("YYYY-MM-DD");

	var blockHight = $("#left-area").innerHeight() - 40;

	//blockHight = 500;
	
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		defaultDate: curDate,
		height: blockHight ,
		editable: true,

		selectable: true,
		selectHelper: true,
		select: function(start, end) {
			var title = prompt('Event Title:');
			var eventData;
			if (title) {
				eventData = {
					title: title,
					start: start,
					end: end
				};
				$('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
			}
			$('#calendar').fullCalendar('unselect');
		},


		//handleWindowResize: true,
		eventLimit: true, // allow "more" link when too many events
		lazyFetching:false,
		
		events: function(start, end, timezone, callback) {
			$.ajax({
				url: "/app/cm/data/front/left?tab=1",
				dataType: 'json',
				data: {
					// our hypothetical feed requires UNIX timestamps
					start: start.unix(),
					end: end.unix()
				},
				success: function(data) {
					//console.log(data['data']['records'])
					
					callback(data['data']['records']);
				}
			});
		},
		dayClick: function(date, jsEvent, view) {

			console.log('Clicked on: ' + date.format());
			//alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
			//alert('Current view: ' + view.name);

			// change the day's background color just for fun
			$(this).css('background-color', 'red');

		},
		eventClick: function(calEvent, jsEvent, view) {

			//alert('Event: ' + calEvent.title);
			//alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
			//alert('View: ' + view.name);

			// change the border color just for fun
			$(this).css('background-color', 'blue');

		},
		eventRender: function(event, element) {
			var $element = $(element);
			
			
			if (event.class){
				$element.addClass(event.class)
			}
			//return "hi";
		}
		
	});
	
	

}