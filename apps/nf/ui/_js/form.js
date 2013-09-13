/*
 * Date: 2013/02/19 - 2:31 PM
 */

var allowedFileExtentions = [];

$.each(_allowedFiles, function (kk,vv) {
	$.each(vv, function (k, v) {
		$.each(v, function (sk, sv) {
			allowedFileExtentions.push(sv)
		});
	});
});
allowedFileExtentions = allowedFileExtentions.join(",");


//console.log(_allowedFiles); 
//console.log(allowedFileExtentions); 

var max_height = $(window).height() - 190;

var _priority = [
	"low", "1", "2", "3", "4", "high"
];



var text_settings = {
	uiColor           : '#FFFFFF',
	height            : '390px',
	toolbar           : text_toolbar,
	resize_enabled    : false,
	extraPlugins      : 'autogrow,onchange',
	autoGrow_maxHeight: max_height,
	autoGrow_minHeight: 390 > max_height ? 390 : max_height,
	autoGrow_fn       : function () {
		resizeform();
	}
};
var caption_settings = {
	uiColor           : '#FFFFFF',
	height            : '110px',
	toolbar           : text_toolbar,
	removePlugins     : 'elementspath',
	resize_enabled    : false,
	extraPlugins      : 'autogrow',
	autoGrow_minHeight: 110,
	autoGrow_maxHeight: 110 > max_height ? 110 : max_height,
	autoGrow_fn       : function () {
		resizeform();
	}
};
$(document).ready(function () {
	getData();

	$(document).on("click", ".view-record-btn", function () {
		var id = $(this).attr("data-id");
		if (id) {
			$(this).closest(".modal").modal("hide");
			$.bbq.pushState({"ID": id});

			getDetails();
		}

	});
	$(document).on("click", "#btn-stage-checkbox", function () {
		var $this = $(this);
		var $checkbox = $this.find("input");
		$checkbox.prop("checked", !$checkbox.prop("checked"));

		var $btn = $this.closest("form").find("button[type='submit']");
		if ($this.find("input").is(":checked")) {
			$btn.html("Save and stage: "+ $this.attr("data-stage-label"))
		} else {
			$btn.html("Save Record")
		}

	});


	$(document).on("click", "#btn-tools-search", function () {
		var meta = $("form #meta").val();

		meta = encodeURIComponent(meta);
		var w = 800;
		var h = $(window).height();
		var url = 'https://www.google.com/search?q=' + meta;
		console.log(meta);
		window.open(url, '_blank', 'width=' + w + ',height=' + h);
		return false;


	});
	$(document).on("click", "#btn-tools-checklist", function () {
		var categoryID = $("#categoryID").val();

		getChecklistData();


	});
	$(document).on("change", "#categoryID", function () {

		checklistBtn();

	});
	$(document).on("click", "#checklist-container li .label", function () {
		var $this = $(this);
		var $help = $this.parent().find(".help-block");
		var show = false;

		if (!$help.is(":visible")) {
			show = true;
		}
		$("#checklist-container .help-block").hide();

		if (show) {
			$help.show();
		}


	});

	$(document).on("change", "#checklist-container input[type='checkbox']", function () {
		var c = [];
		$("#checklist-container input[type='checkbox']").each(function () {
			var $this = $(this);
			if ($this.is(":checked")) {
				c.push($this.val())
			}
		});

		var checked = c.join(",");

		$("#checklist").val(checked);

	});



	$(document).on("click", "#checklist-container li .item", function () {
		var $this = $(this);



	});

	$(document).on("submit", "#main-form", function (e) {
		e.preventDefault();
		form_submit();
		return false;

	});
	$(document).on("reset", "#main-form", function (e) {
		e.preventDefault();

		getData();
		return false;

	});
	$(document).on("change", "#cm-block", function (e) {
		e.preventDefault();
			
		///console.log("changed");

		var artcm = $(this).height();



		if (artcm > 0) {
			artcm = artcm / 38.461538;
			artcm = Math.ceil(artcm);
		}

		var heading = "";
		if (artcm){
			$("#cm").val(artcm);
			heading = heading + '<strong>'+ artcm + "</strong> <span class=' g'>cm</span>";
		} else {
			$("#cm").val("0");
		}
		
		var words = $(this).text();
		//console.log(words);

		var count = words.split(/\s+/).length - 1;

		//console.log(count);

		if (count) {
			
			heading = heading + " <span class=' g'>|</span> <strong>" + count + "</strong> <span class=' g'>words</span>";
		} 
		
		
		if (heading){
			heading = heading + " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='s g'>* (estimates only)</span>"
		}

		$("#booking-heading small").html(heading);

		return false;

	});

	$(document).on("click", "#booking-type button", function () {
		var type = $(this).attr("data-type");
		$.bbq.pushState({"type": type});
		$("#form-diff > article").hide();
		$("#form-diff-" + type).show();
	});
	

});
function checklistBtn() {
	var $this = $("#categoryID");
	var count = $this.find(':selected').attr('data-checklist-count');

	var $btn = $("#btn-tools-checklist");
	if (count > 0) {
		$btn.removeAttr("disabled");
	} else {
		$btn.attr("disabled", "disabled");

	}

}

function getData() {
	var ID = var_record_ID;

	$("#left-area .loadingmask").show();

	$("#whole-area .loadingmask").show();
	$.getData("/app/nf/data/form/_details", {"ID": ID}, function (data) {

		var title = "";
		if (data['details']['ID']) {
			if (data['details']['deleted'] == '1') {
				title = "Edit Deleted Record";
			} else {
				title = "Edit Record";
			}

		} else {
			title = "New Record";
		}
		document.title = "NF - Form - " + title;

		var toolbar = {
			"heading": title,
			"data"   : data
		};


		$("#scroll-container").jqotesub($("#template-form"), data);
		$("#file-area").jqotesub($("#template-form-files"), data['details']['media']);
		//$("#new-file-area").jqotepre($("#template-form-files"), data);
		$("#maintoolbar").jqotesub($("#template-toolbar"), toolbar);


		$("#form-diff > article").hide();
		var type = data['settings']['last_type'];
		if ($.bbq.getState("type")) {
			type = $.bbq.getState("type");
		}
		var $bookingTypeBtns = $("#booking-type");
		$bookingTypeBtns.find("button[data-type='" + type + "']").trigger("click");

		$("#slider-text").html(_priority[data.details.priority]);

		//console.log(data.details.priority);

		formLoaded(data);
		resizeform();

		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}, "data");

}


function formLoaded(data) {
	var $cm = $("#cm-block");
	var body = "";
	if ($("#body").length) {
		var instance = CKEDITOR.replace('body', text_settings);
		instance.on('change', function (e) {
			var body = e.editor.getData()
			$cm.html(body).trigger("change");
		});

		body = $("#body").val();
		
	}
	$cm.html(body).trigger("change");




	$(".caption_boxes").each(function(){
		var ID = $(this).attr("id");
		CKEDITOR.replace(ID, caption_settings);
	});

	checklistBtn();
	//getChecklistData();

	$("select").select2();


	$("#rightpane-top").css("bottom", $("#rightpane-bottom").outerHeight());


	$("#slider").slider({
		value: data.details.priority,
		min  : 0,
		max  : 5,
		step : 1,
		slide: function (event, ui) {

			$("#slider-text").html(_priority[ui.value]);
			$("#priority").val(ui.value);
		}
	});
	$("#amount").val("$" + $("#slider").slider("value"));



	$("#uploader").pluploadQueue({
		// General settings
		runtimes: 'html5,gears,flash,silverlight',
		url     : '/app/nf/upload/?folder=' + _uploadPath,

		chunk_size         : '1mb',
		unique_names       : true,
		multiple_queues    : true,

		// Resize images on clientside if we can
		//resize             : {width: 1000, height: 1000, quality: 90},

		// Specify what files to browse for
		filters            : [
			{title: "Image files", extensions: _allowedFiles['1'].img.join(",")},
			{title: "files", extensions: allowedFileExtentions}
		],

		// Flash settings
		flash_swf_url      : '/ui/plupload/js/plupload.flash.swf',

		// Silverlight settings
		silverlight_xap_url: '/ui/plupload/js/plupload.silverlight.xap',

		init: {
			Refresh       : function (up) {
				resizeform();
			},
			StateChanged  : function (up) {

			},
			QueueChanged  : function (up) {

			},
			UploadProgress: function (up, file) {

			},
			FilesAdded    : function (up, files) {
				plupload.each(files, function (file) {

				});
			},
			FilesRemoved  : function (up, files) {

			},
			FileUploaded  : function (up, file, info) {


				var file_data = filedetails(file.target_name);

				var data = [
					{
						"tempID"       : file.id,
						"ID"           : "",
						//"domainID": "temp",
						"filename"     : file.target_name,
						"filename_orig": file.name,
						"folder"       : _uploadPath,
						"caption"      : "",
						"fileType"     : file_data.fileType,
						//"artID": 19933,
						//"fileID": 31241,
						"scope"        : 0,
						"mainThumbnail": 0,
						//"viewed": 0,
						"tags"         : "",
						"icon"         : file_data.icon
					}
				];


				//	console.log(file);
				//	console.log(data);

				$("#new-file-area").jqotepre($("#template-form-files"), data);
				CKEDITOR.replace('file-caption-' + file.id, caption_settings);


				resizeform()

			},
			ChunkUploaded : function (up, file, info) {

			},
			Error         : function (up, args) {

			}

		}
	});
}
function resizeform() {

	var pane = $(".form-body").jScrollPane(jScrollPaneOptionsMP);
	var api = pane.data("jsp");
	//api.reinitialise();

	scrolling(api);
}
function filedetails(filename) {

	var file_ext = filename.split('.').pop();
	file_ext = file_ext.toLowerCase();
	var filetype = "2";
	var icon = "file";

	$.each(_allowedFiles, function (k, v) {
		//console.log(k + " | " + v);
		$.each(v, function (sk, sv) {
			//console.log(sk + " - " + sv + " - " + $.inArray(file_ext, sv));
			if ($.inArray(file_ext, sv) >= 0) {

				icon = sk;
				filetype = k;
			}
		});
	});

	return {
		"filename": filename,
		"ext"     : file_ext,
		"icon"    : icon,
		"fileType": filetype
	}
}
function getChecklistData() {
	var ID = $("#categoryID").val();
	var checked = $("#checklist").val();

	$.getData("/app/nf/data/form/checklists", {"categoryID": ID, "selected": checked}, function (data) {

		if (data.length) {
			$("#checklist-container").jqotesub($("#template-checklist"), data);
		} else {
			$("#checklist-container").html("No checklists for this category");
		}

		$("#checklist-modal").modal("show");

	}, "checklist_data");

}
function form_submit() {
	$form = $("#main-form");
	$(".fielderror", $form).remove();


	var type = $("#booking-type button.active").attr("data-type");

	if (!type) {
		alert("Something went wrong, please select a booking type");
		return false;
	}

	var submit = true;

	var $fld = "";
	$fld = $("#title");
	if (!$fld.val()) {
		submit = error_msg($fld, "<strong>Title</strong> is Required");
	}

	$fld = $("#authorID");
	if (!$fld.val()) {
		submit = error_msg($fld, "<strong>Author</strong> is Required");
	}



	resizeform();

	if (submit) {
		$("#pagecontent .loadingmask").show();
		var data = $form.serialize();
		$.post("/app/nf/save/articles/form?ID=" + var_record_ID + "&type=" + type, data, function (response) {

			//$("#record-ID").val(response[0]['ID']);
			getData();
			$("#pagecontent .loadingmask").fadeOut(transSpeed);
			$("#modal-form").jqotesub($("#template-modal-form"), response).modal("show");
		});
	}

}
function error_msg($fld, msg) {
	var str = '<div class="alert fielderror alert-error">' + msg + '</div>';
	if (!$fld.hasClass("control-group") && !$fld.hasClass("fieldgroup")) {
		$fld = $fld.closest(".control-group");
	}

	$fld.prepend(str);
	return false;

}