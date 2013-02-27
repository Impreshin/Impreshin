/*
 * Date: 2013/02/13 - 2:06 PM
 */
$(document).ready(function(){
	$("#wizard-previous .btn").removeAttr("disabled");
	if (show_next){
		$("#wizard-next .btn").removeAttr("disabled");
	}


	$("#wizard-next .btn, #wizard-previous .btn").click(function () {
		var $this = $(this), url = $this.attr("data-url");

		window.location = url;
	});
});
