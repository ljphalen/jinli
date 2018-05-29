// 文件上传
jQuery(function() {
	var BASE_URL = '/Public/default/app/webuploader';
	
	var $ = jQuery;
    var showError = function( code ) {
        switch( code ) {
            case 'exceed_size':
                text = '文件大小超出限制';
                break;
            case 'interrupt':
                text = '上传暂停';
                break;
            case 'error':
                text = '上传出错';
                break;
            case 'invalid':
                text = '文件格式不符合要求';
                break;
            default:
                text = code;
                break;
        }
        return text;
    };

    var icon_upload_init = function(_id, icon_type) {
    	var url = "/apps/iconUpload";
    	var appId = $("#app_id").val();
    	var apkId = $("#apk_id").val();
    	var _icon_id = _id.replace("Upload","");
    	var icon_id = $(_icon_id).val();
    	url += "/app_id/"+appId+"/apk_id/"+apkId+"/icon_type/"+icon_type+"/icon_id/"+icon_id;
    	
    	var uploader;
    	
    	uploader = WebUploader.create({
            // 不压缩image
            resize: false,
            accept: {
            	title: 'Images',
            	extensions: 'png',
                mimeTypes: 'image/*'
            },
            // swf文件路径
            swf: BASE_URL + '/flash/Uploader.swf',
            // 文件接收服务端。
            server: url,
            // 自动上传
            auto: true,
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            duplicate:true,
            pick: _id,
            fileNumLimit: 1,
            fileSizeLimit: 2 * 1024 * 1024,    // 2 M
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
            	$("#largeIconImg").attr("src",data.picurl);
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
        
        uploader.on('error', function(file) {
        	
        });
    };
    
    var copyright_upload_init = function(_id) {
    	var url = "/apps/copyrightUpload";
    	var appId = $("#app_id").val();
    	var apkId = $("#apk_id").val();
    	url += "/app_id/"+appId+"/apk_id/"+apkId;
    	
    	var uploader;
    	
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
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            duplicate:true,
            pick: _id,
            fileSizeLimit: 10 * 1024 * 1024,    // 10 M
            fileSingleSizeLimit: 10 * 1024 * 1024    // 10 M
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
        uploader.on( 'uploadSuccess', function( file, data ) {
            if(data.errorCode==1){
            	alertDialog(data.error);
            	return false;
            }
            if(data.picurl) {
            	$("#copyrightUploadImg").attr("src",data.picurl);
            }
        });

        uploader.on( 'uploadError', function( error ) {
        	alertDialog("上传失败，请重新上传");
        	console.log(error);
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
        	if(error == 'F_DUPLICATE')
        		alertDialog("相同的文件不能重复上传");
        	else if(error == 'Q_EXCEED_NUM_LIMIT')
        		alertDialog("添加的文件数量超出限制");
        	else if(error == 'Q_EXCEED_SIZE_LIMIT')
        		alertDialog("添加的文件大小超出限制");
        	else
        		alertDialog("上传失败，请重新上传");
        	console.log(error);
        });
    };
	
    icon_upload_init("#largeIconUpload", 5);
	copyright_upload_init("#copyrightUpload");

});

