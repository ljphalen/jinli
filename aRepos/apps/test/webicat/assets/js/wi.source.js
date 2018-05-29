/*(function(iCat){
	iCat.app('WI');
	
	iCat.mix(WI, {
		fn: function(){
			//alert('hello, iCat');
			$('body').css({'height':'500px', 'background':'#f90'})
				.click(function(){
					alert('Hello, iCat library...');
				});
		}
	});
	
	//WI.fn();
})(ICAT);*/
(function(iCat){
	var timebgein = (new Date()).getTime();
	iCat.app('WI');
	
	iCat.mix(WI, {
		fn: function(){
			//alert('hello, iCat');
			$('span.sub-title')//.css({'height':'500px', 'background':'#f90'})
				.click(function(){
					alert('Hello, iCat library...');
				});
		}
	});
	
	$(function(){
		var timeend = (new Date()).getTime();
		WI.fn();
		alert("执行时间是: " + (timeend - timebgein) + "毫秒"); //弹出总时间
	});
})(ICAT);