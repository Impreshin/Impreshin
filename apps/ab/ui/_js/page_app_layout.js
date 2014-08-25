var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {

	if ($.bbq.getState("details")) {
		getDetails_right();
	}

	if ($.bbq.getState("page")) {
		$("#modal-tetris").modal("show");
		loadTetris()
	}

	scrolling(left_pane);

	$(document).on("click", "#toolbar-stats-link", function (e) {
		if (!$(e.target).closest("#toolbar-stats-pane").get(0)) {
			$("#toolbar-stats-pane").slideToggle(transSpeed);
		}
	});
	$(document).on("click", ".open-tetris", function (e) {
		$("#modal-tetris").html("").modal("show");
		$.bbq.pushState({page: $(this).attr("data-page")});
		loadTetris();
	});
	$(document).on('hide', '#modal-tetris', function () {
		$.bbq.removeState("page");
		getList();
		load_pages();
		getDetails_right();
	});
	
	$(document).on('mouseenter', '#page-area-tetris > article, #list-tetris > article', function () {
		var $this = $(this);
		
		var str = "";
			str += '<h1>'+$this.attr("data-client")+'<small class="pull-right" style="font-size:12px; margin-right:10px; padding-top:10px;">('+$this.attr("data-cm")+' x '+$this.attr("data-col")+')</small></h1>';
		
		
			if ($this.attr("data-img")){
				img  = '<img src="/app/ab/thumb/material/'+$this.attr("data-id")+'/'+$this.attr("data-img")+'?w=260&h=260&c=false&s='+$this.attr("data-img")+'"/>';
			} else {
				img = "no material";
			}
		str += "<div class='preview'>"+ img + "</div>";
		
		$("#tetris-details").html(str);
	});
	$(document).on('mouseleave', '#page-area-tetris > article, #list-tetris > article', function () {
		
		$("#tetris-details").html("");
	});
	
	
	

	$(document).on("scroll", "#left-area .scroll-pane", function () {
		visible_pages();
	});

	$(document).on("change", "#placingID", function () {
		getList();

	});

	$(document).on("click", "#reload-btn", function () {
		getList();
		load_pages();
		getDetails_right();
	});

	$(document).on("click", ".page_section", function () {
		var $this = $(this);
		$.bbq.removeState("details");
		$.bbq.pushState({details: "section-" + $this.attr("data-id")});
		getDetails_right();
	});
	$(document).on("click", ".pagenr", function () {
		var $this = $(this);
		$.bbq.removeState("details");
		$.bbq.pushState({details: "page-" + $this.attr("data-page")});

		var page = "#dummy-area .pages[data-page='" + $this.attr("data-page") + "']";
		if ($("#dummy-area .pages[data-page='" + $this.attr("data-page") + "']").length) {
			left_pane.scrollToElement(page, true, true);
		}


		getDetails_right();
	});

	$(document).on("click", ".close-right-over", function () {
		showList();
	});

	$(document).on("click", "#dummy-bottom .page", function () {
		var $this = $(this);
		var page = "#dummy-area .pages[data-page='" + $this.attr("data-page_nr") + "']";
		if ($("#dummy-area .pages[data-page='" + $this.attr("data-page_nr") + "']").length) {
			left_pane.scrollToElement(page, true, true);
		}

	});
	$(document).on("click", "#dummy-bottom .page.visible", function () {
		var $this = $(this);

		$.bbq.removeState("details");
		$.bbq.pushState({details: "page-" + $this.attr("data-page_nr")});
		getDetails_right();

	});
	$(document).on("click", "#record-details-bottom > article", function () {
		var $this = $(this);
		$.bbq.pushState({"ID": $this.attr("data-id")});
		getDetails();
	});


	$(document).on("click", ".pages .record, .details_record", function () {
		var $this = $(this);
		getDetails_small($this.attr("data-id"));
	});
	$(document).on("dblclick", ".pages .record, .details_record", function (e) {
		e.stopPropagation();
		var $this = $(this);
		$.bbq.pushState({"ID": $this.attr("data-id")});
		getDetails();
	});






	$(document).on('hide', '#ab-details-modal', function () {
		var s = {
			maintain_position: true
		};
		//getList(s);
	});

	$(document).on('submit', '#pages_settings_form', function (e) {
		e.preventDefault();
		var $this = $(this);
		var page = $.bbq.getState("details");
		page = (page.match(/\d+/));
		page = page.join("");

		var section = $("#sectionID", $this).val();
		var colour = $("#colourID", $this).val();

		var data = {
			"page": page, "sectionID": section, "colourID": colour
		};
		for (var i = 0; i < activityRequest.length; i++) activityRequest[i].abort();
		activityRequest.push($.post("/app/ab/save/layout/_page", data, function (response) {
			var s = {
				maintain_position: true
			};
			load_pages(s);
			getDetails_right();
		}));

		return false;
	});
	$(document).on('change', '#pages_settings_form select', function () {
		$(this).closest("form").trigger("submit");
	});

	$(document).on('click', '#pages_settings_form #colours button', function () {
		$(this).closest("form").trigger("submit");
	});

	$(document).on('click', '#pages_settings_form #lock-btn', function (e) {
		e.preventDefault();
		var $this = $(this);
		$this.tooltip("hide");
		var lockState = $this.attr("data-value");

		var page = $.bbq.getState("details");
		page = (page.match(/\d+/));
		page = page.join("");
		var data = {
			"page": page, "locked": lockState
		};
		for (var i = 0; i < activityRequest.length; i++) activityRequest[i].abort();
		activityRequest.push($.post("/app/ab/save/layout/_page", data, function (response) {
			var s = {
				maintain_position: true
			};
			load_pages(s);
			getDetails_right();
		}));

		return false;

	});

	$("#record-list-middle").droppable({
		accept: ".pages tr.record", greedy: true, tolerance: "pointer", over: function (event, ui) {
			var $this = $(this);

			$this.addClass("droppablehover");

		}, out: function (event, ui) {
			var $this = $(this);

			$this.removeClass("pagefull droppablehover");

		}, drop: function (event, ui) {
			var $this = $(this);
			var $page = $(this).parent();
			var $dragged = $(ui.draggable);
			$this.removeClass("pagefull droppablehover");

			remove($dragged.attr("data-id"), $dragged);
		}
	});

	$(document).on("click", "#force_pages_btns button", function () {
		var $force_pages = $("#force_pages"), $this = $(this);
		var dir = $this.attr("data-direction"), pages = $force_pages.attr("data-pages");

		if ($force_pages.val() != "auto") {
			pages = $force_pages.val();
		}

		if (dir == "up") {
			$force_pages.val($("option[value='" + pages + "']", $force_pages).next().attr("value"));
		} else if (dir == "down") {
			$force_pages.val($("option[value='" + pages + "']", $force_pages).prev().attr("value"));
		}

		save_forced_pages();
	});

	$(document).on("change", "#force_pages", function () {
		save_forced_pages();
	});

	getList();
	load_pages();
	if ($.bbq.getState("ID")) {
		getDetails();
		getDetails_small($.bbq.getState("ID"));
	}
});
function save_forced_pages() {
	var pages = $("#force_pages").val();

	$("#left-area .loadingmask").show();
	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.post("/app/ab/save/layout/_force/", {"pages": pages}, function (data) {
		load_pages();
		getList();
	}));

}
function dummy_resize(settings) {
	$("#dummy-area").css("bottom", $("#dummy-bottom").outerHeight());
	if (settings && settings.maintain_position) {
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptionsMP);
	} else {
		left_pane.reinitialise();
	}

	//visible_pages();

}
function records_list_resize() {
	var bottomHeight = $("#record-details-bottom").outerHeight();
	$("#record-list-middle").css("bottom", bottomHeight);
	var $rightsideOver = $("#rightsideOver");
	if ($rightsideOver.length) {
		var b = $("#rightsideOver .footer").outerHeight();

		//$("#record-list-middle").
		$rightsideOver.css({"bottom": bottomHeight}).find(".scroll-pane").css("bottom", b - 42).jScrollPane(jScrollPaneOptions);
	}

	right_pane.reinitialise();

}
function PadDigits(n, totalDigits) {
	n = n.toString();
	var pd = '';
	if (totalDigits > n.length) {
		for (i = 0; i < (totalDigits - n.length); i++) {
			pd += '&nbsp';
		}
	}
	return n.toString() + pd;
}

function getList() {
	var placingID = $("#placingID").val();

	$("#right-area .loadingmask").show();
	$.getData("/app/ab/data/layout/_list", {"placingID": placingID}, function (data) {


		var placings = $.map(data['placing'], function (record) {
			var selected = "";
			if (data['placingID'] == record['ID']) {
				selected = 'selected="selected"';
			} else {
				selected = "";
			}
			var padding = "";

			var recordcount = record['recordCount'];
			if (recordcount == '0') {
				padding = "";
			} else {

				padding = ' (' + record['recordCount'] + ')';
			}
			padding = PadDigits(padding, 9);

			return '<option value="' + record['ID'] + '" ' + selected + '>' + padding + " " + record['placing'] + '</option>';
		});
		placings = placings.join("");

		$("#placingID").html(placings);

		var $recordsList = $("#record-list tbody");
		if (data['records'][0]) {
			$recordsList.jqotesub($("#template-records-list"), data['records']);

			tr_draggable($recordsList);

		} else {
			$recordsList.html('<tr><td class="c no-records">No Records Found</td></tr>')
		}

		$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);

		$force_pages = $("#force_pages").val("auto").attr("data-pages", data['stats']['loading']['pages']);
		if (data['stats']['loading']['forced']) {
			$force_pages.val(data['stats']['loading']['pages']);
		}

		$("#right-area .loadingmask").fadeOut(transSpeed);
		records_list_resize();
	}, "list");
}

function tr_draggable($parent) {

	$("tr.record.dragable", $parent).draggable({
		opacity: 0.5, helper: function (e) {
			var $target = $(e.target).closest("tr.record");
			var cm = $target.attr("data-cm");
			var col = $target.attr("data-col");
			var img = $target.attr("data-image");

			var width = colSize * col, offsetX = width / 2;
			var height = cmSize * cm, offsetY = height / 2;

			var str = "";
			str += '<div class="dragablethingy" style="width: ' + width + 'px; height: ' + height + 'px; margin-left: -' + offsetX + 'px; margin-top: -' + offsetY + 'px;">';
			if (img) {
				str += '<img src="' + img + '&w=' + width + '&h=' + height + '" />';
			}
			str += '</div>';

			return str;
		}, cursorAt: {left: 0, top: 0}, containment: false, zIndex: 2710, appendTo: 'body', //snap:true,
		//snapMode:"outer",
		//revert: 'invalid',
		stop: function (event, ui) {

		}, revert: 'invalid'
	});
}
function page_droppable($element) {
	$element.droppable({
		accept: "tr.record", greedy: true, tolerance: "pointer", over: function (event, ui) {
			var $this = $(this);
			var $page = $this.find("article");
			var $dragged = ui.draggable;

			if ($this.hasClass("dontDrop") == true) {
				$this.addClass("pagefull");

			} else {
				var pageLoadingCM = $page.attr("data-cm");
				var recordCM = $dragged.attr("data-cm") * $dragged.attr("data-col");
				var reason = [];
				var allowDrop = true;

				if ((Number(pageLoadingCM) + Number(recordCM) > colcmAv) && $dragged.attr("data-page") != $this.attr("data-page")) {
					allowDrop = false;
					reason.push("Not enough space");
				}

				var limit = $page.attr("data-limit");
				var draggedColour = $dragged.attr("data-colour");



				if (limit && limit != "undefined" && limit != "null") {
					limit = limit.split(",");
					if (draggedColour == 'null' || draggedColour == 'undefined') {

					} else {
						if (limit.indexOf(draggedColour) < 0) {
							allowDrop = false;
							reason.push("Booking colour");

						}
					}

				}

				if ($this.hasClass("locked")) {
					allowDrop = false;
					reason = [];
					reason.push("Page Locked");
				}

				if (allowDrop) {
					$this.addClass("pagehover");
				} else {
					$this.addClass("pagefull")
				}
				reason = reason.join("<br>");

				var $msgs = $this.find(".msgs");

				if (reason) {
					$msgs.html(reason).stop(true, true).fadeIn();
				} else {
					$msgs.html("").stop(true, true).fadeOut();
				}

			}

		}, out: function (event, ui) {
			var $this = $(this);
			$this.removeClass("pagefull pagehover ").find(".msgs").html("").stop(true, true).fadeOut();

		}, drop: function (event, ui) {
			var $this = $(this);
			var $page = $this.find("article");
			var $dragged = $(ui.draggable);

			var pageLoadingCM = $page.attr("data-cm");
			var recordCM = $dragged.attr("data-cm") * $dragged.attr("data-col");
			var allowDrop = true;
			if ((Number(pageLoadingCM) + Number(recordCM) > colcmAv) && $dragged.attr("data-page") != $this.attr("data-page")) {
				allowDrop = false;
			}

			var limit = $page.attr("data-limit");
			var draggedColour = $dragged.attr("data-colour");


			if (limit && limit != "undefined" && limit != "null") {
				limit = limit.split(",");
				if (draggedColour == 'null' || draggedColour == 'undefined') {

				} else {
					//console.log("hmm");
					if (limit.indexOf(draggedColour) < 0) {
						allowDrop = false;

					}
				}


			}
			if ($this.hasClass("locked")) {
				allowDrop = false;
			}


			if (allowDrop) {
				if ($dragged.attr("data-page") != $this.attr("data-page")) {
					drop($dragged.attr("data-id"), $this.attr("data-page"), $dragged);
				}

			}
			$this.removeClass("pagefull pagehover").find(".msgs").html("").stop(true, true).fadeOut();

		}
	});
}
function load_pages(settings) {
	var placingID = $("#placingID").val();

	$("#left-area .loadingmask").show();
	$.getData("/app/ab/data/layout/_pages", {"placingID": placingID}, function (data) {


		var $recordsList = $("#pages-area");
		if (data['spreads'][0]) {
			//console.log(data['spreads']);
			$recordsList.jqotesub($("#template-spreads"), data['spreads']);
			$("#dummy-bottom").jqotesub($("#template-spreads-bottom"), data['spreads']);

		} else {
			$recordsList.html('<tr><td class="c no-records">No Records Found</td></tr>')
		}
		$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);

		tr_draggable($("#pages-area"));
		page_droppable($("#pages-area .pages"));

		$force_pages = $("#force_pages").val("auto").attr("data-pages", data['stats']['loading']['pages']);
		if (data['stats']['loading']['forced']) {
			$force_pages.val(data['stats']['loading']['pages']);
		}

		$("#left-area .loadingmask").fadeOut(transSpeed);
		dummy_resize(settings);
	}, "data");
}

function visible_pages() {
	var t = new Array();
	$("#dummy-bottom .page.visible").removeClass("visible");
	$("#dummy-area .pages:visible").each(function () {
		var $this = $(this);
		if (isScrolledIntoView($this)) {
			$("#dummy-bottom .page[data-page_nr='" + $this.attr("data-page") + "']").addClass("visible");
			t.push($this.attr("data-page"));
		}
	});
//	console.log(t);

}
function isScrolledIntoView(elem) {
	var docViewTop = $(window).scrollTop();
	var docViewBottom = docViewTop + $(window).height();

	var elemTop = $(elem).offset().top;
	var elemBottom = elemTop + $(elem).height();

	return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom) && (elemBottom <= docViewBottom) && (elemTop >= docViewTop) );
}
function getDetails_small(ID) {
	$('#record-details-bottom').stop(true, true).fadeTo(transSpeed, 0.5);
	$.getData("/app/ab/data/details?r=" + Math.random(), {"ID": ID}, function (data) {

		$(".record.active").removeClass("active");
		$(".record[data-ID='" + ID + "']").addClass("active");
		$('#record-details-bottom').jqotesub($("#template-details-bottom"), data).stop(true, true).fadeTo(transSpeed, 1);

		records_list_resize();
	}, "details");
}
function getDetails_right() {
	var section = $.bbq.getState("details");
	var ID = section;
	if (ID) {
		ID = ID.replace(/page-/, "");
		ID = ID.replace(/section-/, "");

		var str = "-" + ID;
		section = section.replace(str, "");
		var $rightsideOver = $('#rightsideOver');
		$rightsideOver.html("").stop(true, true).fadeIn(transSpeed);
		$("#right-area .loadingmask").show();

		$.getJSON("/app/ab/data/layout/_details_" + section + "?r=" + Math.random(), {"val": ID}, function (data) {

			data = data['data'];
			switch (section) {
				case "page":
					$rightsideOver.jqotesub($("#template-right-page"), data);

					var $recordsList = $("#page-booking-list tbody");
					if (data['records'][0]) {
						$recordsList.jqotesub($("#template-records-list"), data['records']);

						if (data['locked'] != '1') {
							tr_draggable($recordsList);
						}

					} else {
						$recordsList.html('<tr><td class="c no-records" colspan="4">No Records Found</td></tr>')
					}

					break;
				case "section":
					$rightsideOver.jqotesub($("#template-right-section"), data);
					break;
			}

			records_list_resize();
			$("#right-area .loadingmask").fadeOut(transSpeed);
		});
	}

}

function showList() {
	$.bbq.removeState("details");
	$("#rightsideOver").stop(true, true).fadeOut(transSpeed);
	$("#right-area .loadingmask").fadeOut(transSpeed);

}

function drop(ID, page, $dragged) {
	var oldPage = $($dragged).attr("data-page");
	oldPage = oldPage != "undefined" ? oldPage : "";

	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.post("/app/ab/save/layout/_drop?ID=" + ID, {"page": page}, function (data) {
		data = data['data'];

		$dragged.remove();
		$("#page-" + page).jqotesub($("#template-spreads-page"), data);
		page_droppable($("#page-" + page));
		$("#dummy-bottom div[data-page_nr='" + page + "']").jqotesub($("#template-spreads-bottom-page"), data);
		$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);
		tr_draggable($("#page-" + page));

		if (oldPage) {
			$.getJSON("/app/ab/data/layout/_page?r=" + Math.random(), {"page": oldPage}, function (data) {
				data = data['data'];

				$("#page-" + oldPage).jqotesub($("#template-spreads-page"), data);
				page_droppable($("#page-" + oldPage));
				$("#dummy-bottom div[data-page_nr='" + oldPage + "']").jqotesub($("#template-spreads-bottom-page"), data);
				tr_draggable($("#page-" + oldPage));
			});
		}
		getDetails_right();
	}));

}
function remove(ID, $dragged) {
	var oldPage = $($dragged).attr("data-page");
	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.post("/app/ab/save/layout/_drop?ID=" + ID, {"page": "remove"}, function (data) {
		data = data['data'];
		$dragged.remove();

		getList();

		if (oldPage) {
			$.getJSON("/app/ab/data/layout/_page?r=" + Math.random(), {"page": oldPage}, function (data) {
				data = data['data'];

				$("#page-" + oldPage).jqotesub($("#template-spreads-page"), data);
				$("#dummy-bottom div[data-page_nr='" + oldPage + "']").jqotesub($("#template-spreads-bottom-page"), data);
				tr_draggable($("#page-" + oldPage));
			});
		}
	}));
}
function loadTetris() {
	$("#modal-tetris").css("z-index",1050);
	var ID = $.bbq.getState("page");
	if (ID) {

		$.getJSON("/app/ab/data/layout/_details_page?r=" + Math.random(), {"val": ID}, function (data) {
			data = data['data'];

			$("#modal-tetris").jqotesub($("#template-tetris"), data);
			dragTetris();
			doLayout();

			var parentPos = null;


			$("#list-tetris, #page-area-tetris").sortable({
				connectWith: ".connected",
				appendTo: '#modal-tetris',
				stack: "#page-area-tetris article",
				revert: 0,
				tolerance: 'pointer',
				items:'article:not(.locked)',
				helper: function (e) {
					var $target = $(e.target).closest("article");
					
					
					
					//console.log($target)
					var ID = $target.attr("data-id");
					var cm = $target.attr("data-cm");
					var col = $target.attr("data-col");
					var img = $target.attr("data-img");
					var client = $target.attr("data-client");

					//var width = colSize * col, 
					//var height = cmSize * cm, 

					var height = cm * r_h;
					var width = col * r_w;

					var offsetX = width / 2;
					var offsetY = height / 2;

					if (img){
						img  = '<img src="/app/ab/thumb/material/'+ID+'/'+img+'?w='+width+'&h='+height+'&c=false&s='+img+'"/>';
					} else {
						img = "<div class='tetris-no-img' style='height:"+height+"px'>"+client + "<br><span class=' s'>("+cm+"x"+col+")</span></div>";
					}

					
					

					var str = "";
					str += '<article class="dragablethingy" style="width: ' + width + 'px; height: ' + height + 'px; margin-left: -' + offsetX + 'px; margin-top: -' + offsetY + 'px; background-color: #ccc; position:relative; ">';

					str += img;
					str += '</article>';

					

					return str;
				},
				update: function () {

					//dragTetris();
					//doLayout();
				},
				start: function (e, ui) {
					//$("#page-area > div").css('z-index', '1');
					//doLayout();
					//console.log(ui)
				},
				stop: function (event, ui) {
					//$(ui.helper).clone(true).removeClass('box ui-draggable ui-draggable-dragging').addClass('box-clone').appendTo('body');
					//$(this).sortable('cancel');

					var $this = $(ui.item);
					var ID = $this.attr("data-id");
					var cm = $this.attr("data-cm") ;
					var col = $this.attr("data-col") ;
					var colour = $this.attr("data-colour");
					var img = $this.attr("data-img");
					var client = $this.attr("data-client");

					var pos = ui.position;


					

					var width = (col* r_w) ;
					var height = (cm* r_h) ;
					
					

					if (img){
						img  = '<img src="/app/ab/thumb/material/'+ID+'/'+img+'?w='+width+'&h='+height+'&c=false&s='+img+'"/>';
					} else {
						img = "<div class='tetris-no-img' style='height:"+height+"px'>"+client + "<br><span class=' s'>("+cm+"x"+col+")</span></div>";
					}
					

					$this.css({"height": height, "width": width, "background-color": "#ccc"}).html(img);

					

					//console.info("r_h:"+r_h+" | r_w:"+r_w)
					//console.info("h:"+cm+" | w:"+col)
					//console.log(pos);


					var oT = 0, oL = 0;

					var containerPos = $("#page-area-tetris").offset();
					var modalPos = $("#modal-tetris").offset();

					containerPos.top = containerPos.top - modalPos.top
					containerPos.left = containerPos.left - modalPos.left
					

					//console.log(modalPos);
					//console.log(containerPos);
					//console.log(pos);






					oT = ((pos.top - containerPos.top) - (height / 2)) / r_h;
					oL = ((pos.left - containerPos.left) - (width / 2)) / r_w;

					oT = Math.round(oT)
					oL = Math.round(oL)


					$this.attr("data-offset-col", oL).attr("data-offset-cm", oT);
					writeChanges($this)

					//console.log("top: " + oT);
					//console.log("left: "+ oL);

					doLayout();
				},
				change: function () {

					//doLayout();
				},


				cursorAt: {left: 0, top: 0}
			}).disableSelection();


			$("#page-area-tetris").css({"height": page_height, "width": page_width});
			$("#page-container-tetris").css({"height": page_height, "width": page_width});

			$("#grid-area-tetris").html(drawGrid(columnsav, cmav)).find(".cell").css({
				"width": r_w - 1,
				"height": r_h - 1
			});
			

		});
	}

}



var columnsav = 8;
var cmav = 39;
var pagewidth = 262;


var page_width = 255;
var page_height = (((page_width / ( pagewidth / 100 )) * cmav) / 10);

var r_h = page_height / cmav;
r_h = Math.round(r_h);

var r_w = page_width / columnsav;
r_w = Math.round(r_w);

page_height = r_h * cmav;
page_width = r_w * columnsav;










function dragTetris() {
	$("#page-area-tetris > div").draggable({
		grid: [r_w, r_h], //containment: "#page-area,.list ", //refreshPositions: true,
		stack: "div",

		stop: function (event, ui) {
			var pos = ui.position;
			var col = pos.left / r_w;
			var cm = pos.top / r_h;

			$(ui.helper).attr("data-offset-col", col).attr("data-offset-cm", cm);
			writeChanges($(ui.helper))
			doLayout();
		}


	});
}


function doLayout() {
	$("#page-area-tetris article.error").removeClass("error");

	var base_zIndex = (columnsav * cmav) + 10
	$("#page-area-tetris > article").each(function () {
		var $this = $(this);

		var size = $this.attr("data-col") * $this.attr("data-cm");
		var zIndex = base_zIndex - size;
		//console.log(zIndex)


		var offset_t = $this.attr("data-offset-cm") * r_h;
		var offset_l = $this.attr("data-offset-col") * r_w;

		var cm = $this.attr("data-cm");
		var col = $this.attr("data-col");
		var client = $this.attr("data-client");
		


		var height = cm * r_h;
		var width = col * r_w;




		if (($this.attr("data-offset-cm") * 1) + (cm * 1) > cmav) $this.addClass("error")
		if (($this.attr("data-offset-col") * 1) + (col * 1) > columnsav) $this.addClass("error")


		var ID = $this.attr("data-id");
		var img = $this.attr("data-img");

		



		


		if (img){
			img  = '<img src="/app/ab/thumb/material/'+ID+'/'+img+'?w='+width+'&h='+height+'&c=false&s='+img+'"/>';
		} else {
			img = "<div class='tetris-no-img' style='height:"+height+"px'>"+client + "<br><span class='s'>("+cm+"x"+col+")</span></div>";
		}

		
		



		$this.css({
			top: offset_t,
			left: offset_l,
			position: "absolute",
			"width": width,
			"height": height,
			"background-color": "#ccc",
			"z-index": zIndex
		}).html(img);
		//console.log(offset_t+" | "+offset_l);
	});

	var overlaps = $('#page-area-tetris > article').overlaps();

	$.each(overlaps, function () {
		$(this).addClass("error");
	});



}

function writeChanges(element) {

	var offpage = false;
	var save = true;
	var cm = element.attr("data-cm");
	var col = element.attr("data-col");

	if ((element.attr("data-offset-cm") * 1) > cmav) {
		offpage = true
		save = false;
	}
	if ((element.attr("data-offset-col") * 1) > columnsav) {
		offpage = true
		save = false;
	}

	if ((element.attr("data-offset-cm") * 1 + (cm * 1)) > cmav) {
		save = false;
	}
	if ((element.attr("data-offset-col") * 1 + (col * 1)) > columnsav) {
		save = false;
	}
	if ((element.attr("data-offset-cm") * 1 + (cm * 1)) < 0) {
		save = false;
	}
	if ((element.attr("data-offset-col") * 1 + (col * 1)) < 0) {
		save = false;
	}

	var d = {
		"ID": element.attr("data-id"),
		"x_offset": offpage ? null : element.attr("data-offset-col"),
		"y_offset": offpage ? null : element.attr("data-offset-cm"),
		"offpage": offpage
	}
	//console.log(d);
	//console.log(save);
	//console.log(offpage);

	var _class = "";
	var tex = "";
	if (save==true || offpage ==true) {
		$.post("/app/ab/save/layout/_tetris?ID=" + d['ID'], d, function (data) {
			data = data['data'];

			if (offpage) {

				loadTetris()
			}
			
			
		});
	}
	
}


