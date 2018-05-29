/* Demo Note:  This demo uses a FileProgress class that handles the UI for displaying the file name and percent complete.
The FileProgress class is not part of SWFUpload.
*/

function preLoad() {
	if (!this.support.loading) {
		alert("您需要安装Flash Player 9.028或者更高版本，才能使用SWFUpload上传。");
		return false;
	}
}
function loadFailed() {
	alert("由于未知原因，加载SWFUpload失败。");
}

function fileQueued(file) {
	$("#apk").html('');
	$("#existAppAlert").hide();
    $("#closeTime, #closeBtn").hide();
    $("#uploadMask").addClass("fancybox-display");
    $('#jumbotron').show();
}

function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("您已超出了可以上传的文件个数");
			return;
		}
		var err_msg = "";

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			err_msg = "文件太大";
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			err_msg = "不允许上传 0 Byte大小的文件"
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			err_msg = "文件类型错误";
			break;
		default:
			if (file !== null) {
				err_msg = "未知错误";
			}
			break;
		}
		console.log("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
		var context = $('<div/>').appendTo('#apk');
	    var node = $('<p/>').append($('<span/>').text(file.name));
	    node.appendTo(context);
	    if (err_msg!="") {
	        node.appendTo($('<strong id="upload-err-msg" data-ng-show="file.error" class="error text-danger"/>').text(err_msg));
	        $("#closeTime, #closeBtn").show();
	    }
	} catch (ex) {
        console.log(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		//自动开始上传
		this.startUpload();
	} catch (ex)  {
        console.log(ex);
	}
}

function uploadStart(file) {
	try {
		
	}
	catch (ex) {}
	
	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		
		$('#progress').css(
            'width',
            percent + '%'
        );
        if(percent==100)	$("#progress").html('<div style="font-size:13px">上传结果处理中，请等待...</div>');

	} catch (ex) {
		console.log(ex);
	}
}

function uploadSuccess(file, serverData) {
	try {
		var data = serverData.files[0];
		
		if (data.error && data.errorCode) {
            var error = $('<span class="text-danger"/>').text(data.error.error ? data.error.error : data.error);
            $("#upload-err-msg").html(error);
            $("#closeTime, #closeBtn").show();
            if(file.errorCode==1)
            	$("#existAppAlert").show();
        }else if(file.apk) {
        	var apkId = data.apk.apk_id;
        	window.location.href="/apps/info/id/"+apkId+".html?fileuploaddone=1";
        }
		

	} catch (ex) {
		console.log(ex);
	}
}

function uploadError(file, errorCode, message) {
	try {

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			console.log("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			console.log("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			console.log("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			console.log("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			console.log("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			console.log("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			progress.setStatus("Unhandled Error: " + errorCode);
			console.log("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        console.log(ex);
    }
}

function uploadComplete(file) {
	var error = $('<span class="text-danger"/>').html('<p>您的浏览器版本太低或网络异常，文件上传失败</p><p>推荐使用Chrome浏览器、360浏览器极速模式、IE10及以上版本</p>');
    $("#upload-err-msg").html(error);
    $("#closeTime, #closeBtn").show();
}

// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
//	var status = document.getElementById("divStatus");
//	status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
}
