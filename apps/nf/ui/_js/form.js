var allowedFileExtentions = [];

$.each(_allowedFiles, function (kk, vv) {
	$.each(vv, function (k, v) {
		$.each(v, function (sk, sv) {
			allowedFileExtentions.push(sv)
		});
	});
});
allowedFileExtentions = allowedFileExtentions.join(",");

//console.log(allowedFileExtentions); 

var max_height = $(window).height() - 200;

var spell_check_config = {
	lang: function () {
		//console.log("woof"); 
		return $('#language').val()
	}, parser: 'html', webservice: {
		path: '/system/spellcheck?custom=' + _custom_dictionary, driver: 'Enchant'
	}, suggestBox: {
		position: 'below', appendTo: 'body'
	}
};

var extra_plugins_jqueryspellchecker = '';
if (_enable_spellcheck == '1') {
	extra_plugins_jqueryspellchecker = ',jqueryspellchecker';
}

var text_settings = {
	uiColor: '#FFFFFF',
	height: '390px',
	toolbar: text_toolbar,
	resize_enabled: false,
	extraPlugins: 'autogrow,onchange' + extra_plugins_jqueryspellchecker,
	autoGrow_maxHeight: max_height,
	autoGrow_minHeight: 390,
	contentsCss: '/ui/spellchecker/css/jquery.spellchecker.css',
	spell_checker: spell_check_config
	
};
var caption_settings = {
	uiColor: '#FFFFFF',
	height: '110px',
	toolbar: text_toolbar,
	removePlugins: 'elementspath',
	resize_enabled: false,
	
	extraPlugins: 'autogrow' + extra_plugins_jqueryspellchecker,
	autoGrow_minHeight: 110,
	autoGrow_maxHeight: 110 > max_height ? 110 : max_height,
	
	contentsCss: '/ui/spellchecker/css/jquery.spellchecker.css',
	spell_checker: spell_check_config
};

$(document).ready(function () {
	
	window.onbeforeunload = function () {
		var changed = false;
		for (var i in CKEDITOR.instances) {
			if (CKEDITOR.instances[i].checkDirty()) {
				changed = true;
			}
		}
		
		if (changed) {
			return "You have made changes to the form, are you sure you want to navigate away from this page without saving them?"
		}
		
	};
	
	getFormData();
	$(document).on("submit", "#modal-delete form", function (e) {
		e.preventDefault();
		
		var data = $(this).serialize();
		
		if ($("#delete_reason").val() == "") {
			alert("You must specify a reason");
		} else {
			$("input", $(this)).attr("disabled", "disabled");
			$.post("/app/nf/save/articles/_delete/?ID=" + var_record_ID, data, function (r) {
				alert("Record deleted");
				document.location = "/app/nf/";
			});
			
		}
		
	});
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
		//	$checkbox.prop("checked", !$checkbox.prop("checked"));
		
		var $btn = $this.closest("form").find("button[type='submit']");
		if ($this.find("input").is(":checked")) {
			$btn.html("Save and stage: " + $this.attr("data-stage-label"))
		} else {
			$btn.html("Save Record")
		}
		
	});
	
	$(document).on("change", "#locked_record", function () {
		lock_unlock();
	});
	
	$(document).on("click", "#btn-tools-dictionary", function () {
		
		var ck_instance_name = false;
		var selectedText = "";
		for (var ck_instance in CKEDITOR.instances) {
			t = CKEDITOR.instances[ck_instance].getSelection().getSelectedText();
			if (t) {
				selectedText = t;
			}
			
		}
		
		$("#modal-dictionary").modal('show');
		lookup(selectedText);
		
	});
	
	$(document).on("click", "#btn-tools-search", function () {
		var meta = $("form #meta").val();
		
		meta = encodeURIComponent(meta);
		var w = 800;
		var h = $(window).height();
		var url = 'https://www.google.com/search?q=' + meta;
		//console.log(meta);
		window.open(url, '_blank', 'width=' + w + ',height=' + h);
		return false;
		
	});
	$(document).on("click", "#btn-tools-checklist", function () {
		var categoryID = $("#categoryID").val();
		
		getChecklistData();
		
	});
	$(document).on("change", "#categoryID", function () {
		
		checklistBtn();
		
		$("#cm-style-block").load("/app/nf/data/form/cm_block_render?categoryID=" + $(this).val(), function () {
			$("#cm-block").trigger("change");
		})
		
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
		
		getFormData();
		return false;
		
	});
	$(document).on("change", "#cm-block", function (e) {
		e.preventDefault();
		
		///console.log("changed");
		
		var dpi = document.getElementById("dpi").offsetHeight;
		var artcm = $(this).height();
		
		if (artcm > 0) {
			
			artcm = (artcm / dpi);
			if (_useImperial) {
				
			} else {
				artcm = (artcm) * 2.54;
			}
			
			artcm = Math.ceil(artcm);
		}
		
		var heading = "";
		if (artcm) {
			$("#cm").val(artcm);
			var heading_unit = _useImperial ? "Inches" : "Cm";
			heading = heading + '<strong>' + artcm + "</strong> <span class=' g'>" + heading_unit + "</span>";
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
		
		if (heading) {
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
	
	$(document).on("click", ".btn-delete-file", function () {
		var $this = $(this);
		var ID = $this.attr("data-ID");
		if (confirm("Are you sure you want to delete this file?")) {
			$("#pagecontent .loadingmask").show();
			$.post("/app/nf/save/articles/file_delete?ID=" + ID, {}, function (response) {
				$this.closest(".file-record").remove();
				$("#pagecontent .loadingmask").fadeOut(transSpeed);
				resizeform();
			});
			
		}
		
	});
	
	$("#dictionary-form").submit(function (e) {
		e.preventDefault();
		lookup();
	});
	
	$(document).on("click", ".lookups span", function () {
		var word = $(this).text();
		//console.log(word);
		lookup(word);
		
	});
	
	$(document).on("click", ".btn-view-photo-form", function (e) {
		e.stopPropagation();
		var path = $(this).attr("data-file");
		var ID = $(this).attr("data-id");
		var caption = $(this).attr("data-caption");
		
		var $this = $(this);
		var $record = $this.closest("article.file-record");
		
		var id = $record.attr("id");
		
		var capti = id.replace("file-record-", "file-caption-");
		
		caption = CKEDITOR.instances[capti].getData()
		//file-caption-25481
		
		var p = {
			"ID": ID, "caption": caption ? caption : '', "path": path
		};
		
		var w = $(window).width();
		var h = $(window).height();
		
		w = w - 100;
		h = h - 100;
		
		w = w > 930 ? 930 : w;
		h = h > 930 ? 930 : h;
		
		$.fancybox.open({
			href: '/app/nf/thumb/' + w + '/' + h + '?file=' + path + '&crop=false', title: ""
		}, {
			padding: 10, type: 'image', openEffect: 'fade', beforeLoad: function () {
				
				this.title = $("#template-photo-caption").jqote(p);
				
			}, afterLoad: function () {
				
			}, helpers: {
				overlay: {
					speedIn: 500, speedOut: 500, opacity: 0.4
					
				}, title: {
					type: 'over'
				}
				
			}
		});
		
	});
	
});

function wrapify(str) {
	var ret = str;
	if (str) {
		var newHtml = str.split(","), spans = $.map(newHtml, function (v) {
			v = v.trim();
			return '<span>' + v + '</span>';
		});
		
		ret = spans.join(', ');
	}
	
	return ret;
}

function lookup(word) {
	if (word) {
		$("#word").val(word);
	} else {
		word = $("#word").val();
	}
	
	def(word);
	
}
function def(word) {
	$result = $("#modal-dictionary-result");
	if (word) {
		
		$result.html('<img src="/ui/_images/loading-wide.gif" class="loading">');
		jQuery.support.cors = true;
		$.ajax("http://www.stands4.com/services/v2/syno.php?uid=3116&tokenid=DncJPzPES3OLbTH7&word=" + word, {
			cache: true, type: "get", global: false, dataType: "xml", //jsonp : false,
			success: function (returnedXMLResponse) {
				
				var data = {
					"term": word, "result": "0", "results": []
				};
				$('result', returnedXMLResponse).each(function () {
					
					var syn = $('synonyms', this).text()
					
					var d = {
						"term": $('term', this).text(),
						"partofspeech": $('partofspeech', this).text(),
						"definition": $('definition', this).text(),
						"example": $('example', this).text(),
						"synonyms": wrapify($('synonyms', this).text()),
						"antonyms": wrapify($('antonyms', this).text())
						
					};
					data.results.push(d);
					
					//Here you can do anything you want with those temporary
					//variables, e.g. put them in some place in your html document
					//or store them in an associative array
				});
				var template = "#template-dictionary-result"
				if (data.results.length) {
					
				} else {
					template = "#template-dictionary-no-result"
				}
				
				$result.jqotesub($(template), data);
				
				//getChannelMessages(channel);
			}
		});
	} else {
		$result.html("")
	}
	
}

function lock_unlock() {
	var $this = $("#locked_record");
	var $parent = $this.parent();
	var $icon = $parent.find("i");
	$icon.removeClass('icon-lock');
	$icon.removeClass('icon-unlock');
	if ($this.is(":checked")) {
		$parent.attr("title", "Record will remain locked");
		$icon.addClass('icon-lock')
	} else {
		$parent.attr("title", "Record will be unlocked");
		$icon.addClass('icon-unlock')
	}
	//console.log($this.is(":checked"))
}
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

function getFormData() {
	var ID = var_record_ID;
	//console.log("getFormData"); 
	
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
			"heading": title, "data": data
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
		
		//$("#slider-text").html(_priority[data.details.priority]);
		
		//console.log(data.details.priority);
		
		setTimeout(function () {
			
			formLoaded(data);
			resizeform();
			lock_unlock();
		}, 400)
		
		//setTimeout(resizeform, 1000)
		$("#whole-area .loadingmask").fadeOut(transSpeed, function () {
		}());
	}, "form_data");
	
}

function formLoaded(data) {
	
	var pane = $(".form-body").jScrollPane(jScrollPaneOptionsMP);
	var api = pane.data("jsp");
	var $cm = $("#cm-block");
	var body = "";
	if ($("#body").length) {
		
		
		
		//var spellchecker = new $.SpellChecker('#body', spell_check_config);
		
		var instance = CKEDITOR.replace('body', text_settings);
		instance.on('change', function (e) {
			var body = e.editor.getData();
			$cm.html(body).trigger("change");
			//spellchecker.check();
			resizeform();
		});
		instance.on('focus', function (e) {
			
			api.scrollToElement("#body-area", true, true);
			setTimeout(function () {
				resizeform();
			}, 400)
			
		});
		
		body = $("#body").val();
		
	}
	$("#cm-style-block").load("/app/nf/data/form/cm_block_render?categoryID=" + $("#categoryID").val(), function () {
		$("#cm-block").trigger("change");
	});
	$cm.html(body);
	
	$(".caption_boxes").each(function () {
		var ID = $(this).attr("id");
		var instance = CKEDITOR.replace(ID, caption_settings);
		instance.on('change', function (e) {
			resizeform();
		});
		instance.on('focus', function (e) {
			var parentID = $("#" + ID).closest(".file-record").attr("ID");
			api.scrollToElement("#" + parentID, true, true);
			
			setTimeout(function(){
				resizeform();
			}, 400)
			
		});
		
	});
	
	checklistBtn();
	//getChecklistData();
	
	$("select.select2").select2();
	
	$("#rightpane-top").css("bottom", $("#rightpane-bottom").outerHeight());
	
	var $select = $("#priorityID");
	
	if ($select.length) {
		$("#slider-text").html($("option:selected", $select).text())
		var slider = $("#slider").slider({
			min: 1,
			max: $select[0].length,
			range: "min",
			value: $select[0].selectedIndex + 1,
			slide: function (event, ui) {
				//console.log(ui.value - 1); 
				$select[0].selectedIndex = ui.value - 1;
				$("#slider-text").html($("option:selected", $select).text())
				$select.trigger("change");
			}
		});
	}
	
	$("#uploader").pluploadQueue({
		// General settings
		runtimes: 'html5,gears,flash,silverlight', url: '/app/nf/upload/?folder=' + _uploadPath,
		
		chunk_size: '1mb', unique_names: true, multiple_queues: true,
		
		// Resize images on clientside if we can
		//resize             : {width: 1000, height: 1000, quality: 90},
		
		// Specify what files to browse for
		filters: [{title: "Image files", extensions: _allowedFiles['1'].img.join(",")}, {
			title: "files",
			extensions: allowedFileExtentions
		}],
		
		// Flash settings
		flash_swf_url: '/ui/plupload/js/plupload.flash.swf',
		
		// Silverlight settings
		silverlight_xap_url: '/ui/plupload/js/plupload.silverlight.xap',
		
		init: {
			Refresh: function (up) {
				resizeform();
			}, StateChanged: function (up) {
				
			}, QueueChanged: function (up) {
				
			}, UploadProgress: function (up, file) {
				
			}, FilesAdded: function (up, files) {
				plupload.each(files, function (file) {
					
				});
				up.start();
			}, FilesRemoved: function (up, files) {
				
			}, FileUploaded: function (up, file, info) {
				
				var file_data = filedetails(file.target_name);
				
				var data = [{
					"tempID": file.id,
					"ID": "", //"domainID": "temp",
					"filename": file.target_name,
					"filename_orig": file.name,
					"folder": _uploadPath,
					"caption": "",
					"fileType": file_data.fileType, //"artID": 19933,
					//"fileID": 31241,
					"scope": 0,
					"mainThumbnail": 0, //"viewed": 0,
					"tags": "",
					"icon": file_data.icon
				}];
				
				//	console.log(file);
				//	console.log(data);
				
				$("#new-file-area").jqotepre($("#template-form-files"), data);
				CKEDITOR.replace('file-caption-' + file.id, caption_settings);
				
				resizeform()
				
			}, ChunkUploaded: function (up, file, info) {
				
			}, Error: function (up, args) {
				
			}
			
		}
	});
	replace_btn();
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
		"filename": filename, "ext": file_ext, "icon": icon, "fileType": filetype
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
	var locked = $("#locked_record").is(":checked");
	locked = locked ? "1" : "0";
	//console.log(locked)
	
	if (!type) {
		alert("Something went wrong, please select a record type");
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
		$.post("/app/nf/save/articles/form?ID=" + var_record_ID + "&type=" + type + "&locked=" + locked, data, function (response) {
			
			if (response['error'] && response['error'].length) {
				var str = "";
				for (var i in response.error) {
					$fld = $("#" + response.error[i].field);
					error_msg($fld, response.error[i].msg);
					
				}
				$("#pagecontent .loadingmask").fadeOut(transSpeed);
				resizeform();
			} else {
				
				getFormData();
				$("#pagecontent .loadingmask").fadeOut(transSpeed);
				$("#modal-form").jqotesub($("#template-modal-form"), response).modal("show");
			}
			
			//$("#record-ID").val(response[0]['ID']);
			
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
function replace_btn() {
	$(".file-replace-btn").each(function () {
		var $this = $(this);
		
		var ID = $this.attr("data-id");
		var filename = $this.attr("data-filename");
		var folder = $this.attr("data-folder");
		var $progress = $this.parent().find(".progress");
		
		var pluploader = new plupload.Uploader({
			browse_button: this, container: 'btn-container-' + ID, // General settings
			runtimes: 'html5,gears,flash,silverlight', url: '/app/nf/upload/?folder=' + folder,
			
			chunk_size: '1mb', unique_names: true, multiple_queues: false, multi_selection: false,
			
			// Specify what files to browse for
			filters: [{title: "Image files", extensions: _allowedFiles['1'].img.join(",")}, {
				title: "files",
				extensions: allowedFileExtentions
			}],
			
			// Flash settings
			flash_swf_url: '/ui/plupload/js/plupload.flash.swf',
			
			// Silverlight settings
			silverlight_xap_url: '/ui/plupload/js/plupload.silverlight.xap',
			
			init: {
				Refresh: function (up) {
					resizeform();
				}, FilesAdded: function (up, files) {
					if (confirm("Are you sure you want to replace the existing file with:\n " + files[0].name + "?")) {
						$this.find(".icon-exchange").hide();
						$progress.show().find(".bar").css("width", "0");
						
						up.refresh();
						up.start();
						
					}
				}, UploadProgress: function (up, file) {
					$progress.show().find(".bar").css("width", file.percent + "%");
				}, Error: function (up, err) {
					//console.log("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : ""));
				}, FileUploaded: function (up, file, response) {
					
					$progress.hide();
					$this.find(".icon-exchange").show();
					//console.log(file);
					
					var $fileblock = $this.closest("article.row");
					$fileblock.find(".file-filename_orig-field").val(file.name);
					$fileblock.find(".file-filename-field").val(file.target_name);
					
					var details = filedetails(file.target_name);
					var $area = $(".file-icon-area", $fileblock);
					
					//console.log(details); 
					if (details['fileType'] == '1') {
						$area.html('<img src="/app/nf/thumb/110/90?file=' + folder + '/' + file.target_name + '" alt=""/>');
						
					} else {
						$area.html('<div class="file-icon ' + details.icon + '"></div><div class="clearfix"></div>');
					}
				}
				
			}
			
		});
		pluploader.bind('Init', function (up, params) {
			//console.log("Current runtime environment: " + params.runtime);
		});
		pluploader.init(function () {
		});
		
	});
}