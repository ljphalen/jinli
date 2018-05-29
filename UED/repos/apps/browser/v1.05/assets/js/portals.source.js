(function(iCat, $){
	
	iCat.app('MH', function(){
		return {
			version: '1.0'
		};
	});
	
	iCat.mix(MH, {
		scrollPic: function(){
			if(!$('#mainfocus')[0]) return;
			iCat.include('~slidePic.js', function(){
				var mf = $('#mainfocus'), len = mf.find('li').length, w = mf.find('.ui-slide-scrollbox').width(),
					title = mf.find('.ui-slide-title span'), i = 1,
					mainSlide = new SlideAuto();
				mf.find('.ui-slide-scroll').width(w*len*2);
				mainSlide.init({
					touchId:".mainfocus",
					loop:true,
					auto:true,
					afterSlide:function(){
						i>=3? i=1 : i++;
						title.hide().eq(i-1).show();
					}
				});
			});
		},
		
		scrollPro: function(){
			if(!$('.scroll-pro.isTouch')[0]) return;
			iCat.include('~slidePic.js', function(){
				var mf = $('.scroll-pro'), len = mf.find('li').length, w = mf.find('.ui-slide-scrollbox').width(),
					mainSlide = new SlideAuto();
				mf.find('.ui-slide-scroll').width(w*len);
				mainSlide.init({
					touchId:".scroll-pro",
					loop:false,
					auto:false/*,
					afterSlide:function(){
						if(!$('.pro-detail')) return;
						$.post('', {}, function(data){
							data = '{"argus":"网络模式：GSM，WCDMA<br />外观设计：直板<br />主屏尺寸：3.7英寸 800x480像素<br />触摸屏：电容屏，多点触控<br />摄像头像素：前：30万像素，后：500万像素 C...<br />操作系统：Android OS 2.3<br />蓝牙传输：支持<br />GPS导航：内置GPS，支持A-GPS<br />存储卡：MicroSD卡，支持App2SD功能", "func":"视频播放：支持MP4/3GP等格式<br />音频播放：支持MP3/WMA/WAV等格式 <br />图形格式：支持JPEG等<br />游戏：内置，支持下载<br />GPS导航：支持<br />电子罗盘：支持指南针<br />传感器：支持重力感应器、光线传感器、距离传感器<br />数据传输：支持WIFI、IEEE 802.11n、WAPI<br />如影随行：支持<br />语音输入：支持<br />办公工具：支持TXT，Quick Office，Adobe PDF"}';
							var d = $.parseJSON(data);
							$('.J_tabMain:eq(0) p').html(d.argus);
							$('.J_tabMain:eq(1) p').html(d.func);
						});
					}*/
				});
			});
		},
		
		tab: function(){
			if(!$('.J_tabBox')[0]) return;
			var wrap = $('.J_tabBox'),
				handles = wrap.find('.J_tabHandle li'), mains = wrap.find('.J_tabMain');
			handles.click(function(){
				var i = handles.index($(this));
				handles.removeClass('selected').eq(i).addClass('selected');
				mains.hide().eq(i).show();
			});
		}
	});
	
	$(function(){
		MH.scrollPic();
		MH.scrollPro();
		MH.tab();
		
		//为了统计
		var cr = $.trim($('body').attr('dt-cr'));
		if(cr){
			
			$('a').click(function(evt){
				evt.preventDefault();
				
				//$.get(cr, {url:href, t:new Date().getTime()});
				/*if(!$('body').find('iframe[name=cr]')[0]){
					$('body').append('<iframe name="cr" src="" frameborder="0" class="hidden"></iframe>');
					$('body').find('iframe[name=cr]')[0].src = cr+'?url='+encodeURIComponent(this.href);
				}*/
				
				//$('body').find('iframe[name=cr]').remove();
				location.href = cr+'?url='+encodeURIComponent(this.href);
			});
		}
	});
})(ICAT, jQuery);