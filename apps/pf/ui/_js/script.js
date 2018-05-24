var detailsRequest = [];
var listRequest = [];
var logsRequest = [];
var activityRequest = [];
var transSpeed = '300';



$(function () {
	/*$(window).bind( "hashchange", function(e) {
		var ID = $.bbq.getState( "ID" );
		if (ID){
			getDetails();
		}
	});*/

	$('body').tooltip({
		selector : '*[rel=tooltip]',
		live     : true,
		container: 'body'

	});
	$('body').popover({
		selector : '*[rel=popover]',
		offset   : 5,
		live     : true,
		container: 'body',
		html     : true
	});

	$('#notice-area').popover({
		selector : 'span.nbar',
		offset   : 0,
		live     : true,
		container: 'body',
		html     : true,
		trigger  : "hover",
		placement: "top"
	});

	$('.dropdown-toggle').dropdown();

	$(document).ajaxError(function (e, xhr, settings, exception) {
		if (xhr.responseText) alert('error in: ' + settings.url + ' \n \n \n' + '\n' + xhr.responseText);
	});

	$(document).on("show", '#loggedinusers', function () {
		var $this = $(this);
		$this.find("#loggedinusers-activity table tbody").html('<tr><td colspan="2">Loading...</td></tr>');
		$.getJSON("/data/user_activity", "", function (d) {
			d = d['data'];
			$area = $this.find("#loggedinusers-activity table tbody");
			if (!d[0]) {
				$area.html('<tr><td colspan="2">No Users logged in for this company</td></tr>');
			} else {
				$area.jqotesub($("#template-logged-in-users"), d);
			}

		});
	});

});

$(document).ready(function () {

	var fancyHelpOptions = {
		type         : 'iframe',
		iframe       : {
			scrolling: 'no',
			preload  : true
		},
		padding      : 0,
		width        : 950,
		scrollOutside: false,
		beforeClose  : function () {
			$(this.element).attr("href", $.bbq.getState("help"));

		},
		afterClose   : function () {
			$.bbq.removeState("help");
		},

		afterShow: function () {

			// var $frame = $(this.content).contents();
			var $f = $(this.content);
			//console.log($f);
			$f[0].contentWindow.scrollbars();

		},
		title    : false,
		closeBtn : false
	};
	var fancyMessagesOptions = {
		type         : 'iframe',
		iframe       : {
			scrolling: 'no',
			preload  : true
		},
		padding      : 0,
		width        : 950,
		scrollOutside: false,
		beforeClose  : function () {
			//$(this.element).attr("href", $.bbq.getState("help"));

		},
		afterClose   : function () {
			$.bbq.removeState("messages");
			$.getData("/app/pf/notifications?r="+Math.random(),function(){
				
			})
		},

		afterShow: function () {

			// var $frame = $(this.content).contents();
			var $f = $(this.content);
			//console.log($f);
			$f[0].contentWindow.scrollbars();

		},
		title    : false,
		closeBtn : false
	};

	var help = $.bbq.getState("help");
	if (help) {


		$.fancybox.open([
			{
				href: help
			}
		], fancyHelpOptions);
	}

	var messages = $.bbq.getState("messages");
	if (messages) {
		$.fancybox.open([
			{
				href: "/app/pf/messages"
			}
		], fancyMessagesOptions);
	}

	$("a.help_link").fancybox(fancyHelpOptions);
	$("a.messages_link").fancybox(fancyMessagesOptions);

	$(document).bind('keydown', 'f1', function (e) {
		e.preventDefault();
		$("#pagefooter a.help_link").trigger("click");
		return false;
	});


	var $pubSelectID = $("#pubSelectID");
	if ($pubSelectID.length) {
		$pubSelectID.select2({
			dropdownCssClass: 'pub-select-drop'
		}).change(function () {
			window.location = "?apID=" + $(this).val();
		});
	}

	$(document).on('click', '.checkall', function () {
		$(this).closest('.control-group').find(':checkbox').prop('checked', this.checked);
	});
	$(document).on("change", "#pubSelectID", function () {
		window.location = "?apID=" + $(this).val();
	});

	$('.no-csstransforms3d .fade').removeClass('fade');

	$(document).on("click", ".modal .close-btn", function (e) {
		e.preventDefault();
		var $this = $(this), $modal = $this.closest(".modal");
		$modal.modal("hide");
		//	$("#settings-modal").modal('hide');
	});
	$(document).on("click", "*[data-dismiss='popup']", function (e) {
		e.preventDefault();
		var $this = $(this), $popup = $this.closest(".popup");
		$popup.fadeOut(transSpeed);
	});
	$(document).on("click", "*[data-target='popup']", function (e) {
		e.preventDefault();
		var $this = $(this), $popup = $this.attr("href");
		$($popup).fadeIn(transSpeed).trigger("popup-show");
	});

	$(document).on('click', '.btn-row-details', function (e) {
		var $this = $(this), $table = $this.closest("table");
		var $clicked = $(e.target).closest("tr.btn-row-details");
		var active = true;

		if ($this.hasClass("active") && $clicked) active = false;

		$("tr.btn-row-details.active", $table).removeClass("active");
		if (active) {
			$this.addClass("active");
		}

		var show = $("tr.btn-row-details.active", $table).nextAll("tr.row-details");

		$("tr.row-details", $table).hide();
		if (show.length) {
			show = show[0];
			$(show).show();
		}

	});

	$(document).on("click", "#keepalivebtn", function () {
		$("#displaylogout").autoLogout('resetTimer');
		$.getJSON("/app/keepalive?keepalive=true", function (d) {
			$("#displaylogout").fadeOut(1000);
		})
	});
	$(document).on("click", "#keepalivebtn-logout", function () {
		$("#displaylogout").autoLogout('logout');

	});

});
var autologoutRequest = [];
var $noticeareaIdle = $("#notice-area-idle");

//logout 5 minutes
// warning 4 minutes
// show idle 2 minutes

$("#displaylogout").autoLogout({
	LogoutTime: 900,
	//LogoutTime: 30,
	keepAliveSelector: '.keep-me-logged-in',

	onResetTimer : function (e) {
		this.css("background-color", "rgba(250, 250, 250, 0.8)");
		$noticeareaIdle.stop(true, true).fadeOut(1000)

	},
	onLogout     : function (timer) {
		var $this = this;
		var settings = $this.data("settings");
		
		
		
		
		if ($(settings.keepAliveSelector).length){
			$.getJSON("/app/keepalive?keepalive=true", function (result) {
				$("#displaylogout").autoLogout('resetTimer');
			});
		} else {
			window.location = "/app/logout/?msg=You+were+logged+out+due+to+inactivity";
		}
		
		
	},
	keepAlive    : function () {
		$.getJSON("/app/keepalive?keepalive=true", function (result) {
			//this.autoLogout('reset')
		});

	},
	onTimerSecond: function (idle) {
		var $this = this;
		var settings = $this.data("settings");
		var LogoutTime = settings.LogoutTime;
		var parts = LogoutTime / 10;

		
		
			
		
			if (idle == Math.floor(parts * 4) || idle == Math.floor(parts * 8) || idle == Math.floor(parts * 9) || idle >= LogoutTime - 3) {
				if ($(".keep-me-logged-in").length){
					url = '/app/keepalive?keepalive=true'
				} else {
					url = '/app/keepalive'
				}
				
				for (var i = 0; i < autologoutRequest.length; i++) autologoutRequest[i].abort();
				autologoutRequest.push($.getJSON(url, function (result) {
					var real_idle = result.idle;
					$this.data("timer", real_idle);
					idle = real_idle;
					if (idle >= LogoutTime) {
						$this.autoLogout('logout')
					}
					
				}));
			}
	
			if ($(settings.keepAliveSelector).length){
				
			} else {
				
					if (idle >= (parts * 8) + 2) {
						remaining = LogoutTime - idle;
						$noticeareaIdle.stop(true, true).html("You will be automaticaly logged out in " + (remaining) + " seconds").fadeIn(1000)
					} else {
						$noticeareaIdle.stop(true, true).fadeOut(1000)
					}

					if (idle >= (parts * 9) + 2) {
						remaining = LogoutTime - idle;
						$this.stop(true, true).fadeIn(1000).find(".timer").html("You will be automatically logged out in " + (remaining) + " seconds");
					} else {
						$this.stop(true, true).fadeOut(1000);
					}

					var remaining = (LogoutTime - idle);
					var remain_p = (remaining / LogoutTime) * 100

					if (remain_p < 30) {

						var new_p = (remain_p / 30)
						new_p = 1 - new_p;

						if (new_p > 0.8)    this.css("background-color", "rgba(250, 250, 250, " + new_p + ")");
					}
				
				
			}
			
		

	}
});

$(document).on('click', '#view-log .record', function (e) {
	var $this = $(this);
	var $clicked = $(e.target).closest("tr.record");
	var active = true;

	if ($this.hasClass("active") && $clicked) active = false;

	$("#view-log .record.active").removeClass("active");
	if (active) {
		$this.addClass("active");
	}

	var show = $("#view-log .record.active").attr("data-log-id");

	$("#view-log .log-record-details").hide();
	$("#view-log .log-record-details[data-log-id='" + show + "']").show();

});

var uID = "{{ _user['ID'] }}";
var jScrollPaneOptions = {
	showArrows      : false,
	maintainPosition: false
};
var jScrollPaneOptionsMP = {
	showArrows      : false,
	maintainPosition: true
};


function updatetimerlist(d, page_size) {
	//console.log(d);
var highlightfrom = 0.7;

	// d = jQuery.parseJSON(d);

	if (!d || !typeof d == 'object') {
		return false;
	}
	$("#displaylogout").autoLogout('resetTimer');

	var data = d['timer'];
	var page = d['page'];
	var models = d['models'];

	if (d['notifications']) {
		$("#notice-area").jqotesub($("#template-notifications"), d['notifications']['footer']);
		$("#message-icon-area").jqotesub($("#template-notifications-messages"), d['notifications']['messages']);
		
		$("#user-loggedin-list").jqotesub($("#template-notifications-users"), d['notifications']);
		
	}

	var pageSize = (page && page['size']) ? page['size'] : page_size;

	if (data) {
		var highlight = "";
		if (page['time'] > highlightfrom) highlight = 'style="color: red;"';
	var th = '<tr class="heading" style="background-color: #fdf5ce;"><td >' + page['page'] + ' : <span class="g">Size: ' + file_size(pageSize) + '</span></td><td class="s g"' + highlight + '>' + page['time'] + '</td></tr>', thm;
	if (models) {
		thm = $("#template-timers-tr-models").jqote(models, "*");
	} else {
		thm = "";
	}

	$("#systemTimers").prepend(th + $("#template-timers-tr").jqote(data, "*") + thm);


}

}
function scrolling(api) {

	$(document).off('mousewheel').on('mousewheel',function (event, delta, deltaX, deltaY) {
		if ($(event.target).closest(".modal").length || $(event.target).closest(".jspScrollable").length || $(event.target).closest(".select2-container").length) {
		} else {
			if (deltaY < 0) {
				deltaY = -deltaY;
			} else {
				deltaY = -deltaY;
			}

			deltaY = deltaY * 30;

			api.scrollByY(deltaY, false);
		}

	}).on('keydown', function (e) {
//				console.log(api.getContentHeight);

			if ($(e.target).closest(".select2-container").length) {
			} else {

				var keyboardSpeed = 28, scrollPagePercent = .8;

				switch (e.keyCode) {
					case 34: // page down
						api.scrollByY(300, false);
						break;
					case 33: // page up
						api.scrollByY(-300, false);
						break;

				}
			}
		});

}
function file_size(size) {
	if (!size) {
		return 0;
	}
	var origSize = size;
	var units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
	var i = 0;
	while (size >= 1024) {
		size /= 1024;
		++i;
	}

	if (origSize > 1024) {
		size = size.toFixed(1)
	}
	return size + ' ' + units[i];
}
if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function (elt /*, from*/) {
		var len = this.length >>> 0;

		var from = Number(arguments[1]) || 0;
		from = (from < 0) ? Math.ceil(from) : Math.floor(from);
		if (from < 0)
			from += len;

		for (; from < len; from++) {
			if (from in this && this[from] === elt)
				return from;
		}
		return -1;
	};
}
if (!Array.prototype.forEach) {
	Array.prototype.forEach = function (fun /*, thisp*/) {
		var len = this.length;
		if (typeof fun != "function")
			throw new TypeError();

		var thisp = arguments[1];
		for (var i = 0; i < len; i++) {
			if (i in this)
				fun.call(thisp, this[i], i, this);
		}
	};
}
Number.prototype.formatMoney = function (c, d, t) {
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
String.prototype.capitalize = function () {
	return this.charAt(0).toUpperCase() + this.slice(1);
};
function capitaliseFirstLetter(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}
function selectText(element) {
	var doc = document
		, text = element
		, range, selection
		;
	if (doc.body.createTextRange) { //ms
		range = doc.body.createTextRange();
		range.moveToElementText(text);
		range.select();
	} else if (window.getSelection) { //all others
		selection = window.getSelection();
		range = doc.createRange();
		range.selectNodeContents(text);
		selection.removeAllRanges();
		selection.addRange(range);
	}
}
