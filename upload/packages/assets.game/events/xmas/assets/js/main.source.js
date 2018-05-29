(function() {
	var enumMsg = {
		expand_tips:'点击查看活动规则',
		show_tips:'点击收起活动规则'
	};
	var activity = {
		ajaxUrl:$(".J_data").attr('data-ajaxurl'),
		//收起或展开活动规则
		expand:function(){
			$("#J-expand").click(function(){
				var isExpand=$(this).html()==enumMsg.expand_tips?true:false;
				if(isExpand){
					$(this).html(enumMsg.show_tips);
					$(".J_content").show();
				} else {
					$(this).html(enumMsg.expand_tips);
					$(".J_content").hide();
				}
			})
		},
		getRebateData:function(){
			$.ajax({
				url:this.ajaxUrl,
				type:'POST',
				dataType:'json',
				data:{
					token:token
				},
				success:function(d){
					var html='';
					if(d.length<=0) return;
					for (var i =0,len=d.length;i<len;i++){
						html+='<li><span class="phone">'+d[i]['uname']+'</span>\
							<span class="number">'+d[i]['denomination']+'</span>\
							<span class="time">'+d[i]['update_time']+'</span></li>';
					}
					$(".J_data").html(html);
				},
				error:function(){

				}
			})
		},
		init: function() {
			var t=5*60*1000;
			this.expand();
			setInterval(function(){
				activity.getRebateData()
			},t);
		}
	};
	$(function() {
		activity.init(); 
	})
})();

