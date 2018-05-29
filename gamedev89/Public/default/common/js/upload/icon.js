$(function () {
	var url = "/apps/iconUpload";
	var appId = $("#app_id").val();
	var apkId = $("#apk_id").val();
	url += "/app_id/"+appId+"/apk_id/"+apkId;
	$("#smallicon, #middleicon, #bigicon").bind("click",function(){
		var obj = $(this);
		var iconId = obj.attr("iconid");
		var type = obj.attr("icontype");
		if(iconId!='' && iconId!=undefined){
			url += "/icon_id/"+iconId;
		}
		if(type!='' && type!=undefined){
			url += "/icon_type/"+type;
		}
		$("#icon_id").val(iconId);
		$("#icon_type").val(type);
	});

    $("#smallicon, #middleicon, #bigicon").fileupload({
        url: url,
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(png)$/i,
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
            	var icon_type = $("#icon_type").val();
            	var imgId = (icon_type==2)?"smallIconImg":(icon_type==3?"middleIconImg":"bigIconImg");
            	$("#"+imgId).attr("src",file.picurl);
            }
            if(file.errorCode==1){
            	alertDialog(file.error);
            	return false;
            }
        });
    }).bind('fileuploadfail', function (e, data) {
        $.each(data.files, function (index, file) {
        	alertDialog("您的浏览器版本太低，推荐使用Chrome浏览器上传");
            return false;
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
