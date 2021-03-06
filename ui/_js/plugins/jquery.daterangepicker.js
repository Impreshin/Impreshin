/**
 * --------------------------------------------------------------------
 * jQuery-Plugin "daterangepicker.jQuery.js"
 * by Scott Jehl, scott@filamentgroup.com
 * reference article: http://www.filamentgroup.com/lab/update_date_range_picker_with_jquery_ui/
 * demo page: http://www.filamentgroup.com/examples/daterangepicker/
 * 
 * Copyright (c) 2010 Filament Group, Inc
 * Dual licensed under the MIT (filamentgroup.com/examples/mit-license.txt) and GPL (filamentgroup.com/examples/gpl-license.txt) licenses.
 *
 * Dependencies: jquery, jquery UI datepicker, date.js, jQuery UI CSS Framework
 
 *  12.15.2010 Made some fixes to resolve breaking changes introduced by jQuery UI 1.8.7
 * --------------------------------------------------------------------
 */
jQuery.fn.daterangepicker = function(settings){
	var rangeInput = jQuery(this);
	
	//defaults
	var options = jQuery.extend({
		presetRanges: [
			{text: 'Today', dateStart: 'today', dateEnd: 'today' },
			{text: 'Last 7 days', dateStart: 'today-7days', dateEnd: 'today' },
			{text: 'Month to date', dateStart: function(){ return Date.parse('today').moveToFirstDayOfMonth();  }, dateEnd: 'today' },
			{text: 'Year to date', dateStart: function(){ var x= Date.parse('today'); x.setMonth(0); x.setDate(1); return x; }, dateEnd: 'today' },
			//extras:
			{text: 'The previous Month', dateStart: function(){ return Date.parse('1 month ago').moveToFirstDayOfMonth();  }, dateEnd: function(){ return Date.parse('1 month ago').moveToLastDayOfMonth();  } }
			//{text: 'Tomorrow', dateStart: 'Tomorrow', dateEnd: 'Tomorrow' },
			//{text: 'Ad Campaign', dateStart: '03/07/08', dateEnd: 'Today' },
			//{text: 'Last 30 Days', dateStart: 'Today-30', dateEnd: 'Today' },
			//{text: 'Next 30 Days', dateStart: 'Today', dateEnd: 'Today+30' },
			//{text: 'Our Ad Campaign', dateStart: '03/07/08', dateEnd: '07/08/08' }
		], 
		//presetRanges: array of objects for each menu preset. 
		//Each obj must have text, dateStart, dateEnd. dateStart, dateEnd accept date.js string or a function which returns a date object
		presets: {
			specificDate: 'Specific Date', 
			allDatesBefore: 'All Dates Before', 
			allDatesAfter: 'All Dates After', 
			dateRange: 'Date Range'
		},
		presetDates: {

		},
		rangeStartTitle: 'Start date',
		rangeEndTitle: 'End date',
		nextLinkText: '',
		prevLinkText: '',
		doneButtonText: 'Done',
		earliestDate: Date.parse('-15years'), //earliest date allowed 
		latestDate: Date.parse('+15years'), //latest date allowed 
		constrainDates: false,
		rangeSplitter: '-', //string to use between dates in single input
		dateFormat: 'm/d/yy', // date formatting. Available formats: http://docs.jquery.com/UI/Datepicker/%24.datepicker.formatDate
		closeOnSelect: true, //if a complete selection is made, close the menu
		arrows: false,
		appendTo: 'body',
		onClose: function(){},
		onOpen: function(){},
		onChange: function(){},
		datepickerOptions: null //object containing native UI datepicker API options
	}, settings);
	
	

	//custom datepicker options, extended by options
	var datepickerOptions = {
		onSelect: function(dateText, inst) { 
				if(rp.find('.ui-daterangepicker-specificDate').is('.active') || rp.find('.ui-daterangepicker-presetDate').is('.active')){
					rp.find('.range-end').datepicker('setDate', rp.find('.range-start').datepicker('getDate') ); 
				}
				
				$(this).trigger('constrainOtherPicker');
				
				var rangeA = fDate( rp.find('.range-start').datepicker('getDate') );
				var rangeB = fDate( rp.find('.range-end').datepicker('getDate') );
				
				//send back to input or inputs
				if(rangeInput.length == 2){
					rangeInput.eq(0).val(rangeA);
					rangeInput.eq(1).val(rangeB);
				}
				else{
					rangeInput.val((rangeA != rangeB) ? rangeA+' '+ options.rangeSplitter +' '+rangeB : rangeA);
				}
				//if closeOnSelect is true
				if(options.closeOnSelect){
					if(!rp.find('li.active').is('.ui-daterangepicker-dateRange') && !rp.is(':animated') ){
						hideRP();
					}
				}	
				options.onChange();			
			},
			defaultDate: +0
	};
	
	//change event fires both when a calendar is updated or a change event on the input is triggered
	rangeInput.bind('change', options.onChange);
	
	//datepicker options from options
	options.datepickerOptions = (settings) ? jQuery.extend(datepickerOptions, settings.datepickerOptions) : datepickerOptions;


	
	//Capture Dates from input(s)
	var inputDateA, inputDateB = Date.parse('today');
	var inputDateAtemp, inputDateBtemp;
	if(rangeInput.size() == 2){
		inputDateAtemp = Date.parse( rangeInput.eq(0).val() );
		inputDateBtemp = Date.parse( rangeInput.eq(1).val() );
		if(inputDateAtemp == null){inputDateAtemp = inputDateBtemp;} 
		if(inputDateBtemp == null){inputDateBtemp = inputDateAtemp;} 
	}
	else {
		inputDateAtemp = Date.parse( rangeInput.val().split(options.rangeSplitter)[0] );
		inputDateBtemp = Date.parse( rangeInput.val().split(options.rangeSplitter)[1] );
		if(inputDateBtemp == null){inputDateBtemp = inputDateAtemp;} //if one date, set both
	}
	if(inputDateAtemp != null){inputDateA = inputDateAtemp;}
	if(inputDateBtemp != null){inputDateB = inputDateBtemp;}

		
	//build picker and 
	var rp = jQuery('<div class="ui-daterangepicker"></div>');
	var rpPresets = (function(){
		var ul = jQuery('<ul class=""></ul>').appendTo(rp);

		var current = rangeInput.val(), selected;
		jQuery.each(options.presetDates, function () {
			if (!this.text && this.heading){
				jQuery('<li class="ui-daterangepicker-heading">' + this.heading + '</li>').appendTo(ul)
			} else {
				if (current== this.date){
					selected = "active";
				} else {
					selected = "";
				}
				jQuery('<li class="ui-daterangepicker-presetDate '+selected+'"><a href="#">' + this.text + '</a></li>').data('dateStart', this.date).appendTo(ul);
			}
		});
		jQuery.each(options.presetRanges,function(){
			if (!this.text && this.heading) {
				jQuery('<li class="ui-daterangepicker-heading">' + this.heading + '</li>').appendTo(ul)
			} else {

				jQuery('<li class="ui-daterangepicker-'+ this.text.replace(/ /g, '') +' "><a href="#">'+ this.text +'</a></li>')
					.data('dateStart', this.dateStart)
					.data('dateEnd', this.dateEnd)
					.data('value', this.value)
					.data('text', this.text)
					.appendTo(ul);
			}



		});



		var x=0;
		jQuery.each(options.presets, function(key, value) {
			jQuery('<li class="ui-daterangepicker-'+ key +' preset_'+ x +' "><i class="icon-chevron-right icon-grey"></i><a href="#">'+ value +'</a></li>')
			.appendTo(ul);
			x++;
		});
		
		ul.find('li').not(".ui-daterangepicker-heading").hover(
				function(){
					jQuery(this).addClass('hover');
				},
				function(){
					jQuery(this).removeClass('hover');
				})
			.click(function(){
				rp.find('.active').removeClass('active');
				jQuery(this).addClass('active');
				clickActions(jQuery(this),rp, rpPickers, doneBtn);
				return false;
			});




		return ul;
	})();


				
	//function to format a date string        
	function fDate(date){
	   if(!date.getDate()){return '';}
	   var day = date.getDate();
	   var month = date.getMonth();
	   var year = date.getFullYear();
	   month++; // adjust javascript month
	   var dateFormat = options.dateFormat;
	   return jQuery.datepicker.formatDate( dateFormat, date ); 
	}
	
	
	jQuery.fn.restoreDateFromData = function(){
		if(jQuery(this).data('saveDate')){
			jQuery(this).datepicker('setDate', jQuery(this).data('saveDate')).removeData('saveDate'); 
		}
		return this;
	};
	jQuery.fn.saveDateToData = function(){
		if(!jQuery(this).data('saveDate')){
			jQuery(this).data('saveDate', jQuery(this).datepicker('getDate') );
		}
		return this;
	};
	
	//show, hide, or toggle rangepicker
	function showRP(){
		if(rp.data('state') == 'closed'){ 
			positionRP();
			rp.fadeIn(300).data('state', 'open');

			var ar = $.map(options.presetRanges, function (v) {
				return v.value
			});
			var p = ar.indexOf(rangeInput.val());


			if (p >= 0) {
				p = p + 1;
				$(".ui-daterangepicker-" + options.presetRanges[p].text.replace(/ /g, ''),rp).addClass("active")
			}






			options.onOpen(); 
		}
	}
	function hideRP(){
		if(rp.data('state') == 'open'){ 
			rp.fadeOut(300).data('state', 'closed');

			var ar = $.map(options.presetRanges, function (v) {
				return v.value
			});
			var p = ar.indexOf(rangeInput.val());

			var title = rangeInput.val();
			if (p >= 0) {
				p = p + 1;
				title = options.presetRanges[p].text
			}

			rangeInput.text(title);
			options.onClose(); 
		}
	}
	function toggleRP(){
		if( rp.data('state') == 'open' ){ hideRP(); }
		else { showRP(); }
	}
	function positionRP(){
		var relEl = riContain || rangeInput; //if arrows, use parent for offsets
		var riOffset = relEl.offset(),
			side = 'left',
			val = riOffset.left,
			offRight = jQuery(window).width() - val - relEl.outerWidth();

		if(val > offRight){
			side = 'right', val =  offRight;
		}
		var paddingExtraTop = (riContain)?8:0;

		var width = relEl.outerWidth()-2;
		if (width<150) width = 150;
		rp.parent()
			.css(side, val)
			.css('top', riOffset.top + relEl.outerHeight()+ paddingExtraTop)
			.find("ul")
				.css("width", width+"px");
	}
	
	
					
	//preset menu click events	
	function clickActions(el, rp, rpPickers, doneBtn){
		
		if(el.is('.ui-daterangepicker-specificDate')){
			//Specific Date (show the "start" calendar)
			doneBtn.hide();
			rpPickers.show();
			rp.find('.title-start').text( options.presets.specificDate );
			rp.find('.range-start').restoreDateFromData().css('opacity',1).show(400);
			rp.find('.range-end').restoreDateFromData().css('opacity',0).hide(400);
			setTimeout(function(){doneBtn.fadeIn();}, 400);
		}
		else if(el.is('.ui-daterangepicker-allDatesBefore')){
			//All dates before specific date (show the "end" calendar and set the "start" calendar to the earliest date)
			doneBtn.hide();
			rpPickers.show();
			rp.find('.title-end').text( options.presets.allDatesBefore );
			rp.find('.range-start').saveDateToData().datepicker('setDate', options.earliestDate).css('opacity',0).hide(400);
			rp.find('.range-end').restoreDateFromData().css('opacity',1).show(400);
			setTimeout(function(){doneBtn.fadeIn();}, 400);
		}
		else if(el.is('.ui-daterangepicker-allDatesAfter')){
			//All dates after specific date (show the "start" calendar and set the "end" calendar to the latest date)
			doneBtn.hide();
			rpPickers.show();
			rp.find('.title-start').text( options.presets.allDatesAfter );
			rp.find('.range-start').restoreDateFromData().css('opacity',1).show(400);
			rp.find('.range-end').saveDateToData().datepicker('setDate', options.latestDate).css('opacity',0).hide(400);
			setTimeout(function(){doneBtn.fadeIn();}, 400);
		}
		else if(el.is('.ui-daterangepicker-dateRange')){
			//Specific Date range (show both calendars)
			doneBtn.hide();
			rpPickers.show();
			rp.find('.title-start').text(options.rangeStartTitle);
			rp.find('.title-end').text(options.rangeEndTitle);
			rp.find('.range-start').restoreDateFromData().css('opacity',1).show(400);
			rp.find('.range-end').restoreDateFromData().css('opacity',1).show(400);


			setTimeout(function(){doneBtn.fadeIn();}, 400);
		}
		else if (el.is('.ui-daterangepicker-presetDate')) {
			var dateStart = (typeof el.data('dateStart') == 'string') ? Date.parse(el.data('dateStart')) : el.data('dateStart')();



			doneBtn.hide();
			rp.find('.range-start, .range-end').css('opacity', 0).hide(400, function () {
				rpPickers.hide();
			});
			rp.find('.title-start').text(fDate(dateStart));
			rp.find('.range-start').saveDateToData().datepicker('setDate', dateStart).find('.ui-datepicker-current-day').trigger('click');
			rp.find('.range-end').restoreDateFromData();
			setTimeout(function () {
				doneBtn.fadeIn();
			}, 400);


		}
		else {
			//custom date range specified in the options (no calendars shown)
			doneBtn.hide();
			rp.find('.range-start, .range-end').css('opacity',0).hide(400, function(){
				rpPickers.hide();
			});

			if (el.data('value')) {
				var value = (typeof el.data('value') == 'string') ? el.data('value') : "";
				rangeInput.val(value)
				rangeInput.text(el.data('text'));
				if (el.data('dateStart')) {
					var dateStart = (typeof el.data('dateStart') == 'string') ? Date.parse(el.data('dateStart')) : el.data('dateStart')();
					rp.find('.range-start').datepicker('setDate', dateStart);
				}

				if (el.data('dateEnd')) {
					var dateEnd = (typeof el.data('dateEnd') == 'string') ? Date.parse(el.data('dateEnd')) : el.data('dateEnd')();
					rp.find('.range-end').datepicker('setDate', dateEnd);
				}

				toggleRP();
				return false;

			}

			if (el.data('dateStart')){
				var dateStart = (typeof el.data('dateStart') == 'string') ? Date.parse(el.data('dateStart')) : el.data('dateStart')();
				rp.find('.range-start').datepicker('setDate', dateStart).find('.ui-datepicker-current-day').trigger('click');
			}

			if (el.data('dateEnd')){
				var dateEnd = (typeof el.data('dateEnd') == 'string') ? Date.parse(el.data('dateEnd')) : el.data('dateEnd')();
				rp.find('.range-end').datepicker('setDate', dateEnd).find('.ui-datepicker-current-day').trigger('click');
			}

		}

//console.log(options.presetRanges)

		
		return false;
	}	
	

	//picker divs
	var rpPickers = jQuery('<div class="ranges"><div class="range-start"><span class="title-start">Start Date</span></div><div class="range-end"><span class="title-end">End Date</span></div></div>').appendTo(rp);
	rpPickers.find('.range-start, .range-end')
		.datepicker(options.datepickerOptions);
	
	
	rpPickers.find('.range-start').datepicker('setDate', inputDateA);
	rpPickers.find('.range-end').datepicker('setDate', inputDateB);
	
	rpPickers.find('.range-start, .range-end')	
		.bind('constrainOtherPicker', function(){
			if(options.constrainDates){
				//constrain dates
				if($(this).is('.range-start')){
					rp.find('.range-end').datepicker( "option", "minDate", $(this).datepicker('getDate'));
				}
				else{
					rp.find('.range-start').datepicker( "option", "maxDate", $(this).datepicker('getDate'));
				}			
			}
		})
		.trigger('constrainOtherPicker');
	
	var doneBtn = jQuery('<button class="btnDone btn span2 primary clearfix" style="margin-bottom: -5px;">'+ options.doneButtonText +'</button>')
	.click(function(){
		rp.find('.ui-datepicker-current-day').trigger('click');
		hideRP();
	})
	.hover(
			function(){
				jQuery(this).addClass('hover');
			},
			function(){
				jQuery(this).removeClass('hover');
			}
	)
	.appendTo(rpPickers);

	var ar = $.map(options.presetRanges, function (v) {
		return v.value
	});
	var p = ar.indexOf(rangeInput.val());

	var title = rangeInput.val();
	if (p >= 0) {
		p = p + 1;
		$("ui-daterangepicker-" + options.presetRanges[p].text.replace(/ /g, '')).addClass("active");
		title = options.presetRanges[p].text

		if (options.presetRanges[p].dateStart) {
			rp.find('.range-start').data('saveDate', options.presetRanges[p].dateStart());
		}
		if (options.presetRanges[p].dateEnd) {
			rp.find('.range-end').data('setDate', options.presetRanges[p].dateEnd());
		}

	}
	if (title) {
		rangeInput.text(title);

	}
	
	//inputs toggle rangepicker visibility
	jQuery(this).click(function(){
		toggleRP();
		return false;
	});
	//hide em all
	rpPickers.hide().find('.range-start, .range-end, .btnDone').hide();
	
	rp.data('state', 'closed');
	
	//Fixed for jQuery UI 1.8.7 - Calendars are hidden otherwise!
	rpPickers.find('.ui-datepicker').css("display","block");
	
	//inject rp
	jQuery(options.appendTo).append(rp);
	
	//wrap and position
	rp.wrap('<div class="ui-daterangepickercontain"></div>');

	//add arrows (only available on one input)
	if(options.arrows && rangeInput.size()==1){
		var prevLink = jQuery('<a href="#" class="ui-daterangepicker-prev btn btn-mini add-on" title="'+ options.prevLinkText +'"><i class="icon-chevron-left"></i>'+ options.prevLinkText +'</a>');
		var nextLink = jQuery('<a href="#" class="ui-daterangepicker-next btn  btn-mini add-on" title="'+ options.nextLinkText +'"><i class="icon-chevron-right"></i>'+ options.nextLinkText +'</a>');
	
		jQuery(this)
		.addClass('ui-rangepicker-input')
		.wrap('<span class="ui-daterangepicker-arrows input-prepend input-append"></span>')
		.before( prevLink )
		.after( nextLink )
		.parent().find('a').click(function(){
			var dateA = rpPickers.find('.range-start').datepicker('getDate');
			var dateB = rpPickers.find('.range-end').datepicker('getDate');
			var diff = Math.abs( new TimeSpan(dateA - dateB).getTotalMilliseconds() ) + 86400000; //difference plus one day
			if(jQuery(this).is('.ui-daterangepicker-prev')){ diff = -diff; }
			
			rpPickers.find('.range-start, .range-end ').each(function(){
					var thisDate = jQuery(this).datepicker( "getDate");
					if(thisDate == null){return false;}
					jQuery(this).datepicker( "setDate", thisDate.add({milliseconds: diff}) ).find('.ui-datepicker-current-day').trigger('click');
			});
			return false;
		})
		.hover(
			function(){
				jQuery(this).addClass('hover');
			},
			function(){
				jQuery(this).removeClass('hover');
			});
		
		var riContain = rangeInput.parent();	
	}
	
	
	jQuery(document).click(function(){
		if (rp.is(':visible')) {
			hideRP();
		}
	}); 

	rp.click(function(){return false;}).hide();
	return this;
};



