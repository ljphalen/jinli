(function() {
	var enumMsg = {
		expand_tips:'展开<i class="icon-arrow-down"></i>',
		show_tips:'收起<i class="icon-arrow-up"></i>',
		underPoints:'您的积分不足',
		underStock:'库存不足',
		phoneIllegal:'请输入合法的联系电话',
		numOverflow:'当前兑换数量不足',
		numError:'兑换数量必须为数值类型',
		exchangeSuccess:'兑换成功',
		otherError:'网络异常，请稍候重试',
		notLogin:'未登录，请先登录',
		nameError:'收货人不能为空',
		phoneError:'请输入合法的联系电话',
		addressError:'收货地址不能为空',
		exchangeFail:'兑换失败',
		unshelve:'商品已下架',
		postError:'请求异常，请刷新重试',
		timeOut:'请求超时'
	};
	var action={
		network:'gamehall.hasnetwork',
		alert:'gamehall.alert',
		logout:'gamehall.clearlogin',
		finish:'gamehall.finish',
		islogin:'gamehall.islogin',
		getPoint:'gamehall.daily.task',
		login:'gamehall.account',
		statis:'gamehall.sendstatis',
		welfare:'gamehall.welfare',//我的A券，更多奖励
		boradcast:'gamehall.money.change'
	};
	var activity = {
		locked:false,
		getUrlParam:function(name){
			 var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.search.substr(1).match(reg);  //匹配目标参数
            if (r != null) return unescape(r[2]); return null; //返回参数值
		},
		lazyLoad: function(pNode, t) {
			var obj={};
            var imgs = $(pNode).find('img[src$="blank.gif"],img[src$="blankAvator.gif"]'),
                _fn = function(o) {
                    var src = o.getAttribute('data-src');
                    obj.__IMAGE_CACHE = obj.__IMAGE_CACHE || {};
                    if (!src) return;

                    if (!obj.__IMAGE_CACHE[src]) {
                        var oImg = new Image();
                        oImg.src = src;
                        oImg.onload = function() {
                            o.src = src;
                            obj.__IMAGE_CACHE[src] = true;
                            $(o).attr('data-src', src);
                            oImg = null;
                        };
                    } else {
                        o.src = src;
                        $(o).attr('data-src', src);
                    }
                };

            imgs.each(function(i, v) {
                t ? setTimeout(function() {
                    _fn(v);
                }, i * t) : _fn(v);
            });
        },
		//通过viewType接口打开一个新的webview界面
		openPageByViewType:function(){
			$('body').delegate('[data-infpage]', 'click', function() {
				var infpage = $(this).attr("data-infpage").split(',');
	            if(!(window.gamehall||navigator.gamehall)){
	                location.href = infpage[1];
	                return;
	            }
				var type=$(this).attr('data-type'),
					viewType=$(this).attr("data-viewType"),
					source=$(this).attr("data-source")||'',
					url=infpage[1],
					title=infpage[0];
				var cfg={};
					cfg.args={ },cfg.type='list';
	            cfg.args.newArgs={
	                viewType:viewType,
	                param:{
	                    url:url,
	                    title:title,
	                    source:source
	                }
	            }
	            var sucCal=errCal = function() {},
	                gamehall=window.gamehall?window.gamehall:navigator.gamehall;
                gamehall.startlistactivity(sucCal,errCal,JSON.stringify(cfg.args));
			});	
		},
		//打开客户端页面
		onEventAction:function(action,data){
			if(window.gamehall){
				var ret=window.gamehall.onEvent(action,JSON.stringify(data));
			}else{
				if(action='gamehall.alert'){
					// alert(data.alertStr);
				}
			}
		},
		//从客户端取数据或状态
		getValueAction:function(action,data){
			if(window.gamehall){
				var ret=window.gamehall.getValue(action,JSON.stringify(data));
				ret=JSON.parse(ret);
				return ret;
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
					    	var ret=activity.getValueAction(action.network,{});
					    	if(ret==false){
					    		activity.onEventAction(action.alert,{alertStr:enumMsg.otherError});
					    		return;
					    	} 
					    	activity.getAjaxData();
					    }
					}
				})
			}
		},
		//我的A券更多奖励
		moreWelfare:function(){
			$("#more").click(function(){
				activity.onEventAction(action.welfare,{});
			});
		},
		//获取A券数据或兑换活动结束后的玩家列表、商品列表
		getAjaxData:function(){
			var moreBtn=$(".J_loadMore");
			moreBtn.removeClass('invisible');
			activity.locked=true;
			var ajaxUrl=moreBtn.attr("data-ajaxurl"),
				curPage=parseInt(moreBtn.attr("data-curpage"),10);
			var isAcoin=$("#coinList").length?true:false,data,container;
			var isPlayer=$("#playerList").length?true:false;
			var isGoods=$("#goodsList").length?true:false;
			data={
				page: curPage + 1,
                token: token,
			};
			if(isAcoin){
				data={
					page: curPage + 1,
                    token: token,
                    uuid:uuid
				};
			}
			if(isGoods){
				data={
					page:curPage+1,
					token:token,
					puuid:activity.getUrlParam('puuid'),
					sp:activity.getUrlParam('sp'),
					uname:activity.getUrlParam('uname')
				}
			}
			$.ajax({
				type:"POST",
				url:ajaxUrl,
				dataType: 'json',
				data:data,
				success:function(data){
					activity.locked=false;
					var alertStr;
					//非法请求
					if(!data||(typeof data=='string')||!data.success){
						moreBtn.addClass('invisible');
						var msg=enumMsg.postError;
						if(data&&data.msg){
							msg=data.msg;
						}
						activity.onEventAction(action.alert,{alertStr:msg});
						return;
					}
					var d=data.data;
						hasNext=d.hasNext,
						curPage=d.curPage,html='';
					
					if(isAcoin){//A券
						container=$("#coinList");
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
					}
					if(isPlayer){//玩家列表
						container=$("#playerList");
						for(var i=0,len=d.list.length;i<len;i++){
							html+="<li class='tline'>"+d.list[i]["no"]+'.'+d.list[i]["nickname"]+"</li>";
						}
					}
					if(isGoods){//商品列表
						container=$("#goodsList");
						for(var i=0,len=d.list.length;i<len;i++){
							var info=d.list[i];
							html += '<li ">\
										<a data-type="0" data-viewtype="WebView" data-source="prizeexchange" data-infpage="'+info["title"]+','+info["redirectLink"]+'">\
											<div class="goods-list border-1px">\
												<img src="http://assets.gionee.com/apps/game/apkv1/pic/blank.gif" alt="" data-src="'+info["img"]+'">\
												<span class="consume-points">消耗积分：<em>'+info["consumePoints"]+'</em></span>\
												<span class="exchange-num">剩余数量：<em>'+info["remaindNum"]+'</em></span>\
											</div>\
										</a>\
								</li>';
						}
					}
					
					setTimeout(function(){
						if(hasNext == 0 || hasNext == 'false'){
							moreBtn.html('<span class="bottom">到底了，去其它页面看看吧</span>');
						} else{
							moreBtn.addClass('invisible');
						}
						container.append(html);
						moreBtn.attr("data-hasnext",hasNext);
						moreBtn.attr("data-curpage",curPage);
						activity.lazyLoad(container[0],100);
					},100);
				},
				error:function(){
					activity.locked=false;
					moreBtn.addClass('invisible');
				}
			})
		},
		//判断登录状态
		checkLoginStatus:function(){
			if(typeof isLogin=='undefined') return;
			if(isLogin!=undefined&&isLogin=='false'){
				activity.onEventAction(action.finish,{});
				activity.onEventAction(action.logout,{});
				activity.onEventAction(action.login,{});
			}
		},


		dialogShow:function(dialog){
			$("body").bind('touchmove',function(e){e.preventDefault();},false);
            var scrollTop=(3-document.body.scrollTop)+'px';
            dialog.show().animate({
                display: 'block',
                bottom: scrollTop,
            }, 300, function() {
                $(".J_dialog").removeClass('invisible');
            });
		},
		dialogHide:function(dialog){
			$("body").unbind('touchmove');
            var height=dialog.height();
            dialog.animate({
                display: 'block',
                bottom: -height,
            }, 300, function() {
                $(this).hide();
                $(".J_dialog").addClass('invisible');
            });
		},

		//积分兑换A券
		pointExAcoin:function(){
			var btn=$("#exAcoinBtn");
			if(!btn[0]) return;
			var goodId=activity.getUrlParam('goodId'),
				puuid=activity.getUrlParam('puuid'),
				uname=activity.getUrlParam('uname'),
				sp=activity.getUrlParam('sp'),
				ajaxUrl=btn.attr('data-ajaxurl');
			btn.click(function(){
				var canExchangeNums=parseInt($("#canExchangeNums").html(),10),
					num=$("#num").val(),
					dialog=$(".J_loading");
				//输入非数值
				if(!$.isNumeric(num)){
					activity.onEventAction(action.alert,{alertStr:enumMsg.numError});
					return;
				}
				
				//提交数据显示模态框
				activity.dialogShow(dialog);
				activity.onEventAction(action.boradcast,{});//广播通知积分变更
				var data={
					action:'exchange',
					object:'goods'+goodId
				}
				activity.onEventAction(action.statis,data);//统计
				$.ajax({
					type:"POST",
					url:ajaxUrl,
					dataType: 'json',
					timeout:5000,
					data:{
						goodId: goodId,
	                    puuid:puuid,
	                    exchangeNums:num,
	                    uname:uname,
	                    token:token,
	                    sp:sp
					},
					success:function(data){
						var alertStr;
						//非法请求
						if(!data||(typeof data=='string')||!data.success){
							activity.dialogHide(dialog);
							var msg=enumMsg.postError;
							if(data&&data.msg){
								msg=data.msg;
							}
							activity.onEventAction(action.alert,{alertStr:msg});
							return;
						}
						
						var d=data.data;
						//未登录
						if(d.isLogin!=undefined&&d.isLogin==false){
							activity.onEventAction(action.finish,{});
							activity.onEventAction(action.logout,{});
							activity.onEventAction(action.login,{});
							return;
						}
						if(d.exchangeStatus){
							var status=d.exchangeStatus;
							switch(parseInt(status,10)){
								case 1:
									alertStr=enumMsg.exchangeSuccess;
									break;
								case 2:
									alertStr=enumMsg.underPoints;
									break;
								case 3:
									alertStr=enumMsg.exchangeFail;
									break;
								case 4:
									alertStr=enumMsg.underStock;
									break;
								case 5:
									alertStr=enumMsg.underStock;
									break;
								case 6:
									alertStr=enumMsg.unshelve;
									break;
							}
						}
						
						setTimeout(function(){
							activity.onEventAction(action.alert,{alertStr:alertStr});
							activity.dialogHide(dialog);
							setTimeout(function(){
								if(parseInt(status,10)==6){
									location.href=d.url;
								}else{
									location.reload();//页面刷新
								}
								
							},1500);
						},300)
					},
					error:function(xhr,textStatus){
						activity.dialogHide(dialog);
						var msg=enumMsg.otherError;
						if(textStatus=='timeout'){
							msg=enumMsg.timeOut;
						}
						activity.onEventAction(action.alert,{alertStr:msg});
						return;
					}
				})
			});
			
			//只允许输入数字
			$("#num").keypress(function(event) {  
			    var keyCode = event.which;  
			    if (keyCode == 46 || (keyCode >= 48 && keyCode <=57)){ 
			        return true;  
			    }else{  
			        return false;  
			    }}).focus(function() {  
			        this.style.imeMode='disabled';  
			});  
		},
		//积分兑换实物
		pointExEntity:function(){
			var btn=$("#exEntityBtn");
			if(!btn[0]) return;
			var goodId=activity.getUrlParam('goodId'),
				puuid=activity.getUrlParam('puuid'),
				uname=activity.getUrlParam('uname'),
				ajaxUrl=btn.attr('data-ajaxurl'),
				postUrl=$(this).attr('data-ajaxUrl'),
				sp=activity.getUrlParam('sp'),
				dialog=$(".J_loading");
			
			btn.click(function(){
				var name=$("#name").val().trim(),
				phone=$("#phone").val().trim(),
				address=$("#address").val().trim();
				
				//3 数据合法性判断
				if($.isEmptyObject(name)){//收货人不能为空
					activity.onEventAction('gamehall.alert',{alertStr:enumMsg.nameError});
					return;
				}
				var reg=/^1[34578]{1}[0-9]{9}$/;
				if(!reg.test(phone)){//电话号码必须合法
					activity.onEventAction('gamehall.alert',{alertStr:enumMsg.phoneError});
					return;
				}
				if($.isEmptyObject(address)){//收货地址不能为空
					activity.onEventAction('gamehall.alert',{alertStr:enumMsg.addressError});
					return;
				}
				
				activity.dialogShow(dialog);
				activity.onEventAction(action.boradcast,{});//积分A券变更通知
				var data={
					action:'exchange',
					object:'goods'+goodId
				}
				activity.onEventAction(action.statis,data);//积分兑换统计
				$.ajax({
					type:"POST",
					url:ajaxUrl,
					dataType: 'json',
					timeout:5000,
					data:{
						goodId: goodId,
	                    puuid:puuid,
	                    uname:uname,
	                    token:token,
	                    exchangeNums:1,
	                    receiverphone:phone,
	                    receiver:name,
	                    address:address,
	                    sp:sp
					},
					success:function(data){
						var alertStr;
						//非法请求
						if(!data||(typeof data=='string')||!data.success){
							activity.dialogHide(dialog);
							var msg=enumMsg.postError;
							if(data&&data.msg){
								msg=data.msg;
							}
							activity.onEventAction(action.alert,{alertStr:msg});
							return;
						}
						var d=data.data;
						//未登录
						if(d.isLogin!=undefined&&d.isLogin==false){
							activity.onEventAction(action.finish,{});
							activity.onEventAction(action.logout,{});
							activity.onEventAction(action.login,{});
							return;
						}
						
						if(d.exchangeStatus){
							var status=d.exchangeStatus;
							switch(parseInt(status,10)){
								case 1:
									alertStr=enumMsg.exchangeSuccess;
									break;
								case 2:
									alertStr=enumMsg.underPoints;
									break;
								case 3:
									alertStr=enumMsg.exchangeFail;
									break;
								case 4:
									alertStr=enumMsg.underStock;
									break;
								case 5:
									alertStr=enumMsg.underStock;
									break;
								case 6:
									alertStr=enumMsg.unshelve;
									break;
							}
						}


						setTimeout(function(){
							activity.onEventAction(action.alert,{alertStr:alertStr});
							activity.dialogHide(dialog);
							setTimeout(function(){
								if(parseInt(status,10)==6){
									location.href=d.url;
								}else{
									location.reload();//页面刷新
								}
							},1500);
						},300)
					},
					error:function(xhr,textStatus){
						activity.dialogHide(dialog);
						var msg=enumMsg.otherError;
						if(textStatus=='timeout'){
							msg=enumMsg.timeOut;
						}
						activity.onEventAction(action.alert,{alertStr:msg});
						return;
					}
				});
			});
		},
		//设置弹出层的高度及top位置
		setDialogHeight:function(){
			if($("#myAcoin").length>0) return;
			$(".J_dialog").height($(document).height());
			var elementArr=['.J_loading'];
			for(var i=0,len=elementArr.length;i<len;i++){
				var ele=$(elementArr[i]);
				ele.css('bottom',-ele.height());
				ele.removeClass('invisible').hide();
			}
		},
		init: function() {
			this.checkLoginStatus();
			this.pointExAcoin();
			this.pointExEntity();
			this.setDialogHeight();
			this.scroll();
			this.openPageByViewType();
			this.expand();
			this.moreWelfare();
			this.lazyLoad(document.body,100);
		}
	};
	$(function() {
		activity.init();
		FastClick.attach(document.body);
	})
})();
