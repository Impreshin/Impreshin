var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");


$(document).ready(function(){
	
	var textarea_height = $("#right-area").innerHeight() - 50;
	
	$("#right-area").find(".loadingmask").hide();
	$("#right-area").find(".content").show();
	$("textarea#cm-block-form").css("height",textarea_height);
	$("textarea#cm-block-form").keyup(function(){
		render()
	});
	

	var h = $("#article-area").innerHeight() - 40;
	
	
	var text_settings = {
		uiColor           : '#FFFFFF',
		height            : h+'px',
		toolbar           : text_toolbar,
		resize_enabled    : false,
		extraPlugins      : 'onchange'

	};
	var $cm = $("#cm-block");
	var body = "";
	
	if ($("#body").length) {
		var instance = CKEDITOR.replace('body', text_settings);
		instance.on('change', function (e) {
			var body = e.editor.getData()
			$cm.html(body).trigger("change");
			render();
		});
		

		body = $("#body").val();

	}
	$cm.trigger("change");


	$(document).on("change", "#cm-block", function (e) {
		e.preventDefault();

		

		var artcm = $(this).height();



		if (artcm > 0) {
			artcm = artcm / 38.461538;
			artcm = Math.ceil(artcm);
		}

		var heading = "Cm Style Sheet";
		if (artcm){
			$("#cm").val(artcm);
			heading = heading + '<strong style="margin-left:50px;">'+ artcm + "</strong> <span class=' g'>cm</span>";
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

		$("#page-heading small").html(heading);

		return false;

	});
	
	render();
	
});

function render(){
	
	$("#left-area .loadingmask").show();

	var editor_data = CKEDITOR.instances.body.getData();
	var html = editor_data?editor_data:$("#body").val();
	var style = $("textarea#cm-block-form").val();
	$.post("/app/nf/admin/data/cmstylesheet/render?r="+Math.random(),{"style":style,"html":html},function(r){
		$("#left-area .loadingmask").hide();
		$("#render-area").html(r);
		scrollbar();
		resize_left();
		$("#cm-block").trigger("change");
	});
	
	
	
}
function resize_left(){
	var w = $("#render-area").outerWidth() + 40;
	$("#article-area").animate({"right":w},500);
	
	
}
function scrollbar(){
	$("#left-area .scroll-pane").jScrollPane(jScrollPaneOptionsMP);
}