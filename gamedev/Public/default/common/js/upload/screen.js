$(function () {
	var url = "/apps/screenUpload";
	var appId = $("#app_id").val();
	var apkId = $("#apk_id").val();
	url += "/app_id/"+appId+"/apk_id/"+apkId;
    $('#screenUpload').fileupload({
        url: url,
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(jpeg|jpg|png)$/i,
        maxFileSize: 1024*1024*2 // 2M
    }).bind('fileuploadadd', function (e, data) {
        $.each(data.files, function (index, file) {
            if (file.error) {
            	alertDialog(file.error);
                return false;
            }
        });
    }).bind('fileuploadprocessalways', function (e, data) {
        var index = data.index,
	        file = data.files[index];
	    if (file.error) {
	    	alertDialog(file.error);
            return false;
	    }
	}).bind('fileuploaddone', function (e, data) {
        $.each(data.result, function (index, file) {
            if(file.picurl) {
            	var html = '<div class="col-md-3 screen-unit">';
    			html += '<button type="button" class="close deleteBtn" fileid="'+file.id+'" data-dismiss="alert" aria-hidden="true">×</button>';
    			html += '<div class="thumbnail"><a href="javascript:void(0)"><img src="'+file.picurl+'" width="150" height="250"></a>';
    			html += '</div></div>';
    			$("#screenshotdiv").before(html);
    			
            }
            if(file.errorCode==1){
            	alertDialog(file.error);
            	return false;
            }
        });
        $(".deleteBtn").click(function(){
			var _id = $(this).attr("fileid");
			$.ajax({url:"/picture/del", data:"id="+_id});	
		});
    }).bind('fileuploadfail', function (e, data) {
        $.each(data.files, function (index, file) {
        	alertDialog("您的浏览器版本太低，推荐使用Chrome浏览器上传");
            return false;
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
