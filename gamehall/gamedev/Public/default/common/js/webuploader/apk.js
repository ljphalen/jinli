// 文件上传
jQuery(function() {
	var BASE_URL = '/Public/default/app/webuploader';

	var $ = jQuery,
		$list = $('#thelist'),
		state = 'pending',
		uploader;
	
	var url = "/apps/apkUpload";
	var type = $("#type").val() || 0;
	var appId = $("#app_id").val() || 0;
	var apkId = $("#apk_id").val() || 0;
	
	url += "/type/"+type;
	if(type=="1" || appId!="") 
		url += "/appId/"+appId;
	if(type=="2" || apkId!="") 
		url += "/apkId/"+apkId;
	
	if ( !WebUploader.Uploader.support() ) {
        if ( isNaN( flashVersion ) || flashVersion < 11 ) {
            alert( '您尚未安装flash播放器或当前flash player 的版本过低于 11，请升级!');
            return;
        }
    }
    
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
    
    var $err = $('<span class="text-danger"/>');

    uploader = WebUploader.create({
        // 不压缩image
        resize: false,
        timeout: 10*60*1000, //10分钟超时
        accept: {
        	title: 'Android',
        	extensions: 'apk',
            mimeTypes: 'application/vnd.android.package-archive'
        },
        // swf文件路径
        swf: BASE_URL + '/flash/Uploader.swf',
        // 文件接收服务端。
        server: url,
        // 自动上传
        auto: true,
        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: '#apkupload',
        fileNumLimit: 1,
        fileSizeLimit: 200 * 1024 * 1024,    // 300 M
        fileSingleSizeLimit: 200 * 1024 * 1024    // 300 M
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function( file ) {
    	//检查用户有没有登陆超时
        $.getJSON( "/login/login_status", function( data ) {
        	if(data.uid < 1)
        		window.location.reload(true);
        	return false;
        });
    	
        $("#apk").html('');
    	$("#existAppAlert").hide();
        var context = $('<p/>').appendTo($('<div/>').appendTo('#apk'));
        var node = $('<span id="file_name"/>').text(file.name);
        node.appendTo(context);
        if ( file.getStatus() === 'invalid' ) {
        	$err.text(showError( file.statusText )).appendTo(context);
        }
        $("#closeTime, #closeBtn").hide();
        $("#uploadMask").addClass("fancybox-display");
        $('#jumbotron').show();
    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        $('#progress').css(
            'width',
            percentage * 100 + '%'
        );
        if(percentage==1)
        	$("#progress").html('<div style="font-size:13px">上传结果处理中，请等待...</div>');
    });

    uploader.on( 'uploadSuccess', function( file, ret ) {
    	if(typeof file =='object' && typeof ret =='object' && typeof ret.files =='object')
    	{
    		var data = ret.files[0];
        	if (data.error) {
                var error = data.error.error ? data.error.error : data.error;
                $err.text(error).appendTo("#apk div p");
                $("#closeTime, #closeBtn").show();
                if(data.errorCode==1)
                	$("#existAppAlert").show();
                if(data.errorCode==2)
                	$('body').click(function(){window.location.reload();});
            }else if(data.apk) {
            	var apkId = data.apk.apk_id;
            	window.location.href="/apps/info/id/"+apkId+".html?fileuploaddone=1";
            }
    	}else{
    		var error = '<p>上传超时<br />网络异常断开或者您已经登陆超时，请刷新页面查看是否上传成功<br />'
    			+'如文件大于200M或者网络不稳定，推荐您使用ftp上传</p>';
    		$err.html(error).appendTo("#apk div p");
            $("#closeTime, #closeBtn").show();
            if(data.errorCode==1)
            	$("#existAppAlert").show();
    	}
    });

    uploader.on( 'uploadError', function( file, reason ) {
    	if(reason == 'abort')
    	{
    		$("#jumbotron").hide();
    		return;
    	}
    	
    	var error = '<p>上传失败:'+reason+'<br />可能是由于网络异常或者您已经登陆超时，请刷新页面重新上传</p>';
    	if(reason == 'server' || reason == 'http')
    		var error = '<p>上传失败<br />可能是由于系统错误或者您已经登陆超时，请刷新页面重新上传</p>';
    	if(reason == 'timeout')
    		var error = '<p>上传超时<br />网络异常断开，上传结果未知，请刷新页面查看是否上传成功<br />'
    			+'如文件大于200M或者网络不稳定，推荐您使用ftp上传</p>';

    	$err.html(error).appendTo("#apk div p");
    	$("#closeTime, #closeBtn").show();
    });

    uploader.on( 'uploadComplete', function( file ) {
        
    });

    uploader.on( 'all', function( type, file ) {
    	if ( type == 'filesQueued') {
    		if(file.length == 0 && typeof golocation == "undefined" )
    			alert('文件不合法，请重新上传');
    	}
        if ( type === 'startUpload' ) {
            state = 'uploading';
        } else if ( type === 'stopUpload' ) {
            state = 'paused';
        } else if ( type === 'uploadFinished' ) {
            state = 'done';
        }
    });
    
    uploader.on('error', function(file) {
    	if('Q_EXCEED_SIZE_LIMIT' == file){
    		alert("超大文件，请使用FTP上传");
    		window.location.href="/apps/ftpupload.html";
    		golocation = true;
    	}else
    		alert("未知错误，请重新上传");
    });

});