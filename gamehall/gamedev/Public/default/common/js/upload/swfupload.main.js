var url = "/apps/apkUpload";
var type = $("#type").val();
var appId = $("#app_id").val();
var apkId = $("#apk_id").val();

url += "/type/"+type;
if(type=="1" || appId!="")  	url += "/appId/"+appId;
if(type=="2" || apkId!="") 		url += "/apkId/"+apkId;


var swfu;
window.onload = function() {
	var settings = {
		flash_url : "/Public/default/app/swfupload/swfupload.swf",
		flash9_url : "/Public/default/app/swfupload/swfupload_fp9.swf",
		upload_url: url,
		post_params: {"PHPSESSID" : ""},
		file_size_limit : "300 MB",	// 300 MB
		file_types : "*.apk",
		file_types_description : "Apk Files",
		file_upload_limit : 1,  
		file_queue_limit : 0,
		custom_settings : {
			//progressTarget : "fsUploadProgress",
		},
		debug: false,

		// Button settings
		button_placeholder_id: "apkupload",
//		button_image_url: "images/TestImageNoText_65x29.png",
		button_width: "128",
		button_height: "34",
		button_text: '<span class="theFont"><em>+</em>上传新应用</span>',
		button_text_style: ".theFont { background: #f15335; color: #fff; font-size: 14px; text-align: center;line-height: 34px;height: 34px;padding: 0 28px;overflow: hidden; }",
//		button_text_left_padding: 12,
//		button_text_top_padding: 3,
		
		
		// The event handler functions are defined in handlers.js
		swfupload_preload_handler : preLoad,
		swfupload_load_failed_handler : loadFailed,
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete	// Queue plugin event
	};

	swfu = new SWFUpload(settings);
}