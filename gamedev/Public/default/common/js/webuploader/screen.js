// 文件上传
jQuery(function() {
	var BASE_URL = '/Public/default/app/webuploader';
	
	var $ = jQuery,
		state = 'pending',
		uploader;
	
	var url = "/apps/screenUpload";
	var appId = $("#app_id").val();
	var apkId = $("#apk_id").val();
	
	url += "/app_id/"+appId+"/apk_id/"+apkId;
	
    uploader = WebUploader.create({
        // 不压缩image
        resize: false,
        accept: {
        	title: 'Images',
        	extensions: 'jpeg,jpg,png',
            mimeTypes: 'image/*'
        },
        // swf文件路径
        swf: BASE_URL + '/flash/Uploader.swf',
        // 文件接收服务端。
        server: url,
        // 自动上传
        auto: true,
        duplicate:true,
        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: '#screenUpload',
        fileNumLimit: 15,
        fileSizeLimit: 10 * 1024 * 1024,    // 10 M
        fileSingleSizeLimit: 2 * 1024 * 1024    // 2 M
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function( file ) {
        if ( file.getStatus() === 'invalid' ) {
        	alertDialog(showError( file.statusText ));
            return false;
        }
    });
    uploader.on( 'filesQueued', function( file ) {
    	if(file.length == 0)
    		alertDialog( '文件不合法，请重新选择' );
    });

    uploader.on( 'uploadSuccess', function( file, ret ) {
    	var data = ret[0];
    	
    	if(data.picurl) {
        	var html = '<div class="col-md-3 screen-unit">';
			html += '<button type="button" class="close deleteBtn" fileid="'+data.id+'" data-dismiss="alert" aria-hidden="true">×</button>';
			html += '<div class="thumbnail"><a href="javascript:void(0)"><img src="'+data.picurl+'" width="150" height="250"></a>';
			html += '</div></div>';
			$("#screenshotdiv").before(html);
			$(".deleteBtn").click(screen_del);
        }
        if(data.errorCode==1){
        	alertDialog(data.error);
        	return false;
        }
    });

    uploader.on( 'uploadError', function( file ) {
        var error = '<p>您的浏览器版本太低或网络异常，文件上传失败</p><p>推荐使用Chrome浏览器、360浏览器极速模式、IE10及以上版本</p>';
        $err.text(error).appendTo("#apk div p");
        $("#closeTime, #closeBtn").show();
    });

    uploader.on( 'all', function( type ) {
        if ( type === 'startUpload' ) {
            state = 'uploading';
        } else if ( type === 'stopUpload' ) {
            state = 'paused';
        } else if ( type === 'uploadFinished' ) {
            state = 'done';
        }
    });
    
    uploader.on('error', function(error) {
    	if(error == 'F_DUPLICATE'){
    		alertDialog("相同的文件不能重复上传");
    	}
    	else if(error == 'Q_EXCEED_NUM_LIMIT'){
    		alertDialog("一次上传最多不能超过15张");
    	}
    	else{
    		alertDialog("上传失败，请重新上传");
    	}
    	if(console) console.log(error);
    });

});

