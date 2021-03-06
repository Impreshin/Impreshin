var pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var api = pane.data("jsp");
$(document).ready(function () {
	if ($.bbq.getState("uploadPage")){
		openPDFupload()
	}
	
	$(document).on("click","#btn-page-upload",function(){

		$.bbq.pushState({"uploadPage":"true"})
		openPDFupload()
	});
	$(document).on("hide","#modal-upload-page",function(){

		$.bbq.removeState("uploadPage")
		
	});
	$(document).on("click","#modal-upload-page .pages",function(){
		var $this = $(this);
		$.bbq.pushState({"uploadPage":$this.attr("data-page")})
		if ($.bbq.getState("uploadPage")!="true"){
			openPDFuploadPage($.bbq.getState("uploadPage"));
		}
	});
	
	function openPDFupload(){

		$("#modal-upload-page").html("<div style='width:100%; height:440px;'><img src='/ui/_images/loading-wide.gif' style='margin-top:200px;margin-left:356px;'/></div>").modal("show");
		
		
		$.getData("/app/ab/data/layout/_pages", {}, function (data) {


			$("#modal-upload-page").jqotesub($("#template-upload-page-pdf"), data);

			if ($.bbq.getState("uploadPage")!="true"){
				openPDFuploadPage($.bbq.getState("uploadPage"));
			}

		}, "data-pages");
	}
	function openPDFuploadPage(page){

		$("#modal-upload-page #side-pane").html("");
		$("#modal-upload-page .pages.active").removeClass("active")
		$.getData("/app/ab/data/layout/_details_page", {"val":page}, function (data) {

			
			$("#modal-upload-page .pages[data-page='"+data.page+"']").addClass("active")

			$("#modal-upload-page #side-pane").jqotesub($("#template-upload-page-pdf-side"), data);

			

			set_upload(data)

		}, "data-pages-page");
	}
	function set_upload(data) {
		$("#progress-area .progress .bar").css("width", "0%");
		$("#progress-area .span3.l").html("");
		
		
		
		var folder = "../pf/"+data['cID'] + "/" + data['pID'] + "/" + data['dID'] + "/";

		
			var uploader = new plupload.Uploader({
				runtimes      :'html5,gears,flash,silverlight',
				browse_button :'upload-page-pdf',
	//container          :'container',
				max_file_size :'200mb',
				max_file_count:1,
				chunk_size    :"2MB",
				url           :'/app/ab/upload/?folder=' + folder,
	
				flash_swf_url      :'/ui/plupload/js/plupload.flash.swf',
				silverlight_xap_url:'/ui/plupload/js/plupload.silverlight.xap',
				filters            :[
	{title:"PDF", extensions:"pdf"}
	//{title:"Zip files", extensions:"zip"}
				],
				unique_names       :true
			});

		

			uploader.bind('Init', function (up, params) {

			});

			uploader.bind('FilesAdded', function (up, files) {
				var fileCount = up.files.length, i = 0, ids = $.map(up.files, function (item) {
					return item.id;
				});

				for (i = 0; i < fileCount; i++) {
					uploader.removeFile(uploader.getFile(ids[i]));
				}
				i = 0;
				$('#material-file-area-filename').html('<div id="' + files[i].id + '" class="g">Uploading: ' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ')</div>');

				setTimeout(function () {
					$("#progress-area").fadeIn();
					uploader.start();
				}, 100);
				

			});

			uploader.bind('UploadProgress', function (up, file) {
				$("#progress-area .progress .bar").css("width", file.percent + "%");
				$("#progress-area .span3.l").html("Uploading: "+ file.percent + "%");
				
			});

			uploader.bind('UploadComplete', function (up, files) {
				var file = files[0];

			
				
				
				var $img = '<img src="/app/pf/thumb/page/' + data['dID'] + '/'  + data['page'] + '/'+ file.name + '?w=25&h=25&c=true&s=' + file.target_name + '&instantrender=true" alt="">';

				$("#progress-area .span3.l").html($img + "<em class='g'>Rendering</em>");



				// console.log($($img).attr("src"))
				$('#progress-area .span3.l img').load(function () {
					$("#progress-area .span3.l").html($img + file.name);

					$("#progress-area").fadeOut(500, function () {
						$("#material-file-area .progress .bar").css("width", "0%");
					});

					data.filename = file.target_name;
					$.post("/app/ab/save/layout/_upload_page", data, function (response) {
						openPDFupload()
					});
				
				});


				

				
			});

			uploader.init();
		
	}
	
	

	scrolling(api);

	var highlight = $.bbq.getState("highlight");
	highlight = (highlight) ? highlight : "checked";
	var filter = $.bbq.getState("filter");
	filter = (filter) ? filter : "*";

	if ($.bbq.getState("modal") == "settings") {
		$("#settings-modal").modal('show');
	}

	if ($.bbq.getState("highlight")) {
		$("#list-highlight-btns button[data-highlight].active").removeClass("active");
		$("#list-highlight-btns button[data-highlight='" + highlight + "']").addClass("active");
	}
	if ($.bbq.getState("filter")) {
		$("#list-filter-btns button[data-filter].active").removeClass("active");
		$("#list-filter-btns button[data-filter='" + filter + "']").addClass("active");
	}

	if ($.bbq.getState("groupBy")) {
		$("#record-settings li[data-group-records-by].active").removeClass("active");
		$("#record-settings li[data-group-records-by='" + $.bbq.getState("groupBy") + "']").addClass("active");
	}
	if ($.bbq.getState("orderBy")) {
		$("#record-settings li[data-order-records-by].active").removeClass("active");
		$("#record-settings li[data-order-records-by='" + $.bbq.getState("orderBy") + "']").addClass("active");
	}
	getList();

	$searchform = $("#search-box form");
	$searchbox = $searchform.find(".search-query");
	$(document).bind('keyup', 'ctrl+f', function (e) {
		e.preventDefault();
		$searchform.toggle("slide", { direction:"right" }, 1000, function () {
			if ($(this).is(":visible")) {
				$searchform.find(".search-query").focus();
			} else {
				$searchbox.val("");
				getList();
			}
		});
		return false;
	});

	$(document).on("submit", "#search-box form", function (e) {
		e.preventDefault();
		getList();
	});

	if ($searchbox.val()) {
		$searchform.stop(true, true).show("slide", { direction:"right" }, 1000, function () {
		});
	}

	$(document).on('click', '#search-box-toggle', function (e) {
		$searchform.toggle("slide", { direction:"right" }, 1000, function () {
			if ($(this).is(":visible")) {
				$searchform.find(".search-query").focus();
			} else {
				$searchbox.val("");
				getList();
			}
		});
	});

//$("#whole-area .loadingmask").show();
	$("#pageheader li ul a").click(function () {
		$("#pagecontent").css({"opacity":0.5});
		$("#pageheader li.active").removeClass("active");
		$(this).closest(".nav > li").addClass("active");
		$(this).closest("li").addClass("active");
	});

	$(document).on("click", "#record-settings li[data-group-records-by]", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-settings li[data-group-records-by].active").removeClass("active");
		$this.addClass("active");
		$.bbq.pushState({"groupBy":$this.attr("data-group-records-by")});
		getList();

	});
	$(document).on("click", ".order-btn", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-list .order-btn").removeClass("asc desc");
		//$this.addClass("active");
		$.bbq.pushState({"order":$this.attr("data-col")});
		var s = {
			maintain_position:true
		};
		getList(s);
		$.bbq.removeState("order");

	});
	$(document).on("click", "#record-settings li[data-order-records-by]", function (e) {
		e.preventDefault();
		var $this = $(this);
		$("#record-settings li[data-order-records-by].active").removeClass("active");
		$this.addClass("active");
		$.bbq.pushState({"orderBy":$this.attr("data-order-records-by")});
		getList();

	});
	$(document).on("click", "#list-settings", function (e) {
		e.preventDefault();
		var $this = $(this);

		$this.addClass("active");
		$.bbq.pushState({"modal":"settings"});
		$("#settings-modal").modal('show');

	});
	$(document).on('hide', '#settings-modal', function () {
		$.bbq.removeState("modal");
		$("#list-settings").removeClass("active");
	});

	$(document).on('hide', '#ab-details-modal', function () {
		var s = {
			maintain_position:true
		};
		getList(s);
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
	$(document).on("click", "#list-highlight-btns button, #list-filter-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		var highlight = $("#list-highlight-btns button.active").attr("data-highlight");
		highlight = (highlight) ? highlight : "checked";
		var filter = $("#list-filter-btns button.active").attr("data-filter");
		filter = (filter) ? filter : "*";

		$.bbq.pushState({"highlight":highlight, "filter":filter});
		getList();

	});

	$(document).on('click', '.scrolllinks a', function (e) {
		e.preventDefault();
		var $this = $(this), scrollto = $this.attr("rel");

		api.scrollToElement("[data-heading='" + scrollto + "']", true, true);

	});

	$(document).on("click", "#toolbar-stats-link", function (e) {
		if (!$(e.target).closest("#toolbar-stats-pane").get(0)) {
			$("#toolbar-stats-pane").slideToggle(transSpeed);
		}

	});
	$(document).on("reset", "#settings-modal form", function (e) {
		e.preventDefault();
		if (confirm("Are you sure you want to reset all these settings?")) {
			$("#settings-modal").addClass("loading");
			$.post("/app/ab/save/list_settings/?section=production&reset=columns,group,order", function () {
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
		$.post("/app/ab/save/list_settings/?section=production", {"columns":columns, "group":group, "groupOrder":order}, function () {
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
	//

});

function getList(settings) {

	var ID = $.bbq.getState("ID");
	var group = $.bbq.getState("groupBy");
	group = (group) ? group : "";
	var order = $.bbq.getState("order");
	order = (order) ? order : "";
	var groupOrder = $.bbq.getState("orderBy");
	groupOrder = (groupOrder) ? groupOrder : "";

	var highlight = $("#list-highlight-btns button.active").attr("data-highlight");
	highlight = (highlight) ? highlight : "";
	var filter = $("#list-filter-btns button.active").attr("data-filter");
	filter = (filter) ? filter : "";

	var search = $("#record-search").val();
	search = (search) ? search : "";

	var orderingactive = (order) ? true : false;
	$("#maintoolbar-date").html('Loading...');
	$("#whole-area .loadingmask").show();

	$.getData("/app/ab/data/production/_list", {"group":group, "groupOrder":groupOrder, "highlight":highlight, "filter":filter, "order":order, "search":search}, function (data) {


		var $recordsList = $("#record-list");
		if (data['list'][0]) {
			$recordsList.jqotesub($("#template-records"), data['list']);
		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
		}

		$("#provisional-stats-bar").jqotesub($("#template-provisional-stats-bar"), data);

		

		var order = data['order']['c'];
		$(".order-btn[data-col='" + order + "'] .indicator", $recordsList).show();

		if ($.bbq.getState("ID")) {
			$("#record-list .record.active").removeClass("active");
			$("#record-list .record[data-ID='" + ID + "']").addClass("active");

			var api = $("#whole-area .scroll-pane").data("jsp");
			if ($("#record-list .record[data-ID='" + ID + "']").length) {
				api.scrollToElement("#record-list .record[data-ID='" + ID + "']", false, false);
			}

			if (!$("#ab-details-modal").is(":visible")) {
				getDetails();
			}
		}
		

		scrollwindow(orderingactive,settings);
		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}, "data");

}
function scrollwindow(orderingactive,settings){
	var $scrollpane = $("#whole-area .scroll-pane");
	if (orderingactive) {
		$scrollpane.jScrollPane(jScrollPaneOptionsMP);
	} else {
		if (settings && settings.maintain_position) {
			$scrollpane.jScrollPane(jScrollPaneOptionsMP);
		} else {
			$scrollpane.jScrollPane(jScrollPaneOptions);
		}

	}
	var goto = $.bbq.getState("scrollTo");
	if (goto) {
		if ($("#record-list .record[data-ID='" + goto + "']").length) {
			var api = $scrollpane.data("jsp");
			if ($("#record-list .record[data-ID='" + goto + "']").length && api) {
				api.scrollToElement("#record-list .record[data-ID='" + goto + "']", true, true);
			}

		}
		$.bbq.removeState("scrollTo");
	}
}