/*
 * Date: 2012/05/30 - 8:37 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {

	if ($.bbq.getState("details")) {
		getDetails_right();
	}

	scrolling(left_pane);

	$(document).on("click", "#toolbar-stats-link", function (e) {
		if (!$(e.target).closest("#toolbar-stats-pane").get(0)) {
			$("#toolbar-stats-pane").slideToggle(transSpeed);
		}

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
		$.bbq.pushState({details:"section-" + $this.attr("data-id")});
		getDetails_right();
	});
	$(document).on("click", ".pagenr", function () {
		var $this = $(this);
		$.bbq.removeState("details");
		$.bbq.pushState({details:"page-" + $this.attr("data-page")});

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
		$.bbq.pushState({details:"page-" + $this.attr("data-page_nr")});
		getDetails_right();

	});
	$(document).on("click", "#record-details-bottom > article", function () {
		var $this = $(this);
		$.bbq.pushState({"ID":$this.attr("data-id")});
		getDetails();
	});


	$(document).on("click", ".pages .record, .details_record", function () {
		var $this = $(this);
		getDetails_small($this.attr("data-id"));
	});
	$(document).on("dblclick", ".pages .record, .details_record", function (e) {
		e.stopPropagation();
		var $this = $(this);
		$.bbq.pushState({"ID":$this.attr("data-id")});
		getDetails();
	});






	$(document).on('hide', '#ab-details-modal', function () {
		var s = {
			maintain_position:true
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
			"page"     :page,
			"sectionID":section,
			"colourID"   :colour
		};
		for (var i = 0; i < activityRequest.length; i++) activityRequest[i].abort();
		activityRequest.push($.post("/app/ab/save/layout/_page", data, function (response) {
			var s = {
				maintain_position:true
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
			"page"  :page,
			"locked":lockState
		};
		for (var i = 0; i < activityRequest.length; i++) activityRequest[i].abort();
		activityRequest.push($.post("/app/ab/save/layout/_page", data, function (response) {
			var s = {
				maintain_position:true
			};
			load_pages(s);
			getDetails_right();
		}));

		return false;

	});

	$("#record-list-middle").droppable({
		accept   :".pages tr.record",
		greedy   :true,
		tolerance:"pointer",
		over     :function (event, ui) {
			var $this = $(this);

			$this.addClass("droppablehover");

		},
		out      :function (event, ui) {
			var $this = $(this);

			$this.removeClass("pagefull droppablehover");

		},
		drop     :function (event, ui) {
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
	listRequest.push($.post("/app/ab/save/layout/_force/", {"pages":pages}, function (data) {
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
	if ($rightsideOver.length){
		var b = $("#rightsideOver .footer").outerHeight();

		//$("#record-list-middle").
		$rightsideOver.css({"bottom": bottomHeight}).find(".scroll-pane").css("bottom",b-42).jScrollPane(jScrollPaneOptions);
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
	return  n.toString() + pd;
}

function getList() {
	var placingID = $("#placingID").val();

	$("#right-area .loadingmask").show();
	$.getData("/app/ab/data/layout/_list", {"placingID":placingID}, function (data) {


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
		opacity    :0.5,
		helper: function(e){
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
		},
		cursorAt   :{left:0, top:0},
		containment:false,
		zIndex     :2710,
		appendTo   :'body',
		//snap:true,
		//snapMode:"outer",
		//revert: 'invalid',
		stop       :function (event, ui) {

		},
		revert     :'invalid'
	});
}
function page_droppable($element) {
	$element.droppable({
		accept   :"tr.record",
		greedy   :true,
		tolerance:"pointer",
		over     :function (event, ui) {
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



				if (limit && limit != "undefined"  && limit != "null") {
					limit = limit.split(",");
					if (limit.indexOf(draggedColour) < 0){
						allowDrop = false;
						reason.push("Booking colour");

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

		},
		out      :function (event, ui) {
			var $this = $(this);
			$this.removeClass("pagefull pagehover ").find(".msgs").html("").stop(true, true).fadeOut();

		},
		drop     :function (event, ui) {
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

			if (limit && limit != "undefined"  && limit != "null") {
				limit = limit.split(",");
				if (limit.indexOf(draggedColour) < 0) {
					allowDrop = false;

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
	$.getData("/app/ab/data/layout/_pages", {"placingID":placingID}, function (data) {


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
	$.getJSON("/app/ab/data/details?r=" + Math.random(), {"ID":ID}, function (data) {

		$(".record.active").removeClass("active");
		$(".record[data-ID='" + ID + "']").addClass("active");
		$('#record-details-bottom').jqotesub($("#template-details-bottom"), data).stop(true, true).fadeTo(transSpeed, 1);

		records_list_resize();
	});
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

		$.getJSON("/app/ab/data/layout/_details_" + section + "?r=" + Math.random(), {"val":ID}, function (data) {

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
	listRequest.push($.post("/app/ab/save/layout/_drop?ID=" + ID, {"page":page}, function (data) {
		data = data['data'];

		$dragged.remove();
		$("#page-" + page).jqotesub($("#template-spreads-page"), data);
		page_droppable($("#page-" + page));
		$("#dummy-bottom div[data-page_nr='" + page + "']").jqotesub($("#template-spreads-bottom-page"), data);
		$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);
		tr_draggable($("#page-" + page));

		if (oldPage) {
			$.getJSON("/app/ab/data/layout/_page?r=" + Math.random(), {"page":oldPage}, function (data) {
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
	listRequest.push($.post("/app/ab/save/layout/_drop?ID=" + ID, {"page":"remove"}, function (data) {
		data = data['data'];
		$dragged.remove();

		getList();

		if (oldPage) {
			$.getJSON("/app/ab/data/layout/_page?r=" + Math.random(), {"page":oldPage}, function (data) {
				data = data['data'];

				$("#page-" + oldPage).jqotesub($("#template-spreads-page"), data);
				$("#dummy-bottom div[data-page_nr='" + oldPage + "']").jqotesub($("#template-spreads-bottom-page"), data);
				tr_draggable($("#page-" + oldPage));
			});
		}
	}));
}
