/*
 * Date: 2012/06/20 - 8:49 AM
 */
var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {

	show_text()
	
	$(".pagination a").click(function(e){
		e.preventDefault();
		$.bbq.pushState({"char":$(this).attr("data-char")});
		show_text();
	});
	$(document).on("keyup","#new-words input[name='words[]']",function(){
		var is_empty = false;
		$("#new-words input").each(function(){
			if ($(this).val() == ""){
				is_empty = true;
			}
		});
		if (!is_empty){
			$("#new-words").append('<input type="text" name="words[]" value="" placeholder="New Word">');
			$("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
		}
		
	})

});
function show_text(){
	var char = $.bbq.getState("char");
	$("#existing-words input[type='text']").attr("type","hidden");
	$(".pagination li.active").removeClass("active");
	if (char){
		$("#existing-words input[data-char='"+char+"']").attr("type","text");
		$(".pagination a[data-char='"+char+"']").closest("li").addClass("active");
	}
	$("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions);
}