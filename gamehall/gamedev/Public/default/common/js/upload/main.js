$(function () {
	var url = "/apps/apkUpload";
	var type = $("#type").val();
	var appId = $("#app_id").val();
	var apkId = $("#apk_id").val();
	url += "/type/"+type;
	if(type=="1" || appId!="") 
		url += "/appId/"+appId;
	if(type=="2" || apkId!="") 
		url += "/apkId/"+apkId;
    $('#apkupload').fileupload({
        url: url,
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(apk)$/i,
        maxFileSize: 1024*1024*300 // 300 MB
    }).bind('fileuploadadd', function (e, data) {
    	$("#apk").html('');
    	$("#existAppAlert").hide();
        data.context = $('<div/>').appendTo('#apk');
        $.each(data.files, function (index, file) {
            var node = $('<p/>').append($('<span/>').text(file.name));
            node.appendTo(data.context);
            if (file.error) {
                node.appendTo($('<strong data-ng-show="file.error" class="error text-danger"/>').text(file.error));
            }
        });
        $("#closeTime, #closeBtn").hide();
        $("#uploadMask").addClass("fancybox-display");
        $('#jumbotron').show();
    }).bind('fileuploadprocessalways', function (e, data) {
        var index = data.index,
	        file = data.files[index],
	        node = $(data.context.children()[index]);
        if (file.preview) {
            node.prepend(file.preview);
        }
        if (file.error) {
            node.append($('<span class="text-danger"/>').text(file.error));
            $("#closeTime, #closeBtn").show();
        }
    }).bind('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress').css(
            'width',
            progress + '%'
        );
        if(progress==100)	$("#progress").html('<div style="font-size:13px">上传结果处理中，请等待...</div>');
    }).bind('fileuploaddone', function (e, data) {
        $.each(data.result.files, function (index, file) {
            if (file.error && file.errorCode) {
                var error = $('<span class="text-danger"/>').text(file.error.error ? file.error.error : file.error);
                $(data.context.children()[index]).append(error);
                $("#closeTime, #closeBtn").show();
                if(file.errorCode==1)
                	$("#existAppAlert").show();
            }else if(file.apk) {
            	var apkId = file.apk.apk_id;
            	window.location.href="/apps/info/id/"+apkId+".html?fileuploaddone=1";
            }
        });
    }).bind('fileuploadfail', function (e, data) {
        $.each(data.files, function (index, file) {
            var error = $('<span class="text-danger"/>').html('<p>您的浏览器版本太低或网络异常，文件上传失败</p><p>推荐使用Chrome浏览器、360浏览器极速模式、IE10及以上版本</p>');
            $(data.context.children()[index]).append(error);
            $("#closeTime, #closeBtn").show();
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
