;(function($) {
	//举报
	$('.buy_ed').live('click', function(){
		if(!$.meguo.dialog.islogin()) return !1;
		var znid = $(this).attr('znid');

		$.getJSON(FTXIAER.root+'/?m=ajax&a=report&znid='+znid, function(result){
			if(result.status == 1){
				$.dialog({id:'report', title:'举报中心', content:result.data, padding:'', fixed:true, lock:true});
				$.meguo.item.report($('#J_report'));
			}else{
				$.meguo.tip({content:result.msg, icon:'error'});
			}
		});
	});
})(jQuery);