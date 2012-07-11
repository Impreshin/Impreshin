/*
 * Date: 2012/05/30 - 8:37 AM
 */
var whole_pane = $("#whole-area .scroll-pane").jScrollPane(jScrollPaneOptions).data("jsp");

$(document).ready(function () {


	$("#pub-select input:checkbox").change(function(){
		var str = $("#pub-select input:checkbox:checked").map(function(){
				return $(this).attr("data-pub");
		});
		str = $.makeArray(str);
		str = str.join(", ");
		$("#pub-select-label").html(str);


	});
	$(document).on("change",".trigger_getdata input:checkbox",function(){
		getData();
	});
	getData();
});

function getData() {

	var pubs = $("#pub-select input:checkbox:checked").map(function () {
		return $(this).attr("data-id");
	});
	pubs = $.makeArray(pubs);
	pubs = pubs.join(",");

	var years = $("#report-years input:checkbox:checked").map(function () {
		return $(this).val();
	});
	years = $.makeArray(years);
	years = years.join(",");





	$("#whole-area .loadingmask").show();


	for (var i = 0; i < listRequest.length; i++) listRequest[i].abort();
	listRequest.push($.getJSON("/ab/data/reports_publication_figures/_data/", {"pubs":pubs,"years":years}, function (data) {
		data = data['data'];

		$("#scroll-container").jqotesub($("#template-report-figures"), data);


		var $scrollpane = $("#whole-area .scroll-pane");
			$scrollpane.jScrollPane(jScrollPaneOptionsMP);


		$("#whole-area .loadingmask").fadeOut(transSpeed);
	}));

}