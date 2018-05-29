(function(iCat, $){
	
	iCat.app('AG', function(){
		iCat.require('jm',['~jm/jm.css','~jm/jm.js']);
		return {
			version: '1.0'
		};
	});
	
	iCat.mix(AG, {
		scrollPic: function(){
			if(!$('#mainfocus')[0]) return;
			iCat.include('./assets/js/slide.auto.js', function(){
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
		},
		
		textPic: function(){
			var tp = $('#J_textPic');
			if(!tp[0]) return;
			
			var url = tp.attr('dt-ajaxUrl'), hl = tp.siblings('.handle'), num = 1,
				
				fnAjax = function(p, l){
					$.get(url, {page:p}, function(data){
						var objData = $.parseJSON(data),
							strHtml = '<div class="wrap" style="left:'+(l? -48:48)+'em;"><ul>@ph</ul></div>', strli = '',
							arrCla = ['cor-blue', 'cor-orange', 'cor-red', 'cor-purple'], msg = l? '上页信息没有了':'下页信息没有了';
						if(!objData.success){
							alert(msg);
							return;
						}
						if(objData.data.curpage==1)
							hl.eq(0).addClass('disable');
						if(!objData.data.hasnext)
							hl.eq(1).addClass('disable');
						hl.eq(l? 1:0).removeClass('disable');
						
						num = objData.data.curpage;
						iCat.foreach(objData.data.list, function(i,v){
							//console.log(item[0]);
							strli += '<li class="'+arrCla[i]+'">'
									+	'<a href="'+v.link+'">'
									+		'<img src="'+v.img+'" alt=""><em><span>'+v.title+'</span></em>'
									+	'</a>'
									+'</li>'+(i==1? '</ul><ul>':'');
						});
						strHtml = strHtml.replace(/@ph/, strli);
						tp.append(strHtml).find('.wrap:first').animate({left:(l? 48:-48)+'em'}, 1000);
						tp.find('.wrap:last').animate({left:0}, 1000, function(){
							tp.find('.wrap:first').remove();
						});
					});
				};
			
			hl.eq(0).click(function(){
				/*hl.eq(1).removeClass('disable');
				if(num>1){
					num--;
					fnAjax(num, 3);
				} else {
					$(this).addClass('disable');
				}*/
				if($(this).hasClass('disable')) return;
				fnAjax(num-1,true);
			});
			hl.eq(1).click(function(){
				/*hl.eq(0).removeClass('disable');
				if(num<4){
					num++;
					fnAjax(num, 3);
				} else {
					$(this).addClass('disable');
				}*/
				if($(this).hasClass('disable')) return;
				fnAjax(num+1,false);
			});
			
			iCat.use('jm', function(){
				tp.find('.wrap').live('swipeleft', function(){
					if(hl.eq(0).hasClass('disable')) return;
					fnAjax(num-1,true);
				}).live('swiperight', function(){
					if(hl.eq(1).hasClass('disable')) return;
					fnAjax(num+1,false);
				});
			});
		}
	});
	
	$(function(){
		AG.scrollPic();
		AG.textPic();
	});
})(ICAT, jQuery);