(function(iCat, $){
	
	iCat.app('Store', function(){
		iCat.mix(iCat.mods, {
			'jm': [iCat.sysRef+'/lib/jquerymobile/jm.css',iCat.sysRef+'/lib/jquerymobile/jm.js']
		});
		return {
			version: '0.6'
		}
	});
	
	iCat.mix(Store, {
		
		scrollPic: function(){
			if(!$('#mainfocus')[0]) return this;
			iCat.include('~slidePic.js', function(){
				var mf = $('#mainfocus'), len = mf.find('li').length, w = mf.find('.ui-slide-scrollbox').width(),
					title = mf.find('.ui-slide-title span'), i = 1,
					mainSlide = new SlideAuto();
				mf.find('.ui-slide-scroll').width(w*len*2);
				mainSlide.init({
					touchId:".mainfocus",
					loop:true,
					auto:true
				});
			});
			
			return this;
		},
		
		showMore: function(){
			var btnMore = $('.JS-readMore');
			if(!btnMore[0]) return this;
			
			btnMore.find('a').toggle(function(evt){
				evt.preventDefault();
				$(this).find('span').html('收起<i>&gt;&gt;</i>');
				btnMore.prev('.JS-hide').show()
					.prev('p').hide();
			}, function(evt){
				evt.preventDefault();
				$(this).find('span').html('展开<i>&gt;&gt;</i>');
				btnMore.prev('.JS-hide').hide()
					.prev('p').show();
			});
			
			return this;
		},
		
		swapPic: function(){
			var wrap = $('.JS-swapPic');
			if(!wrap[0]) return this;
			
			var oUL = wrap.find('ul'), len = wrap.find('li').length,
				wLi = wrap.find('li').width(), i = 0, n = len;
			oUL.width(wLi*len);
			iCat.use('jm', function(){
				wrap.find('#J_handle').change(function(){console.log(this.value);
					/*wLi = wrap.find('li').width();
					oUL.width(wLi*len);
					var wWrap = wrap.find('.pic-wrap').width(),
						per = this.value/100, w = wLi*len-wWrap;
					oUL.css('left', -(w>0? w:0)*per+'px');*/
					/*wLi = wrap.find('li').width();
					oUL.width(wLi*len);
					
					var wWrap = wrap.find('.pic-wrap').width(),
						w = wLi*len-wWrap;
					if(w<=0){
						n = 0;
						return;
					}
					n = Math.floor(w/wLi);
					i = this.value;
					if(i==n) return;
					oUL.css('left', -i*wLi+'px');*/
				});
				
				oUL.bind('swipeleft', function(){
					wLi = wrap.find('li').width();
					oUL.width(wLi*len);
					
					var wWrap = wrap.find('.pic-wrap').width(),
						w = wLi*len-wWrap;
					n = Math.ceil(w/wLi);
					
					if(w<=0){
						n = 0;
						oUL.animate({left: -wLi+'px'},500, function(){
							oUL.animate({left: 0},500);
						});
						return;
					}
					if(i==n) return;
					i++;
					oUL.animate({left: -(i*wLi)+'px'},500);
				});
				oUL.bind('swiperight', function(){
					wLi = wrap.find('li').width();
					oUL.width(wLi*len);
					
					var wWrap = wrap.find('.pic-wrap').width(),
						w = wLi*len-wWrap;
					n = Math.ceil(w/wLi);
					
					if(w<=0){
						n = 0;
						oUL.animate({left: wLi+'px'},500, function(){
							oUL.animate({left: 0},500);
						});
						return;
					}
					if(i==0) return;
					i--;
					oUL.animate({left: -(i*wLi)+'px'},500);
				});
				$(window).bind('orientationchange',function(e){
					wrap.toggleClass('turn');
				});
			});
			
			return this;
		},
		
		loadMore: function(){
			var btnLoad = $('#J_loadMore');
			if(!btnLoad[0]) return this;
			
			var num = 2;
			btnLoad.click(function(evt){
				evt.preventDefault();
				
				if(btnLoad.hasClass('disable')){
					return;
				} else {
					var url = btnLoad.attr('dt-ajaxUrl');
					btnLoad.addClass('disable').find('a').html('<img src="'+iCat.appRef+'/pic/load.gif" alt="" />');
					$.get(url, {page: num}, function(data){
						//data = '{"success":true,"msg":"","data":{"list":[{"name":"\u7528\u9014\u7279uuteyu","resume":"\u7279uyuirtjtddsage \u800c\u4ed6\u70ed\u60c5\u7279\u8ba9\u4ed6\u8ba4\u4e3a\u5207\u5c14\u5929\u7136\u6e29\u6cc9\u7279\u8ba4\u4e3a\u592a\u70ed\u800c\u5979\u70edtry\u5de5\u4f1a\u6cd5\u4eba\u63d0\u9ad8\u548c\u4eba ","link":"http:\/\/erweima.gionee.com\/apps\/Dinosaurs.apk ","img":"http:\/\/3g.tongtianjie.com\/attachs\/\/ad\/201208\/170809.gif"},{"name":"\u8ba2\u91d1\u95e8\u7a7a\u95f4","resume":"\u6cd5\u56fd\u7edd\u70ed\u8003\u8651\u8003\u8651\u7684\u53d1\u7ed9\u60f9","link":"http:\/\/g.apk.anzhi.com\/apk\/201207\/06\/com.likeapp.game.bubbleshooter_93289300_0.apk ","img":"http:\/\/3g.tongtianjie.com\/attachs\/\/ad\/201208\/165949.jpg"},{"name":"huadong","resume":"huadonghuadonghuadonghuadonghuadonghuadong","link":"http:\/\/g.apk.anzhi.com\/apk\/201206\/15\/com.feelingtouch.NinjaRun_78678800_0.apk ","img":"http:\/\/3g.tongtianjie.com\/attachs\/\/ad\/201208\/164209.jpg"},{"name":"\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d","resume":"\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d\u8d44\u6e90\u8d44\u6599\u4e0b\u8f7d\u4e0b\u8f7d","link":"http:\/\/g.apk.anzhi.com\/apk\/201203\/28\/com.halfbrick.fruitninja_25469100_0.apk ","img":"http:\/\/3g.tongtianjie.com\/attachs\/\/ad\/201208\/162509.jpg"},{"name":"\u50f5\u5c38\u56f4\u57ce","resume":"\u4e00\u6b3e\u7b2c\u4e09\u4eba\u79f0\u5c04\u51fb\u6e38\u620f,\u7f8e\u519b\u7a81\u7136\u6536\u5230\u6765\u81eaAnderson\u535a\u58eb\u7684\u6551\u63f4\u8bf7\u6c42\uff0c\u6d3e\u90633\u540d\u4e13\u5458\u53bb\u8c03\u67e5\u6b64\u4e8b\u3002","link":"http:\/\/adown.myaora.net:81\/test\/apkfile\/33b4bd544be98c79977fcbfc3c33ee47.apk","img":"http:\/\/3g.tongtianjie.com\/attachs\/\/ad\/201208\/155839.png"}],"hasnext":true,"curpage":2}}';
						var objData = $.parseJSON(data), strli = '';
						
						num = objData.data.curpage+1;
						iCat.foreach(objData.data.list, function(i,v){
							strli += '<li>'
									+	'<a href="'+v.alink+'" class="intro clearfix">'
									+		'<div class="pic"><img src="'+v.img+'" alt="" /></div>'
									+		'<div class="desc">'
									+			'<h2><strong>'+v.name+'</strong></h2>'
									+			'<p><span>'+v.resume+'</span></p>'
									+		'</div>'
									+	'</a>'
									+	'<a href="'+v.link+'" class="download"><span>下载</span></a>'
									+'</li>';
						});
						btnLoad.prev('ul').append(strli);
						btnLoad.removeClass('disable').find('a').html('<span>点击加载更多</span>');
						if(!objData.data.hasnext) btnLoad.addClass('disable');
					});
				}
			});
			
			return this;
		}
	});
	
	$(function(){
		Store.scrollPic().loadMore();
		Store.showMore();
		Store.swapPic(); 
	});
})(ICAT, jQuery);