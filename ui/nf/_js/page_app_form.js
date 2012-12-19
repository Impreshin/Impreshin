/*
 * Date: 2012/11/20 - 8:27 AM
 */
var $form = $("#capture-form");
$(document).ready(function(){
	getForm(data);
	highlightselectedsidebar();
	$(document).on("change","#capture-form-sidebar input:radio",function(){
		highlightselectedsidebar();
	});





	//$("#article")
	$(document).on("click","#article-box",function(){
		$('#modal-form-article').modal('show');

	});
	scrollpane();
	$('#modal-form-article')
		.on('hide', function () {
			var val = CKEDITOR.instances.article.getData();
			//console.log(val)
			$("#article-box").html(val);
			// do something…
			scrollpane();
		})
		.on('show', function () {
			var val = $("#article-box").html();
			updateCount(val)
			$("#article").val(val);
		// do something…

		});

	$("#capture-form").on("submit",function(e){
		e.preventDefault();
		form_submit();
		return false;

	})


});

var toolbar = [
	{ name:'basicstyles', items:[ 'Bold', 'Italic', 'Underline', 'Strike'] },
	{ name:'basicstyles', items:[  'Subscript', 'Superscript' ] },
	{ name:'basicstyles', items:[   'RemoveFormat', '-', 'Undo', 'Redo' ] },
	{ name:'clipboard', items:[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', ] },
	{ name:'editing', items:[ 'Find', 'Replace', '-', 'SelectAll' ] },
	{ name:'tools', items:[  'ShowBlocks'] },
	{ name:'tools', items:[  'Maximize'] },
	{ name:'tools', items:[ 'Source'] }

];



var editor, html = '';
var height = $("#whole-area").height() - 50;
height = (height>600)?530:height;
var config = {
	//skin              :'BootstrapCK-Skin',
	//toolbarCanCollapse:false,
	toolbar:toolbar,
	contentsCss : '/ui/_css/bootstrap.css',
	removePlugins     :'resize',
	height: height +"px"
	//height            :
};
function scrollpane(){
	var $scrollpane = $("#whole-area .scroll-pane");
		$scrollpane.jScrollPane(jScrollPaneOptionsMP);
}

function getForm(data){
	$("#whole-area .loadingmask").show();
	removeEditor();
	if (data){
		$form.jqotesub($("#template-form"), data);
	}

	createEditor();


	$("#whole-area .loadingmask").fadeOut(transSpeed);
}
function highlightselectedsidebar(){
	$("#capture-form-sidebar label.active").removeClass("active");
	$("#capture-form-sidebar label input:radio:checked").closest("label").addClass("active");
}
function updateCount(t) {
	//var chars = t.length;
	var words = t.split(" ").length;

	$("#modal-form-article-words").text(words);
//	$("#modal-form-article-characters").text(chars);

}

function createEditor(el) {
	if (editor)
		return;

	el = "article";
	// Create a new editor inside the <div id="editor">, setting its value to html

	if ($("#"+el).length){

		editor = CKEDITOR.replace(el, config, html);
		editor.on('key', function (evt) {
			updateCount(evt.editor.getData());
		}, editor.element.$);
	}

}

function removeEditor() {
	if (!editor)
		return;

	// Retrieve the editor contents. In an Ajax application, this data would be
	// sent to the server or used in any other way.
	document.getElementById('editorcontents').innerHTML = html = editor.getData();
	document.getElementById('contents').style.display = '';

	// Destroy the editor.
	editor.destroy();
	editor = null;
}

function form_submit() {
	$(".alert", $form).remove();






	var type = $("#booking-type button.active").attr("data-type");
	if (!type) {
		//alert("something went wrong, please select an article type");
		//return false;
	}

	var submit = true;

	var $fld = "";

	$fld = $("#title");
	if (!$fld.val()) {
		submit = error_msg($fld, "<strong>Title</strong>, is Required");
	}

	scrollpane();

	if (submit) {
		$("#pagecontent .loadingmask").show();
		var values = $form.serializeArray();
		values.push({name:'article', value:CKEDITOR.instances.article.getData()});

		$.post("/nf/save/articles/form?ID=" + data.details.ID + "&type=" + type, values, function (response) {

			$("#whole-area .loadingmask").fadeOut(transSpeed);
			//$("#modal-form").jqotesub($("#template-modal-form"), response[0]).modal("show");
		});
	}
}
function error_msg($fld, msg) {
	var str = '<div class="alert alert-error">' + msg + '</div>';
	if (!$fld.hasClass("control-group") && !$fld.hasClass("fieldgroup")) {
		$fld = $fld.closest(".control-group");
	}

	$fld.prepend(str);
	return false;

}
