(function(iCat, $){
	
	iCat.app('GNG', function(){
		return {
			version: '0.6'
		};
	});
	
	iCat.mix(GNG, {
		
		scrollPic: function(){
			if(!$('#mainfocus')[0]) return this;
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
			
			return this;
		},
		
		toggle: function(){
			if(!$('.JS-handle')[0]) return this;
			
			var handle = $('.JS-handle'), ul = handle.prev('ul.J_toggleUL');
			
			handle.find('.wrap span').toggle(function(){
				ul.show();
				this.innerHTML = '收起';
			},function(){
				ul.hide();
				this.innerHTML = '展开';
			});
			
			return this;
		},
		
		dialog: function(){
			if(!$('#J_dialogBox')[0]) return this;
			
			var box = $('#J_dialogBox'), mask = box.next('.dialog-mask'),
				btnForm = $('#J_submitForm'), form = btnForm.parents('form'),
				formSelector = form.find('input,select,textarea');
			btnForm.click(function(evt){
				evt.preventDefault();
				
				/*var argus = form.serialize();
					argus = argus.replace(/=/g,'":"').replace(/&/g,'","');
					argus = $.parseJSON('{"'+argus+'"}');*/
				var argus = {},
					showTip = function(msg){
						var tip = form.find('.tip');
						tip.find('p').html(msg);
						tip.show();
						
						setTimeout(function(){
							tip.hide();
							tip.find('p').html('');
						},3000);
					};
				for(var i=0, len=formSelector.length; i<len; i++){
					if(formSelector.eq(i).attr('required') && formSelector[i].value==''){
						formSelector.eq(i).focus();
						showTip(formSelector.eq(i).attr('data-null'));
						return;
					}
					
					if(formSelector.eq(i).attr('pattern')){
						var reg = new RegExp(formSelector.eq(i).attr('pattern'));
						if(!reg.test(formSelector[i].value)){
							formSelector.eq(i).focus();
							showTip(formSelector.eq(i).attr('data-error'));
							return;
						}
					}
					
					argus[formSelector.eq(i).attr('name')] = formSelector[i].value;
				}
				
				$.post(form.attr('action'), argus, function(data){
					var oData = $.parseJSON(data),
						bw = box.width(), dw = $(document).width()-40;
					box.find('p span').html(oData.msg);
					box.css('left',(dw-bw)/2+'px').show();
					mask.show();
				});
			});
			
			box.find('.btn a').click(function(){
				box.hide();
				mask.hide();
				
				iCat.foreach(formSelector, function(){
					if($(this).attr('data-setNull')=='true')
						this.value = '';
				});
			});
			
			return this;
		},
		
		tearPaper: function(){
			if(!$('#J_tearPaper')[0]) return this;
			$('#J_tearPaper a').bind('click', function(evt){
				evt.preventDefault();
				
				var href = this.href, myP = $('#J_tearPaper'),
					bn = myP.prev('#mainfocus'), cs = myP.next('section'),
					hd = myP.parent().prev('header.hd');
				//myP.slideUp(1000);
				/*myP.slideDown(1000);*/
				myP.addClass('fadeOut');
				bn.addClass('bounceOutUp');
				hd.addClass('bounceOutUp');
				cs.addClass('bounceOutDown');
				
				setTimeout(function(){
					location.href = href;
				},600);
				
				setTimeout(function(){
					myP.removeClass('fadeOut');
					bn.removeClass('bounceOutUp');
					hd.removeClass('bounceOutUp');
					cs.removeClass('bounceOutDown');
				},1000);
			});
			
			return this;
		},
		
		textPic: function(){
			var tp = $('#J_textPic');
			if(!tp[0]) return this;
			
			var url = tp.attr('dt-ajaxUrl'), hl = tp.siblings('.handle'), num = 1,
				
				fnAjax = function(p, l){
					$.get(url, {page:p}, function(data){
						var objData = $.parseJSON(data),
							strHtml = '<div class="wrap" style="left:'+(l? -48:48)+'em;"><ul>@ph</ul></div>', strli = '',
							arrCla = ['cor2', 'cor3', 'cor3', 'cor4'], msg = l? '上页信息没有了':'下页信息没有了';
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
				if($(this).hasClass('disable')) return;
				fnAjax(num-1,true);
			});
			hl.eq(1).click(function(){
				if($(this).hasClass('disable')) return;
				fnAjax(num+1,false);
			});
			
			/*iCat.use('jm', function(){
				tp.find('.wrap').live('swipeleft', function(){
					if(hl.eq(0).hasClass('disable')) return;
					fnAjax(num-1,true);
				}).live('swiperight', function(){
					if(hl.eq(1).hasClass('disable')) return;
					fnAjax(num+1,false);
				});
			});*/
			
			return this;
		},
		
		showMore: function(){
			if(!$('.J_showMore')[0]) return this;
			
			var btnMore = $('.J_showMore'), num = 2;
			btnMore.click(function(evt){
				evt.preventDefault();
				
				if(btnMore.hasClass('disable')){
					return;
				} else {
					var url = btnMore.attr('dt-ajaxUrl');
					$.get(url, {page: num}, function(data){
						var objData = $.parseJSON(data), strli = '';
						
						num = objData.data.curpage+1;
						iCat.foreach(objData.data.list, function(i,v){
							//console.log(item[0]);
							strli += '<li>'
									+	'<div class="msg">'
									+		'<span>'+v.react+(v.mobile? v.mobile:'')+'<s>'+v.create_time+'</s></span>'
									+	'</div>'
									+	'<div class="reply"><p>'+v.reply+'</p></div>'
									+'</li>';
						});
						
						$('.show-order ul').append(strli);
						if(!objData.data.hasnext) btnMore.addClass('disable');
					});
				}
			});
		}
	});
	
	$(function(){
		GNG.scrollPic().tearPaper().textPic();
		GNG.toggle();
		GNG.dialog();
		GNG.showMore();
	});
})(ICAT, jQuery);