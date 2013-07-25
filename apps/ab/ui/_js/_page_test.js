/*
 * Date: 2012/06/14 - 9:07 AM
 */

console.log($('#whole-area').length)

var uploader = new plupload.Uploader({
	runtimes           :'gears,html5,flash,silverlight',
	browse_button      :'file-area-filename',
	//container          :'container',
	max_file_size      :'200mb',
	max_file_count: 1,
	chunk_size: "5MB",
	url                :'/app/ab/upload/?folder=1/3/1040/',

	flash_swf_url      :'/ui/plupload/js/plupload.flash.swf',
	silverlight_xap_url:'/ui/plupload/js/plupload.silverlight.xap',
	filters            :[
		//{title:"Image files", extensions:"jpg,gif,png"},
		//{title:"Zip files", extensions:"zip"}
	],
	unique_names: true
});
uploader.bind('Init', function (up, params) {

});

uploader.bind('FilesAdded', function (up, files) {

	var fileCount = up.files.length, i = 0, ids = $.map(up.files, function (item) {
			return item.id;
		});

	for (i = 0; i < fileCount; i++) {
		uploader.removeFile(uploader.getFile(ids[i]));
	}

	i = 0;
	$('#file-area-filename').html('<div id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ')</div>');

	setTimeout(function () {
		$("#file-area .progress").fadeIn();
		uploader.start();
	}, 100);
});




uploader.bind('UploadProgress', function (up, file) {
	$("#file-area .progress .bar").css("width",file.percent+"%");
});

uploader.bind('UploadComplete', function () {
	$("#file-area .progress").fadeOut(500,function(){
		$("#file-area .progress .bar").css("width", "0%");
	});
});




$('#uploadfiles').on("click", function () {
	console.log("starting");
	uploader.start();
	return false;
});

uploader.init();

console.log(uploader);