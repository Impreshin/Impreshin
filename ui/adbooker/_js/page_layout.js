/*
 * Date: 2012/05/30 - 8:37 AM
 */
var left_pane = $("#left-area .scroll-pane").jScrollPane(jScrollPaneOptions);
var right_pane = $("#right-area .scroll-pane").jScrollPane(jScrollPaneOptions);

$(document).ready(function () {

	scrolling(left_pane.data("jsp"));


});