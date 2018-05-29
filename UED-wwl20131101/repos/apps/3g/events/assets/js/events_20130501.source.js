(function(iCat){
	//var Event = iCat.Event;

	iCat.app("GNEvents",function(){
		return {verstion:"1.0"};
	});
	iCat.mix(GNEvents,{
		init: function(){
			setTimeout(function(){ window.scrollTo(0, 1); }, 100);
			iCat.include(['lib/zepto/zepto.js'],function(){
				//Event.on(".J_award_btn,.J_again_btn",'singleTap:dg',function(){
				$(".wrapper").delegate(".J_award_btn,.J_again_btn",'click',function(){
					if($(".J_award_btn,.J_again_btn").hasClass('done')) return;
					$.ajax({
						//url: iCat.util.fullUrl('/reward/fate',true),
						url: "http://" + location.host + "/reward/fate",
						type: "GET",
						dataType: "json",
						timeout: 10000,
						success: function(res){
							if(res.success){
								$(".J_award_btn,.J_again_btn").addClass('done');
								switch(res.data.code){
									case 101: //用户未登录
										GNEvents.showTips(res.data.code,{url:res.data.data.redirect_url});
										break;
									case 102: //奖品已经全部发放完
									case 103: //当日奖品全部发放完
										GNEvents.showTips(res.data.code);
										break;
									case 104: //用户抽奖次数已用完
										GNEvents.showTips(res.data.code);
										break;
									case 105: //未中奖
										GNEvents.showTips(res.data.code);
									case 0: //中奖啦
										GNEvents.showTips(res.data.code,{url:res.data.data.detail_url,name:res.data.data.name});
										break;
									case 106: //抽奖失败
										GNEvents.showTips(res.data.code);
										break;
									default: //异常结果
										GNEvents.showTips(-2);
								};
							}
						},
						error: function(){
							//请求服务器错误
							GNEvents.showTips(105);
						}
					});
				});
			});
		},
		showTips: function(status,options){
			var shtml;
			switch(status){
				case 101: //用户未登录
					window.location.href = options.url; //跳转到登陆页面
					break;
				case 102: //奖品已经全部发放完
				case 103: //当日奖品全部发放完
					shtml = '<div class="timeout">\
							<p class="txt">今天的奖品已经派完，请明天再来吧！</p>\
						</div>';
					GNEvents.animation(shtml);
					break;

				case 104: //用户抽奖次数已用完
					shtml = '<div class="timeout">\
							<p class="txt">您今天的抽奖次数<br>已经用完<br>请明天再来吧！</p>\
						</div>';
					GNEvents.animation(shtml);
					break;

				case 105: //未中奖
					shtml = '<div class="unwinning">\
							<p class="txt">差一点就中奖了！</p>\
							<p><a class="btn J_again_btn">再来一次</a></p>\
						</div>';
					GNEvents.animation(shtml);
					break;

				case 0: //中奖
					shtml = '<div class="unwinning">\
							<p class="txt">恭喜您获得'+options.name+'</p>\
							<p><a href="'+options.url+'" class="btn">查看奖励</a></p>\
						</div>';
					GNEvents.animation(shtml);
					break;

				case 106: //抽奖失败
					shtml = '<div class="winning-error">哎呀，我们的程序出错了,请稍后再试!</div>';
					GNEvents.animation(shtml);
					break;

				case -2: //ajax请求成功，但返回的code错误
					shtml = '<div class="winning-error">哎呀，我们的程序出错了,请稍后再试!</div>';
					GNEvents.animation(shtml);
					break;

				default: //ajax请求异常结果
					shtml = '<div class="winning-error">服务器请求失败，请重试！</div>';
					GNEvents.animation(shtml);
			};
		},
		animation: function(str){
			var pointer = $('.lottery-pointer'), wrapBox = $('.winning-tips-wrap');

			pointer.css({"-webkit-transform-origin":"46px 57px","-webkit-transform": "rotateZ(0deg)"});
			pointer.animate({
				rotate: 360*3+Math.ceil(Math.random()*360)+"deg",

			},{
				duration: 3000,
				easing: 'ease-in-out',
				complete: function(){
					window.scrollTo(0,100);
					$(".J_award_btn,.J_again_btn").removeClass('done');
					$('.J_winning_tips').removeClass('ishide').addClass("show-winning-tips");
					wrapBox.html(str);
				}
			});
		}

	});

	GNEvents.init();

})(ICAT);