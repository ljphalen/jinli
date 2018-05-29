(function() {
	var enumMsg = {
		expand_tips:'展开<i class="icon-arrow-down"></i>',
		show_tips:'收起<i class="icon-arrow-up"></i>'
	};
	var activity = {
		locked:false,
		//通过viewType接口打开客户端界面
		openPageByViewType:function(elem){
			var type=elem.attr('data-type'),
				viewType=elem.attr("data-viewType"),
				id=elem.attr('data-id'),
				gameId=elem.attr("data-gameId"),
				url=elem.attr("data-url"),
				title=elem.attr("data-title");
			var cfg={};
				cfg.args={ },cfg.type=type;
            cfg.args.newArgs={
                viewType:viewType,
                param:{
                    contentId:id,
                    gameId:gameId,
                    url:viewType=='WebView'?url:'',
                    title:viewType=='WebView'?title:'',
                    source:''
                }
            }
            var sucCal=errCal = function() {},
                gamehall=window.gamehall?window.gamehall:navigator.gamehall;
            switch(cfg.type){
                case 'list':
                    gamehall.startlistactivity(sucCal,errCal,JSON.stringify(cfg.args));
                    break;
                case 'detail':
                    gamehall.startdetailsactivity(sucCal,errCal,JSON.stringify(cfg.args));
                    break;
                case 'browser':
                    gamehall.startlocalbrowser(sucCal,errCal,JSON.stringify(cfg.args));
                    break;
            }
		},
		//打开客户端页面
		onEventAction:function(action,data){
			if(window.gamehall){
				var ret=window.gamehall.onEvent(action,JSON.stringify(data));
			} 
		},
		//从客户端取数据或状态
		getValueAction:function(action,data){
			if(window.gamehall){
				var ret=window.gamehall.getValue(action,JSON.stringify(data));
				ret=JSON.parse(ret);
				if(action=='gamehall.hasnetwork'){
					var str="";
					if(!ret){
						str='网络异常，请稍候重试';
						onEvent('gamehall.alert',{alertStr:str});
					}
					return ret;
				}
			} 
		},
		//收起或展开活动规则
		expand:function(){
			$(".J-expand").click(function(){
				var isExpand=$(this).html()==enumMsg.expand_tips?true:false;
				if(isExpand){
					$(this).html(enumMsg.show_tips);
					$(".J_content").addClass('h-auto');
				} else {
					$(this).html(enumMsg.expand_tips);
					$(".J_content").removeClass('h-auto');
				}
			})
		},
		scroll:function(){
			//活动详情页面滚动
			if($('.J_ac_anno').length) {
				$(document).on({
					scroll:function(){
						scrollHanlder();
					}
				})
				function scrollHanlder(){
					var contentHeight=$(".J_ac_anno").height(),
						scrollTop=window.scrollY;
					if(scrollTop>contentHeight){
						$(".J_ac_anno").addClass("ac-fixed");
					} else{
						$(".J_ac_anno").removeClass("ac-fixed");
					}
				}
			}
			//我的A券滑动加载分页数据
			if($(".J_loadMore").length){
				var winH=$(window).height();
				$(document).on({
					scroll:function(){
						var hasnext=$(".J_loadMore").attr("data-hasnext");
						if(hasnext=='false'||hasnext==0) return;
						if(activity.locked) return;
					 	 var boxHeight = document.body.clientHeight,
	                        visibleHeight = document.documentElement.clientHeight,
	                        boxScrollTop = document.body.scrollTop;
					    if(Math.abs(boxHeight - visibleHeight) <boxScrollTop){
					    	//判断网络状态
					    	var ret=activity.getValueAction('gamehall.hasnetwork',{});
					    	if(ret==false) return;
					    	activity.getACoinData();
					    }
					}
				})
			}
		},
		//我的A券更多奖励
		moreWelfare:function(){
			$("#more").click(function(){
				if($(this).attr("data-viewType")){
					activity.openPageByViewType($(this));
				}
				if($(this).attr("data-action")){
					var action=$(this).attr("data-action");
					var	data=$(this).attr("data-data")?elem.attr("data-data"):{};
					activity.onEventAction(action,data);
				}
			});
		},
		getACoinData:function(){
			var moreBtn=$(".J_loadMore");
			moreBtn.removeClass('invisible');
			activity.locked=true;
			var ajaxUrl=moreBtn.attr("data-ajaxurl"),
				curPage=parseInt(moreBtn.attr("data-curpage"),10);
			$.ajax({
				type:"POST",
				url:ajaxUrl,
				dataType: 'json',
				data:{
					page: curPage + 1,
                    token: token,
                    uuid:uuid
				},
				success:function(data){
					activity.locked=false;
					var d=data.data;
						hasNext=d.hasNext,
						curPage=d.curPage,html='';
					for(var i=0,len=d.list.length;i<len;i++){
						var info=d.list[i],
							className='',
							status='';
						if(info['status']=='no'){
							status="即将生效";
						}
						if(info['status']=='available'){
							if(info['leftMount']==0){
								status="已使用";
								className="disable";
							}else {
								status="可用"+info["leftMount"];
							}
						} 
						if(info['status']=='outdate'){
							status="已过期";
							className="disable";
						}
						html += '<li class="'+className+'">\
								<div class="toLeft">\
									<span class="mount">'+info["chargeMount"]+'</span>\
									<span class="status">'+status+'</span>\
								</div>\
								<div class="toRright">\
									<span class="origion">'+info["origin"]+'</span>\
									<span class="riqi">'+info["startDate"]+'至'+info["endDate"]+'</span>\
								</div>\
							</li>';
					}
					setTimeout(function(){
						if(hasNext == 0 || hasNext == 'false'){
							moreBtn.html('<span class="bottom">到底了，去其它页面看看吧</span>');
						} else{
							moreBtn.addClass('invisible');
						}
						$("#coinList").append(html);
						moreBtn.attr("data-hasnext",hasNext);
						moreBtn.attr("data-curpage",curPage);
					},100);
				},
				error:function(){
					activity.locked=false;
					moreBtn.addClass('invisible');
				}
			})
		},
		checkLoginStatus:function(){
			if(typeof isLogin=='undefined') return;
			if(isLogin!=undefined&&isLogin=='false'){
				activity.onEventAction('gamehall.finish',{});
				activity.onEventAction('gamehall.clearlogin',{});
				activity.onEventAction('gamehall.account',{});
			}
		},
		init: function() {
			this.checkLoginStatus();
			this.scroll();
			this.expand();
			this.moreWelfare();
		}
	};
	$(function() {
		activity.init(); 
	})
})();
