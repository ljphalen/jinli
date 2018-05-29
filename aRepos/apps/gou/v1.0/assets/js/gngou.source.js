;(function(iCat, $){
	var lang = {
		//order
		order:{
			createdOrder:"订单已生成！",
			cneeInfoNull:"请完善收货人信息！",
			stopBuyTips:"您已经购买过了，此商品每人限购1个，再看看其他的吧。",
			cneeNameNull:"请填写收货人！",
			cneePhoneNull:"请填写联系方式！",
			cneeAddressNull:"请填写收货地址！",
			cneePostCodeNull:"请填写邮政编码！",
			cneeProvinceNull:"请选择省份！",
			cneeCityNull:"请选择城市！",
			accountUmidNull:"请输入帐号！",
			accountUnameNull:"请输入姓名！"
		},
		//order tips
		gng_tips1: "商品加入心愿清单了！",
		gng_tips2: "心愿清单中已有些商品。",
		gng_tips3: "此商品每人限购一件哦！",
		gng_tips4: "活动结束",
		gng_tips5: "您已领取过，下次再来！",
		gng_tips6: "请勿重复操作！",
		gng_tips7: "服务器繁忙,请稍后重试！",
		gng_tips8: "已到最后一页",
		gng_tips9: "请勿频繁操作！",
		gng_tips10: "反馈内容超过500字！",

		//ERROR TIPS
		gng_error1: "请填写正确的邮政编码！",
		gng_error2: "请填写正确的11位手机号！",
		//check login tips
		noLoginTip: "请登录!",
		feedbackNull: "内容不能为空！",
		resendInfo: "请勿重复提交！"
	};

	//瀑布流
	$.fn.publ = function(options){
		var opt = options || {},
			waterfall=$.waterfall={},
			$container=null;//容器
			waterfall.load_index=0; //加载次数

		var setting = {
			column_width:200,//列宽
			column_count: 0,
			column_className:'col',//列的类名
			column_space:10,//列间距
			cell_selector:'.cell',//要排列的砖块的选择器，限定在瀑布流的容器内
			img_selector:'img',//要加载的图片的选择器
			auto_imgHeight:true,//是否需要自动计算图片的高度
			fadein:true,//是否渐显载入
			fadein_speed:600,//渐显速率，单位毫秒
			insert_type:1, //砖块插入方式，1为插入最短那列，2为按序轮流插入
			getResource:function(index){ }  //获取动态资源函数,必须返回一个砖块元素集合,传入参数为加载的次数
		};

		setting = $.extend(setting,opt);

		$container=waterfall.$container=$(this);

		
		waterfall.$columns=creatColumn();

		render($(this).find(setting.cell_selector).remove(),false); //重排已存在元素时强制不渐显
		waterfall._scrollTimer2=null;
		$(window).bind('scroll',function(){
		 clearTimeout(waterfall._scrollTimer2);
		 waterfall._scrollTimer2=setTimeout(onScroll,300);
		});
		waterfall._scrollTimer3=null;
		$(window).bind('resize',function(){
		 clearTimeout(waterfall._scrollTimer3);
		 waterfall._scrollTimer3=setTimeout(onResize,300);
		});

		function creatColumn(){//创建列
		       waterfall.column_num=calculateColumns();//列数
		  //循环创建列
		  var html='';
		  for(var i=0;i<waterfall.column_num;i++){
		     html+='<div><ul class="mod-item-list '+setting.column_className+'"></ul></div>';
		  }
		  $container.prepend(html);//插入列
		  return $('.'+setting.column_className,$container);//列集合
		}

		function calculateColumns(){//计算需要的列数
		  if(setting.column_count > 0){
		  	return setting.column_count;
		  }

		  var num = Math.floor(($container.get(0).clientWidth)/(setting.column_width+setting.column_space));
		  if(num<1){ num=1; } //保证至少有一列
		  return num;
		}

		function render(elements,fadein){//渲染元素
		  if(!$(elements).length) return;//没有元素
		  var $columns = waterfall.$columns;
		  $(elements).each(function(i){                                     
		      if(!setting.auto_imgHeight||setting.insert_type == 2){//如果给出了图片高度，或者是按顺序插入，则不必等图片加载完就能计算列的高度了
		         if(setting.insert_type == 1){ 
		            insert($(elements).eq(i),setting.fadein&&fadein);//插入元素
		         }else if(setting.insert_type == 2){
		            insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素   
		         }
		         return true;//continue
		      }                     
		      if($(this)[0].nodeName.toLowerCase() == 'img'||$(this).find(setting.img_selector).length>0){//本身是图片或含有图片
		          var image=new Image;
		          var src=$(this)[0].nodeName.toLowerCase() == 'img'?$(this).attr('src'):$(this).find(setting.img_selector).attr('src');
		          image.onload=function(){//图片加载后才能自动计算出尺寸
		              image.onreadystatechange=null;
		              if(setting.insert_type==1){ 
		                 insert($(elements).eq(i),setting.fadein&&fadein);//插入元素
		              }else if(setting.insert_type==2){
		                 insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素  
		              }
		              image=null;
		          }
		          image.onreadystatechange=function(){//处理IE等浏览器的缓存问题：图片缓存后不会再触发onload事件
		              if(image.readyState == "complete"){
		                 image.onload = null;
		                 if(setting.insert_type == 1){ 
		                    insert($(elements).eq(i),setting.fadein&&fadein);//插入元素
		                 }else if(setting.insert_type == 2){
		                    insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素   
		                 }
		                 image=null;
		              }
		          }
		          image.src=src;
		      }else{//不用考虑图片加载
		          if(setting.insert_type==1){ 
		             insert($(elements).eq(i),setting.fadein&&fadein);//插入元素
		          }else if(setting.insert_type==2){
		             insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素  
		          }
		      }                     
		  });
		}

		function public_render(elem){//异步数据渲染接口函数    　　
		   render(elem,true);
		}

		function insert($element,fadein){//把元素插入最短列
		  if(fadein){//渐显
		     $element.css({"opacity":0}).appendTo(waterfall.$columns.eq(calculateLowest())).animate({opacity: 1},opt.fadein_speed, 'ease-in');;
		  }else{//不渐显
		     $element.appendTo(waterfall.$columns.eq(calculateLowest()));
		  }
		}

		function insert2($element,i,fadein){//按序轮流插入元素
		  if(fadein){//渐显
		  	console.log($element);
		     $element.css({"opacity":0}).appendTo(waterfall.$columns.eq(i%waterfall.column_num)).animate({opacity: 1},opt.fadein_speed, 'ease-in');
		  }else{//不渐显
		     $element.appendTo(waterfall.$columns.eq(i%waterfall.column_num));
		  }
		}

		function calculateLowest(){//计算最短的那列的索引
		  var min = waterfall.$columns.eq(0).height(),min_key=0;
		  waterfall.$columns.each(function(i){                         
		     if($(this).height() < min){
		        min = $(this).height();
		        min_key = i;
		     }                             
		  });
		  return min_key;
		}

		function getElements(){//获取资源
		  $.waterfall.load_index++;
		  return setting.getResource($.waterfall.load_index,public_render);
		}

		waterfall._scrollTimer = null;//延迟滚动加载计时器

		function onScroll(){//滚动加载
		  clearTimeout(waterfall._scrollTimer);
		  waterfall._scrollTimer = setTimeout(function(){

		      var $lowest_column = waterfall.$columns.eq(calculateLowest());//最短列
		      var bottom = $lowest_column.offset().top + $lowest_column.height();//最短列底部距离浏览器窗口顶部的距离
		      var scrollTop = document.documentElement.scrollTop || document.body.scrollTop || 0;//滚动条距离
		      var windowHeight = document.documentElement.clientHeight || document.body.clientHeight || 0;//窗口高度
		      if(scrollTop >= bottom-windowHeight){
		         render(getElements(),true);
		      }

		  },100);
		}

		function onResize(){//窗口缩放时重新排列
		  if(calculateColumns() == waterfall.column_num) return; //列数未改变，不需要重排
		  var $cells = waterfall.$container.find(setting.cell_selector);
		  waterfall.$columns.remove();
		  waterfall.$columns = creatColumn();
		  render($cells,false); //重排已有元素时强制不渐显
		}
	}

	//滚动加载插件
	$.fn.scrollLoad = function(options){
		var setting = {
			currPage: 0,
			ajaxUrl: '',
			token: token
		};
		var opt = $.extend(setting,options);
		var _this = $(this);
		var page = {};
			page.isGetData = false;
			page.curIndex = opt.currPage;

		$(window).bind('scroll',function(){
			if(isScrollBottom() && !page.isGetData){
				getAjaxData(opt.ajaxUrl);
			}
		});

		//获取ajax数据
		function getAjaxData(url){
			$.ajax({
				url: url,
				type: 'get',
				data: {token:opt.token,page:page.curIndex + 1},
				dataType: 'json',
				beforeSend: function(){
					
				},
				success: function(data){
					if(data.success){
						page.curIndex = data.data.curpage;
						if(data.data.hasnext == false){
							page.isGetData = true;
						}
						_this.append(template('J_itemView',data));
					}
				}
			});
		}
		//判断滚动条是否在底部
		function isScrollBottom(){
			var scrollTop = 0,
				scrollHeight = 0,
				clientHeight = 0;

			if(document.documentElement && document.documentElement.scrollTop){
				scrollTop = document.documentElement.scrollTop;
			} else if(document.body && document.body.scrollTop){
				scrollTop = document.body.scrollTop;
			}

			if (document.body.clientHeight && document.documentElement.clientHeight) {
				clientHeight = (document.body.clientHeight < document.documentElement.clientHeight) ? document.body.clientHeight: document.documentElement.clientHeight;
			} else {
				clientHeight = (document.body.clientHeight > document.documentElement.clientHeight) ? document.body.clientHeight: document.documentElement.clientHeight;
			}

			scrollHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);

			if(clientHeight + scrollTop == scrollHeight){
				return true;
			} else {
				return false;
			}
		}
	}

	iCat.app('GNG',function(){
		return {version:'1.0',isLogin:false,currTime:0}
	});

	iCat.mix(GNG,{
		pop : {
			prompt: function(msg,type){
				switch(type){
					case 0: window.prompt("['"+msg+"']","gn://['GNAccount','getAccountInfo','1','false']");
						break;
					case 1: window.prompt("['"+msg+"']","gn://['GNTips','GNTips','1','false']");
						break;
					case 2: window.prompt("['"+msg+"']", "gn://['GNPay','GNPay','1','false']");
						break;
				}
			}
		}
	});

	iCat.mix(GNG, {
		init: function(){
			//统计
			this._count();
			
			this.orderList();
			//订单处理
			this.morder();
			//广告滑动
			this.slidePic();
			//倒计时
			this.countdown();

			//滚动加载
			this.scrollLoad();

			//瀑布流
			this.publ();
		},

		_count: function(){
			var cr = $('body').attr('dt-cr');
			if(cr){
				$('a').click(function(evt){
					evt.preventDefault();
					//$.get(cr+'?url='+encodeURIComponent(this.href));
					var label = this.getAttribute('dt-labelCla')? '&tid='+this.getAttribute('dt-labelCla') : '';
					location.href = cr+'?url='+encodeURIComponent(this.href)+label;
				});
			}
		},

		//查询登陆状态
		checkLogin: function(){
			if(isLoginUrl == 'undefined'){
				isLoginUrl = null;
			}

			if(isLoginUrl){
				$.getJSON(isLoginUrl,function(data){
					if(!data.success){
						//调动客户端getAccountInfo
						GNG.isLogin = false;
					} else{
						GNG.isLogin = true;
					}
				});
			}
		},

		//验证表单
		checkForm: function(){
			var options = {
				realname: $('input[name=realname]'),
				mobile: $('input[name=mobile]'),
				province: $('select[name=province]'),
				city: $('select[name=city]'),
				detail_address: $('input[name=detail_address]'),
				postcode: $('input[name=postcode]'),
				username: $('input[name=username]'),
				sex: $('input[name=sex]'),
				year:$('input[name=year]'),
				month:$('input[name=month]'),
				day:$('input[name=day]'),
				feedback: $('textarea[name=react]'),
			};

			var rg = null;

			if(options.realname && options.realname.val() == ''){
				GNG.pop.prompt(lang.order.cneeNameNull,1);
				return;
			}

			if(options.username && options.username.val() == ''){
				GNG.pop.prompt(lang.order.accountUnameNull,1);
				return;
			}

			if(options.mobile && options.mobile.val() == ''){
				GNG.pop.prompt(lang.order.cneePhoneNull,1);
				return;
			} else {
				rg = /^1[3|5|8][0-9]\d{8}$/ig;
				if(options.mobile.length && rg.test(options.mobile.val()) == false){
					GNG.pop.prompt(lang.gng_error2,1);
					return;
				};
			}

			if(options.province && options.province.val() == '请选择'){
				GNG.pop.prompt(lang.order.cneeProvinceNull,1);
				return;
			}

			if(options.city && options.city.val() == '请选择'){
				GNG.pop.prompt(lang.order.cneeCityNull,1);
				return;
			}

			if(options.detail_address && options.detail_address.val() == ''){
				GNG.pop.prompt(lang.order.cneeAddressNull,1);
				return;
			}

			if(options.postcode && options.postcode.val() == ''){
				GNG.pop.prompt(lang.order.cneePostCodeNull,1);
				return;
			} else {
				rg = /^\d{6}$/g;
				if(options.postcode.length && rg.test(options.postcode.val()) == false){
					GNG.pop.prompt(lang.gng_error1,1);
					return;
				};
			}

			if(options.umid && options.umid.val() == ''){
				GNG.pop.prompt(lang.order.accountUmidNull,1);
				return;
			}


			if(options.feedback && options.feedback.val() == ''){
				GNG.pop.prompt(lang.feedbackNull,1);
				return;
			}

			if(options.feedback && options.feedback.length != 0 && options.feedback.val().length > 500){
				GNG.pop.prompt(lang.gng_tips10,1);
				return;
			}

			return true;

		},

		slidePic: function(){
			if(!$('.mindex, .cate-detail nav, .J_slidePic')[0]) return;
			iCat.incfile(['/zepto/touchSwipe.js','../slidePic.source.js'], function(){
				$('.mindex').slidePic({
					circle:true, auto:true, disableFirst:true, disableLast:true
				});

				$('.J_slidePic').slidePic({
					slideWrap: '.wrap',
					slidePanel: '.pic',
					slideItem: '.pic li'
				});

				$('.cate-detail nav .wrap').slidePic({
					slidePanel: '.top-tags',
					slideItem: '.top-tags li',
					prev: '.cate-detail nav .prevBtn',
					next: '.cate-detail nav .nextBtn',
					handleItem: '',
					disableFirst:true, disableLast:true,
					/*fixCurrent: function(){
						var slideItem = $('.cate-detail nav .top-tags li'), curIndex = 0;
						$.each(slideItem, function(i, v){
							var child = $(this).find('.actived');
							if(child.length) curIndex = i;
						});
						return curIndex;
					}*/
				});

			});
		},

		//滚动加载
		scrollLoad: function(){
			if(!$('.lazyload')[0]){
				return;
			}

			iCat.incfile('../tempcore.js',function(){
				var loadUrl = $('.lazyload').attr('data-ajaxUrl');
				
				$('.lazyload').scrollLoad({ajaxUrl: loadUrl,currPage:1});
			});
		},

		//瀑布流
		publ: function(){
			if(!$('#J_publWrap')[0]){return;}
			var loadUrl = $('#J_publWrap').attr('data-ajaxUrl');
			var isGetData = false;
			var opt={
				page: 1,
				getResource:function(index,render){//index为已加载次数,render为渲染接口函数,接受一个dom集合或jquery对象作为参数。通过ajax等异步方法得到的数据可以传入该接口进行渲染，如 render(elem)
				// if(index>=7) index=index%7+1;
				// var html='';
				// for(var i=20*(index-1);i<20*(index-1)+20;i++){
				//  var k='';
				//  for(var ii=0;ii<3-i.toString().length;ii++){
				//     k+='0';

				//  }
				//  k+=i;
				//  var src="http://cued.xunlei.com/demos/publ/img/P_"+k+".jpg";

				//  html+='<li><img src="'+src+'" /><p>'+k+'</p></li>';
				//  console.log(html);
				// }

				// return $(html);
				//console.log(index);

				if(!isGetData){
					$.ajax({
						url: loadUrl,
						type: 'get',
						dataType: 'json',
						data: {page:index+1},
						success: function(data){
					  	if(data.success){
					  		if(data.data.hasnext == false){
					  			isGetData = true;
					  		}
							opt.page = data.curpage;
							//console.log(opt.page);
							var list = data.data.list;
							//render(template('J_itemView',data));
							for(var i = 0, lens = list.length; i < lens; i++){
								render(template('J_itemView',{list:list[i]}));
								//render('<li><a href="'+list[i].click_url+'"><div class="pic"><img src="'+list[i].img+'" alt="" /></div><div class="txt">￥'+list[i].price+'</div></a></li>');
							}
						}
						}
					});
				} else {
					GNG.pop.prompt(lang.gng_tips8,1);
				}
			  },
			  auto_imgHeight:true,
			  insert_type:1,
			  cell_selector:'li',
			  column_count:3
			}

			iCat.incfile('../tempcore.js',function(){
				$('#J_publWrap').publ(opt);
			});
		},

		//倒计时
		countdown: function(){
			if($('.J_countDown')[0]){
				iCat.incfile(['../countDown.source.js'], function(){
					$('.J_countDown').countDown({
						startHandle: function(index){
							//活动开始时触发
							$('.J_countDown').eq(index).parent('li').hide();
						},
						endHandle: function(index){
							//活动结束时触发
							$('.J_countDown').eq(index).parent().find('.button .btn').remove();
							$('.J_countDown').eq(index).parent().find('.button').html('<span class="btn gray-arrow">'+lang.gng_tips4+'</span>');
							
						}
					});
				});
			}
		},

		orderList: function(){
			if(!$('#J_createOrder')[0]) return;

			var __quantity     = $('.quantity'), quantity,
				__subtotal     = $('.subtotal'), subtotal,
				__totalPrice   = $('.total-price'), totalPrice,
				__maxLimitNum  = $('.maxLimitNum'), maxLimitNum,
				__coinNum      = $('.coinNum'), coinNum,
				__subcointotal = $('.subcointotal'), subcointotal,
				__maxCoinLimit = $('.maxCoinLimit'), maxCoinLimit,
				__result       = [], __out = [], __unit = "元";

			//修改购买数量
			maxLimitNum = + __maxLimitNum.text();
			quantity = + __quantity.val();
			coinNum     = __coinNum.val();
			subcointotal = __subcointotal.html().match(/\d+.\d+/ig)[0];
			maxCoinLimit = __maxCoinLimit.html() == null ? 0.00 : + __maxCoinLimit.html().match(/\d+.\d+/ig)[0];
			subtotal = + __subtotal.html().match(/\d+.\d+/ig)[0];
			totalPrice = + __totalPrice.html().match(/\d+.\d+/ig)[0];

			__out.push(quantity); //[0] 默认数量 1
			__out.push(subtotal); //[1] 默认单价
			__out.push(coinNum);  //[2] 默认使用银币数量
			__out.push(subcointotal); //[3] 默认使用银币
			__out.push(totalPrice); //[4] 默认实际支付

			$('.quantity').bind('keypress',function(evt){
				//只能输入数字和小数点
				return evt.keyCode >= 48 && evt.keyCode <=57;
			});

			__quantity.bind('change',function(){
				if(__quantity.val() > maxLimitNum){
					__quantity.val(maxLimitNum);
				}

				if(__quantity.val() == '' || __quantity.val() < 1){
					__quantity.val(__out[0]);
					__coinNum.val(__out[2]);
				} else {

				}

				subtotal = parseInt(__quantity.val()) * __out[1];

				coinNum  = parseInt(__quantity.val()) * __out[2];

				__subtotal.html(subtotal.toFixed(2)+__unit);

				subcointotal = parseInt(__coinNum.val()) * parseInt(__quantity.val());

				

				if(subcointotal > maxCoinLimit){
					coinNum = maxCoinLimit;
					subcointotal = maxCoinLimit;
				}

				__coinNum.val(coinNum);

				__subcointotal.html("-"+subcointotal.toFixed(2)+__unit);

				__totalPrice.html( (subtotal - subcointotal).toFixed(2)+__unit);

			});

			__coinNum.bind('change',function(){
				if(__coinNum.val() > maxCoinLimit){
					__coinNum.val(maxCoinLimit);
				}

				if(__coinNum.val() == ''){
					__coinNum.val(0);
				}

				subcointotal = parseInt(__coinNum.val());
				var tmp = parseInt(__quantity.val()) * __out[2];

				if(subcointotal > tmp && tmp < maxCoinLimit){
					subcointotal = tmp;
					__coinNum.val(tmp);
				}

				__subcointotal.html("-"+subcointotal.toFixed(2)+__unit);

				__totalPrice.html( (subtotal - subcointotal).toFixed(2)+__unit);
			});
		},

		//订单处理
		morder: function(){
			var silver_coin = $('.coinNum');
			var goods_id = $('#goods_id');
			var address_id = $('#address_id');
			var number = $('.quantity');
			//check user login
			GNG.checkLogin();

			//确认订单
			$("#J_createOrder").bind('click',function(evt){
				if(GNG.isLogin == false){
					//GNG.pop.prompt(lang.noLoginTip,1);
					GNG.pop.prompt(location.href,0);
					return;
				}

				var _this = $(this), url = _this.attr('data-ajaxUrl'),
					params = {
						token: token,
						number: number.val(),
						silver_coin : silver_coin.val(),
						goods_id : goods_id.val(),
						address_id : address_id.val()
					};

				if(address_id.val() == ''){
					GNG.pop.prompt(lang.order.cneeInfoNull,1);
					return;
				}

				if(_this.hasClass('disable')){
					GNG.pop.prompt(lang.gng_tips9,1);
					return;
				}

				_this.addClass('disable').removeClass('orange');

				$.post(url,params,function(data){
					var odata = data;
					if(odata.success){
						//alert(odata.msg+"======Testing00...");
						GNG.pop.prompt(odata.msg,1);

						if (odata.data.iscash == 2) { //在线支付
							//console.log(odata.data.out_trade_no+"','"+odata.data.url);
							GNG.pop.prompt(odata.data.out_trade_no+"','"+odata.data.url,2);

						} else if(odata.data.iscash == 1) { //货到付款

							if(odata.data.url){
								window.location.href = odata.data.url;
							}

						} else {
							//非在线支付和货到付款
						}
					} else {
						//alert(odata.msg+"======Testing01...");
						_this.removeClass('disable').addClass('orange');
						GNG.pop.prompt(odata.msg,1);
					}
				},'json');

			});

			//收货人表单

			$('#J_checkForm').bind('submit',function(evt){
				evt.preventDefault();
			});

			$('#J_saveForm').bind('click',function(evt){
				evt.preventDefault();

				if(!GNG.checkForm()) return false;

				if($('#J_checkForm').attr('action')){
					var _this = $(this), elForm  = $('#J_checkForm'),url = elForm.attr('action');
					var param = elForm.serialize();

					if(_this.hasClass('requested')){
						GNG.pop.prompt(lang.gng_tips6,1);
						return;
					}

					_this.addClass('requested');

					$.post(url,param,function(data){
						var odata = data;

						if(odata.success){
							GNG.pop.prompt(odata.msg,1);

							if(odata.data.type == 'redirect' && odata.data.url){
   								window.location.href = odata.data.url;
							}

							if(odata.data.feedback == 1){
								$("textarea[name=react]").val('');
								_this.removeClass('requested');
							}

						} else {
							_this.removeClass('requested');
							GNG.pop.prompt(odata.msg,1);
						}

					},'json');
				}
				
			});

			//点击收货人信息时验证登陆
			$(".J_cneeLink,.btn-buy").bind('click',function(evt){
				
				if(GNG.isLogin == false){
					//GNG.pop.prompt(lang.noLoginTip,1);
					evt.preventDefault();
					GNG.pop.prompt(location.href,0);
					return;
				}

			});

			//点击立刻支付
			$('#J_goToPay').bind('click',function(evt){
				var _this = $(this);
				
				if(GNG.isLogin == false){
					evt.preventDefault();
					//GNG.pop.prompt(lang.noLoginTip,1);
					GNG.pop.prompt(location.href,0);
					return;
				} 

				if(_this.hasClass('required')){
					GNG.pop.prompt(lang.gng_tips6,1);
					return;
				}

				_this.addClass('required');

				var payUrl = $('.J_orderNo').attr('data-payUrl'),
					tradeUrl = $('.J_orderNo').attr('data-tradeUrl'),
					tradeNo = $('.J_orderNo').attr('data-tradeNo');
				var params = {'trade_no': tradeNo, 'token':token};
				$.post(payUrl, params ,function(data){
					if(data.success){
						GNG.pop.prompt(tradeNo+"','"+tradeUrl,2);
					} else {
						_this.removeClass('required');
						GNG.pop.prompt(data.msg,1);
					}
				}, 'json');
			});

			//点击马上领取
			$('#J_getCoinLink').bind('click',function(evt){
				evt.preventDefault();

				var _this = $(this), url = _this.attr('data-ajaxUrl');

				if(url && _this.hasClass('requested')){
					window.pop.prompt(lang.gng_tips5,1);
					return;
				}

				$.getJSON(url,function(data){
					if(data.success){
						_this.addClass('requested');
						if(data.data.type == 'redirect' && data.data.url){
							GNG.pop.prompt(data.msg+"','"+data.data.url,1);
						} else {
							GNG.pop.prompt(data.msg,1);
						}

					} else {
						GNG.pop.prompt(data.msg,1);
					}
				});
			});

			//个人中心登录检测
			if($('.account .pindex')[0]){
				$('.pindex a').each(function(i){
					if(i == 0 || i == 3 || i == 4){
						$('.pindex a').eq(i).bind('click',function(evt){
							if(GNG.isLogin == false){
								evt.preventDefault();
								GNG.pop.prompt(location.href,0);
								return;
							}
						});
					}
				});

			}

			//收货人两级联动
			if($('.J_selectProv')[0]){
				var initProvVal = [];
				if($('#J_initProvVal')[0]){
					initProvVal = $('#J_initProvVal').val().split('-');
				}
				
				//iCat.incfile(['../provincesdata.js'], function(){
					//iCat.incfile(['../provincesCity.source.js'], function(){
						$('.J_selectProv').provCity({levelNum:2, defValue:initProvVal});
					//});
				//});
			}

			//我想要
			$(".J_WantBtn").bind('click', function(evt){
				evt.preventDefault();

				if(GNG.isLogin == false){
					//GNG.pop.prompt(lang.noLoginTip,1);
					GNG.pop.prompt(location.href,0);
					return;
				}

				var curWantCount = $('.J_wantCount').html();

				if( $(this).attr('data-ajaxUrl') ){
					var url = $(this).attr('data-ajaxUrl');
					$.getJSON(url,function(data){
						if(data.success){
							$('.J_wantCount').html(+curWantCount+1);
							GNG.pop.prompt(data.msg,1);
						} else {
							GNG.pop.prompt(data.msg,1);
						}	
					});
				}
			
			});
		}
	});

	$(function(){
		GNG.init();
	});
})(ICAT, Zepto);