(function($, iCat){
	iCat.inc(['/jquerymobile/jm.css','/jquerymobile/jm.js']);
	var initData = window.prompt('gn://GNNavigationData/select','');
	
	$(function(){
		var wrap = $('#J_labelBox'), addBtn = $('#J_addLabel'),
			_refresh = function(oLabel){
				var strHtml = '';
				oLabel = oLabel.replace(/\s|\n/g,'');
				oLabel = eval('('+oLabel+')');
				
				if(oLabel.result!='0'){
					alert('未请求到数据...');
					return;
				}
				iCat.foreach(oLabel.data, function(i, v){
					strHtml += '<li'+(v.del=='false'? ' class="delDisabled"':'')+' data-labelId='+v.id+'>'
							+		'<a href="'+v.url+'" dt-href="'+v.url+'">'
							+			(v.del=='false'? '':'<div class="del-icon J_delIcon"><s>×</s></div>')
							+			'<div class="pic"><img src="data:image/png;base64,'+v.imagename+'" alt="'+v.title+'" /></div>'
							+			'<div class="title"><span>'+v.title+'</span></div>'
							+		'</a>'
							+	'</li>';
				});
				addBtn.before(strHtml);
			};
		
		_refresh(initData);
		
		wrap.find('li').live('taphold', function(){
			var me = $(this);
			
			wrap.find('li:not(#J_addLabel)').addClass('swing');
			me.addClass('on').live('mouseup', function(){
				if(me.hasClass('delDisabled')){
					me.removeClass('on');
				} else {
					setTimeout(function(){
						me.removeClass('on').addClass('delAbled');
					},100);
				}
			})
			.siblings('li:not(.delDisabled, #J_addLabel)').addClass('delAbled');
			
			wrap.find('.J_delIcon').show();
			wrap.find('li a').removeAttr('href');
			addBtn.hide();
		});
		
		wrap.find('li.delAbled').live('vclick', function(){
			//request
			var text = window.prompt("gn://GNNavigationData/delete","{_id:"+this.getAttribute('data-labelId')+"}");
			text = text.replace(/\s|\n/g,'');
			text = eval('('+text+')');
			if(text.result!='0'){
				alert('删除不成功，请重试...');
				return;
			}
			$(this).remove();
			wrap.find('li:not(#J_addLabel)').removeClass('delAbled swing');
			wrap.find('.J_delIcon').hide();
			wrap.find('li a').each(function(){
				this.href = this.getAttribute('dt-href');
			});
			addBtn.show();
		});
		
		$(document).click(function(evt){
			var me = $(evt.target), p = me.parents('li');
			if(!p.hasClass('swing')){
				wrap.find('li:not(#J_addLabel)').removeClass('delAbled swing');
				wrap.find('.J_delIcon').hide();
				wrap.find('li a').each(function(){
					this.href = this.getAttribute('dt-href');
				});
				addBtn.show();
			}
		});
		
		addBtn.click(function(evt){
			evt.preventDefault();
			var json = window.prompt("gn://GNAppInterface/startactivity","{\"activityName\":\"com.android.browser.gnnavigation.GNAddNavSiteActivity\"}");
			_refresh(json);
		});
	});
	
})(jQuery, ICAT);