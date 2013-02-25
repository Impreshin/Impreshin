/*
 * Date: 2013/02/25 - 2:01 PM
 */
var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");
$(document).ready(function () {

	$("#wizard-previous .btn").removeAttr("disabled");
	$("#wizard-next .btn, #wizard-previous .btn").on("click", function () {
		var $this = $(this), url = $this.attr("data-url");

		window.location = url;
	});
});