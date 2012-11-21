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

});
var toolbar = [
	{ name:'basicstyles', items:[ 'Bold', 'Italic', 'Underline', '-', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat','-',  'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-',  'ShowBlocks' , 'Maximize', '-', 'Source'] }

];
var editor, html = '';
var config = {
	//skin              :'BootstrapCK-Skin',
	//toolbarCanCollapse:false,
	toolbar:toolbar,
	contentsCss : '/ui/_css/bootstrap.css',

	extraPlugins      :'autogrow',
	autoGrow_maxHeight:900,
	autoGrow_minHeight:300,
	// Remove the Resize plugin as it does not make sense to use it in conjunction with the AutoGrow plugin.
	removePlugins     :'resize',
	height            :'300px'
};

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


function createEditor() {
	if (editor)
		return;

	// Create a new editor inside the <div id="editor">, setting its value to html

	editor = CKEDITOR.replace('article', config, html);
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