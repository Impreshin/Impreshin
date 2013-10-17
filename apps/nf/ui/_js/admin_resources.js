/*
 * Date: 2012/06/20 - 8:49 AM
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

var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
var right_pane = $("#record-list-middle").jScrollPane(jScrollPaneOptions).data("jsp");
var max_height = $(window).height() - 190;
var text_settings = {
	uiColor           : '#FFFFFF',
	height            : '190px',
	toolbar           : text_toolbar,
	resize_enabled    : false
	
};

$(document).ready(function () {
	getList();
	getDetails();

	$(document).on("change", "input:radio[name='type']", function () {
		section();
	});
	$(document).on("click", "#record-list .record", function (e) {
		var $this = $(this), ID = $this.attr("data-id");

		var $cur_pub = $(e.target).closest(".cur-pub");
		$.bbq.pushState({"ID":ID});
		
			getDetails();

	});

	$(document).on("change", "#searchform select", function () {
		$("#searchform").trigger("submit")
	});
	$(document).on("change", "#searchform select", function () {
		$("#searchform").trigger("submit")
	});
	$(document).on("submit", "#searchform", function (e) {
		e.preventDefault();
		$.bbq.removeState("ID");
		getList();
		getDetails();
		return false;
	});

	$(document).on("click", "#reload-btn", function () {
		getList();
		getDetails();
	});
	$(document).on("click", "#btn-new", function () {
		$.bbq.removeState("ID");
		getDetails();
	});
	$(document).on("click", "#btn-delete", function () {
		var ID = $.bbq.getState("ID");
		if (confirm("Are you sure you want to delete this record?")) {
			$("#left-area .loadingmask").show();
			$.post("/app/nf/save/admin/resources/_delete/?ID=" + ID, function (r) {
				$.bbq.removeState("ID");
				getList();
				getDetails();
			});
		}

	});
	$(document).on("submit", "#capture-form", function (e) {
		e.preventDefault();
		var $this = $(this);
		var data = $this.serialize();
		var categoryID = $("#categoryID").val();
		var $errorArea = $("#errorArea").html("");

		var ID = $.bbq.getState("ID");
		$("#left-area .loadingmask").show();
		$.post("/app/nf/save/admin/resources/_save/?ID=" + ID+"&categoryID="+ categoryID, data, function (r) {
			r = r['data'];
			if (r['error'].length) {
				var str = "";
				for (var i in r['error']) {
					str += '<div class="alert alert-error">' + r['error'][i] + '</div>'
				}
				$("#left-area .loadingmask").fadeOut(transSpeed);
				$errorArea.html(str);
				$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
			} else {

				$.bbq.removeState("ID");
				getList();
				getDetails();
			}

		});
		return false;
	});

	$(document).on("click", ".order-btn", function (e) {
		e.preventDefault();
		var $this = $(this);
		$this.closest("table").find(".order-btn").removeClass("asc desc");
		//$this.addClass("active");
		$.bbq.pushState({"order":$this.attr("data-col")});

		getList();
		$.bbq.removeState("order");

	});

	$(document).on("click", "#btn-log", function (e) {
		e.preventDefault();
		var $this = $(this);

		$.getData("/app/nf/logs/resources", {}, function (data) {
			$logarea = $("#view-log table").html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>');
			if (data[0]) {
				$logarea.jqotesub($("#template-admin-logs"), data);

			}
			$("#view-log").modal("show");

		},"logs");

	});

});

function getList() {

	var ID = $.bbq.getState("ID");

	var order = $.bbq.getState("order");
	order = (order) ? order : "";

	var categoryID = $("#categoryID").val();

	$("#right-area .loadingmask").show();
	$.getData("/app/nf/data/admin/resources/_list", {"order":order,"categoryID":categoryID}, function (data) {

		var $recordsList = $("#record-list");
		var $pagenation = $("#pagination");
		if (data['records'][0]) {
			$recordsList.jqotesub($("#template-list"), data['records']);
			$("#record-list tr.active").removeClass("active");
			$("#record-list tr[data-id='" + ID + "']").addClass("active");

		} else {
			$recordsList.html('<tfoot><tr><td class="c no-records">No Records Found</td></tr></tfoot>')
		}

		$recordsList.find("tbody").sortable({
			'axis'       :"y",
			'containment':"#record-list-middle",
			update       :function (event, ui) {
				var rec = [];
				$("#record-list tr").each(function () {
					rec.push($(this).attr("data-id"));
				});
				rec = rec.join(",");

				$.post("/app/nf/save/admin/resources/_sort/", {"order":rec,"categoryID":categoryID}, function (t) {

				});
			}
		});
		$recordsList.find("tbody").disableSelection();

		$("#record-list-middle").css("bottom", $("#record-details-bottom").outerHeight());
		$("#record-list-middle").jScrollPane(jScrollPaneOptions);
		$("#right-area .loadingmask").fadeOut(transSpeed);

	},"list");

}
function getDetails() {
	var ID = $.bbq.getState("ID");

	$("#record-list tr.active").removeClass("active");
	$("#record-list tr[data-id='" + ID + "']").addClass("active");
	$("#left-area .loadingmask").show();

	$.getData("/app/nf/data/admin/resources/_details", {"ID":ID}, function (data) {
		$("#form-area").jqotesub($("#template-details"), data);


		section();
		replace_btn();
		$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		
		$("#left-area .loadingmask").fadeOut(transSpeed);

	},"details");
}
function resizeform() {
	var pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptionsMP);
	var api = pane.data("jsp");
	//api.reinitialise();

	scrolling(api);
}

function section(){
	var sec = $("input:radio[name='type']:checked").val();
	$(".diff-types").hide();
	$("#type-"+sec).show();
}
function replace_btn() {

		

		var pluploader = new plupload.Uploader({
			browse_button: 'btn-container-file-button',
			container    : 'btn-container-file',
			// General settings
			runtimes     : 'html5,gears,flash,silverlight',
			url          : '/app/nf/upload/?folder=' + folder,

			chunk_size         : '1mb',
			unique_names       : true,
			multiple_queues    : false,
			multi_selection    : false,

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
				Refresh: function (up) {
					
				},
				FilesAdded: function (up, files) {
					var doing = false;
					if ($("#filename").val()){
						if (confirm("Are you sure you want to replace the existing file with:\n " + files[0].name + "?")) {


							doing = true;
							
						}
					} else {
						doing = true;
					}
					
					if (doing){
						up.refresh();
						up.start();
						$("#btn-container-file-wait-progress").html("starting...");
						$("#btn-container-file-wait").show();
						$("#filename").attr("disabled","disabled");
					}
					
					
				},
				UploadProgress: function(up, file){
					//$progress.show().find(".bar").css("width", file.percent + "%");
					$("#btn-container-file-wait-progress").html(file.percent + "%")
				},
				Error: function (up, err) {
					//console.log("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : ""));
				},
				FileUploaded: function (up, file, response) {


					$("#btn-container-file-wait").hide();
					$("#btn-container-file-wait-progress").html("");
					$("#filename").val(file.name).removeAttr("disabled");
					$("#path").val(file.target_name);
					
				}

			}



		});
		pluploader.bind('Init', function (up, params) {
			//console.log("Current runtime environment: " + params.runtime);
		});
		pluploader.init(function () {
			//console.log("woof"); 
		});



	
}