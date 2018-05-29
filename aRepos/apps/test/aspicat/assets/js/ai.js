(function(iCat, $){
	iCat.app('AI');
	
	iCat.mix(AI, {
		demo_dealUrl: function(){
			if(!$('#J_testUrl')[0]) return this;
			
			var ipt = $('#J_testUrl'), btn = $('#J_testit');
			iCat.include('../util.js');
			btn.click(function(){
				var url = iCat.util.dealUrl(ipt.val());
				url = /demo\.acued|jndream\.net/i.test(url)? url.replace(/.*\/apps/, 'apps') : url;
				$('.JS-panel span').html(url);
			});
			
			return this;
		},
		
		demo_mix: function(){
			if(!$('#J_rObj')[0] && !$('#J_sObj')[0]) return this;
			
			var r = $('#J_rObj'), s = $('#J_sObj'),
				wl = $('#J_wlObj'), ov = $('#J_ovObj'),
				btn = $('#J_testit');
			btn.click(function(){
				var a = $.parseJSON(r.val()),
					b = $.parseJSON(s.val()),
					c = eval(wl.val()),
					d = ov.val()==1? true : false;
				
				iCat.mix(a, b, c, d);
				var str = '';
				iCat.foreach(a,function(k,v){
					str += '"'+k+'":"'+v+'", ';
				});
				str = '{@}'.replace(/@/, str.replace(/,\s$/,''));
				$('.JS-panel span').html(str);
			});
			
			return this;
		},
		
		demo_app: function(){
			if(!$('#J_testApp')[0]) return;
			
			var codeObj = $('#J_testApp'), btn = $('#J_testit');
			btn.click(function(){
				eval(codeObj.val());
			});
			
			return this;
		}
	});
	
	$(function(){
		AI.demo_dealUrl().demo_mix().demo_app();
	});
})(ICAT, jQuery);