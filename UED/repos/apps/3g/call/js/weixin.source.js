(function(){
	var isRead = false;
	$(function() {
		$("#scratchpad").wScratchPad({
			width: 320,
			height: 80,
			//color: "#82b630",
			image2:"http://assets.3gtest.gionee.com/apps/3g/call/img/ggl-bg1.png",
			scratchMove: function() {
				$.post('/user/activity/scratching',function(data){
					if(isRead === false){
						isRead = true;
					}
				},'json');
			}
		});
	});
})();