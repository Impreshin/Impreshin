/*
 * Date: 2012/05/30 - 8:37 AM
 */
var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {

	if ($.bbq.getState("highlight")) {
		$("#list-highlight-btns button[data-highlight].active").removeClass("active");
		$("#list-highlight-btns button[data-highlight='" + $.bbq.getState("highlight") + "']").addClass("active");
	}
	

	$(document).on("click", "#list-highlight-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		var highlight = $("#list-highlight-btns button.active").attr("data-highlight");

		$.bbq.pushState({"highlight":highlight});
		load_pages();

	});

	$(document).on("click", "#list-zoom-btns button", function (e) {
		e.preventDefault();
		var $this = $(this);

		var zoom = $(this).attr("data-zoom");

		$.bbq.pushState({"zoom":zoom});
		load_pages();

	});

	$(document).on("click", "#reload-btn", function () {
		load_pages();
	});
$(document).on("change", "#dID", function () {
	$.bbq.pushState({"dID":$(this).val()})
		load_pages();
	});

	$(document).on("click", "#dummy-bottom .page", function () {
		var $this = $(this);
		var page = "#dummy-area .pages[data-page='" + $this.attr("data-page_nr") + "']";
		if ($("#dummy-area .pages[data-page='" + $this.attr("data-page_nr") + "']").length) {
			whole_pane.scrollToElement(page, true, true);
		}

	});
	$(document).on("click", ".pages", function () {
		var $this = $(this), ID = $this.attr("data-page");
		$.bbq.pushState({"page": ID});
		getPageDetails();
	});
	$(document).on('hide', '#page-details-modal', function () {
		$.bbq.removeState("page");

	});


	load_pages();
});
function getPageDetails(){
	var ID = $.bbq.getState("page");
	var dID = $("#dID").val();
	$.getData("/app/pf/data/front/_details?r=" + Math.random(), {"page": ID,"dID":dID}, function (data) {

		$("#page-details-modal").jqotesub($("#template-page-details-modal"), data).modal("show");
		set_upload(data.page);
	});
}
function dummy_resize(settings) {
	$("#dummy-area").css("bottom", $("#dummy-bottom").outerHeight());
	if (settings && settings.maintain_position) {
		$("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptionsMP);
	} else {
		whole_pane.reinitialise();
	}

}
function load_pages(settings) {

	var highlight = $("#list-highlight-btns button.active").attr("data-highlight");
	highlight = (highlight) ? highlight : "";
	var placingID = $("#placingID").val();

	var zoom = $.bbq.getState("zoom");
	$.bbq.removeState("zoom");

	var dID = $.bbq.getState("dID");
	highlight = (highlight) ? highlight : "";
	//$.bbq.removeState("dID");

	$("#whole-area .loadingmask").show();
	$("#page-heading-area").html("loading...");
	
	

	$.getData("/app/pf/data/front/_pages", {"highlight":highlight,"zoom":zoom,"dID":dID}, function (data) {


		var $recordsList = $("#pages-area");
		//console.log(data['zoom']);

		var $pl = $("#list-zoom-btns button[data-zoom='+1']");
		var $mi = $("#list-zoom-btns button[data-zoom='-1']");
		if (data.zoom.p){
			$pl.removeAttr("disabled")
		} else {
			$pl.attr("disabled","disabled")
		}
		if (data.zoom.m){
			$mi.removeAttr("disabled")
		} else {
			$mi.attr("disabled","disabled")
		}



		$("#page-heading-area").html(data.date);

		$recordsList.jqotesub($("#template-spreads"), data);
		$("#dummy-bottom").jqotesub($("#template-spreads-bottom"), data['spreads']);

		$('#dID').html("").append( $.map(data.datelist, function(v, i){ return $('<option>', { val: v.ID, text: v.publish_date_display }).attr("data-has",v.has); })).val(data.dID).select2({
			formatResult: dID_options,
			formatSelection: dID_options
		});

		$("#whole-area .loadingmask").fadeOut(transSpeed);
		dummy_resize(settings);
		if ($.bbq.getState("page")) {
			getPageDetails();
		}

	}, "data");
}
function dID_options(data) {
	if (!data.id) return data.text; // optgroup
	if ($(data.element).attr("data-has")=='1'){
		return "* "+data.text;
	} else {
		return "<span class='g'>" + data.text+"</span>";
	}
	
}

function set_upload(data) {
	$("#progress-area").hide();
	$("#progress-area .progress .bar").css("width", "0%");
	$("#progress-area .span1.l").html("");



	var folder = "../pages/"+data['cID'] + "/" + data['pID'] + "/" + data['dID'] + "/";


	var uploader = new plupload.Uploader({
		runtimes      :'html5,gears,flash,silverlight',
		browse_button :'upload-page-pdf',
		//container          :'container',
		max_file_size :'200mb',
		max_file_count:1,
		chunk_size    :"2MB",
		url           :'/app/pf/upload/?folder=' + folder,

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
		$("#progress-area .span1.l").html(""+ file.percent + "%");

	});

	uploader.bind('UploadComplete', function (up, files) {
		var file = files[0];




		var $img = '<img src="/app/pf/thumb/page/' + data['dID'] + '/'  + data['page'] + '/'+ file.name + '?w=25&h=25&c=true&s=' + file.target_name + '&instantrender=true" alt="">';

		$("#progress-area .span1.l").html($img + "<em class='g'>Rendering</em>");



		// console.log($($img).attr("src"))
		$('#progress-area .span1.l img').load(function () {
			$("#progress-area .span1.l").html($img);

			$("#progress-area").fadeOut(500, function () {
				$("#material-file-area .progress .bar").css("width", "0%");
			});

			data.filename = file.target_name;
			$.post("/app/pf/save/front/_upload_page", data, function (response) {
				load_pages()
			});

		});





	});

	uploader.init();

}
	