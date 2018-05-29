/*
 * Applaction: Gou
 * Author: valleykid
 * Date: 2013-11-21 09:09:25.
 */

(function(iCat){
	var elBody = $('*[data-pagerole="body"]')[0] || document.body,
		bodyId = elBody.id;
	
	switch(bodyId){
		case 'weather':
			iCat.include('./single/waterfall', function(){
				var wf_page = 0, wf = $('#waterfall'),
					ajaxUrl = wf.attr('data-ajaxUrl');
				wf.waterfall({
					colWidth: '7.5rem',
					marginLeft: 6,
					marginTop: 6,
					isAnimation: false,
					ajaxFunc: function(success, error){
						$.ajax({
							type: 'GET',
							url: ajaxUrl,
							cache: false,
							data: {'page': ++wf_page},
							timeout: 60000,
							success: success,
							error: error
						});
					},
					createHtml: function(data){
						return '<div class="wf_item_inner">' +
								  '<a href="'+ data.link +'" class="thumb">' +
									'<img class="thumb_img"  src="'+ data.img +'" />' +
								  '</a>' +
								  '<div class="price"><span>￥'+data.price+'</span></div>'
							  '</div>';
					}
				});
			});
			break;

		case 'lottery':
			iCat.include('./single/jquery.rotate', function(){
				var body = $(elBody),
					board = $('#J_lotteryBoard'), 
					btn = $('#J_lotteryBtn'),
					box = $('.J_dialogBox'), 
					mask = $('.J_fullMask'),
					pointer = $('.abled'),
					url = pointer.attr('data-ajaxurl'),
					cate_id = 1,
					// 特殊指定的奖品id，用于控制代金卷时的页面显示
					special_award_id = 7,
					version,
					isNewVersion = false,
					keyword = location.host.split('.')[0]; // ios apk channel ...

				if (keyword == 'apk') {
					// 判断新旧版本，小于2.4.6的版本提示用户升级
					if (window.share && window.share.getVersionName && (version = window.share.getVersionName())) {
						var arr = version.split('.');
						arr[0]*1<2 ? isNewVersion = false : 
							arr[1]*1<4 ? isNewVersion = false : 
								arr[2]*1>=6 ? isNewVersion = true : false;
					}
				} else if (keyword == 'ios' ){
					if ($('#lottery').attr('data-version') == 'true') {
						isNewVersion = true;
					}
				}

				mask.height($(document).height());

				document.body.addEventListener('touchstart', function(e) {
					var $target = $(e.target);

					// 点击抽奖
					if ($target.hasClass('abled')) {
						var count = $target.attr('data-count') * 1;
						var score = $target.attr('data-score') * 1;
						if (count < 3) {
							runLottery();
						} else {
							if (isNewVersion) { // 新版本
								if (count < 5) {
									if (score >= 50) {
										box.html(_simpleDialog({
											tip: '本次抽奖需要消耗50分.',
											confirm: true
										})).show();
									} else {
										box.html(_simpleDialog({
											tip: '本次抽奖需要消耗50分<br>您的分数已不足50，无法参加抽奖！',
											txt: '您可以做任务获取分数！'
										})).show();
									}
								} else {
									box.html(_simpleDialog({
										tip: '您今天的抽奖机会已全部用完<br>请明天再来试试吧！'
									})).show();
								}
							} else { // 旧版本
								box.html(_simpleDialog({
									tip: '您今天的抽奖机会已全部用完!<br>升级新版本可以获得额外的抽奖机会，<br>更有手机S5.1，话费大奖等你拿！'
								})).show();
							}
						}
						return false;
					}
					// 关闭窗口
					if ($target.hasClass('J_close')) {
						box.hide();
						mask.hide();
						return false;
					}
					// 确定扣去积分
					if ($target.hasClass('J_fixed')) {
						box.hide();
						runLottery();
						return false;
					}

					// 提交表单信息
					if ($target.hasClass('submit')) {

						var $this = $target,
							argus = {
								imei: $this.attr('data-imei'),
								mobile: $('#mobile').val(),
								nickname: $('#nickname').val(),
								token: token,
								code: $this.attr('data-code')
							};
						if(!/^1[34578][0-9]{9}$/.test(argus.mobile)){
							_tip(argus.mobile? '手机号码错误，请重新填写' : '手机号码不能为空');
							return;
						}
						if (argus.nickname.length <= 0) {
							_tip('昵称不能为空');
							return;
						}
						$this.removeClass('submit');
						$.ajax({
							type: 'POST',
							url: ICAT.DebugMode ? '/gou/new/api/lottery/winnerinfo.php' :'/api/lottery/winnerinfo',
							data: argus,
							success: function(result){
								if (!$.isPlainObject(result)) result = JSON.parse(result);
								if(result.success){
									box.html(_createHtml({
										code: $this.attr('data-code'),
										text: '恭喜您兑奖信息提交成功',
										awardId: $this.attr('data-awardId')
									}, true));
								} else {
									$this.addClass('submit');
									_tip(result.msg);
								}
							},
							error: function(){ $this.addClass('submit'); }
						});
						return false;
					}
					
				}, false)

				function runLottery() {
					$.ajax({
						type: 'GET',
						url: url,
						cache: false,
						timeout: 5000,
						success: function(result){
							if (!$.isPlainObject(result)) result = JSON.parse(result);
							if (result.success) {
								pointer.attr('data-count', result.data.count*1 + 1);
								pointer.attr('data-score', result.data.total_score);
								// 前三次免费
								var index = new Number(result.data.sort) - 7,
									gngle = 30 + (index*60);

								rotateFunc({
									code: result.data.code,
									text: result.data.award_name,
									imei: result.data.imei,
									sort: result.data.sort,
									mobile: result.data.mobile,
									nickname: result.data.nickname
								}, gngle);

							} else {
								_tip(result.msg);
							}
						},
						error: timeOut
					})
				}
				
				function timeOut(){
					btn.addClass('abled');
					_tip('网络超时，请重试');
				}

				function rotateFunc(data, angle){
					if(angle===undefined){
						btn.addClass('abled');
						mask.show();
						data.text = '亲，您已经抽中了' + data.text + '，请及时填写兑奖信息！';
						box.html(_createHtml(data)).show();
					} else {
						board.stopRotate();
						board.rotate({
							angle:0,
							duration: 3000,
							animateTo: angle+1440,
							callback:function(){
								btn.addClass('abled');
								mask.show();
								if (data.sort == 1 || data.sort == 4) {
									var txt = '感谢您的参与，祝您下次中奖！<br>购物大厅祝你新年快乐';
									box.html(_simpleDialog({
										tip: txt
									})).show();
								} else {
									data.text = '亲，您已经抽中了'+ data.text + '，请及时完善个人信息以便兑奖！';
									box.html(_createHtml(data)).show();
								}

							}
						});
					}
				}

				function _simpleDialog(args) {
					var txt = [
						'<div class="txt">' + args.txt + '</div>'
					].join('');

					var confirm = [
						'<ul class="confirm-bottom">',
							'<li class="J_close">取消</li>',
							'<li class="line J_fixed">确定</li>',
						'</ul>'
					].join('');

					var bottomBtn = [
						'<div class="bottom">',
							'<span class="btn J_close">确定</span>',
						'</div>'
					].join('')

					return [
						'<div class="main">',
							'<div class="tip">',
								args.tip,
								(args.txt ? txt : ''),
							'</div>',
							args.confirm ? confirm : bottomBtn,
						'</div>'
					].join('');
				}

				function _createHtml(data, gohome){

					var inputWrap = [
						'<div class="wrap">',
							'<ul>',
								'<li><input type="text" id="nickname"',
								data.nickname?(' value="' + data.nickname + '"'): '',
								' placeholder="昵称"></li>',
								'<li><input type="text" id="mobile" ',
								data.mobile?('value="' + data.mobile + '"'):'',
								' placeholder="手机"></li>',
							'</ul>',
							'<p>',
								'请确保输入正确的手机号，并保持手机畅通！如断网，请截图或记录好中奖编号联系购物大厅-小惠',
							'</p>',
						'</div>'
					].join('');

					return ['<div class="top">',
								(data.code? '<h2>中奖编号：'+data.code+'</h2>' : ''),
								//'<i class="close">×</i>' +
							'</div>',
							'<div class="main'+(gohome? ' no-form' : '')+'">',
								'<div class="tip">'+data.text,
								'</div>',
								gohome ? '':inputWrap ,
							'</div>',
							'<div class="bottom">',
								(gohome? '<span class="btn J_close">确定</span>' : '<span class="btn submit" data-awardId="'+data.awardId+'" data-code="'+data.code+'" data-imei="'+data.imei+'">提交</span>'),
							'</div>'].join('');
				}

				function _tip(msg){
					var tip = body.find('.J_msgTip');
					if(tip[0]){
						//mask.show();
						tip.html(msg);
						tip.parent().show();
					} else {
						body.append('<div class="msg-tip"><span class="J_msgTip">'+msg+'</span></div>');
						tip = body.find('.J_msgTip');
					}

					setTimeout(function(){
						//mask.hide();
						tip.parent().hide();//fixed bug: 行内元素居然不起作用。。。
					}, 3000);
				}
			});

			// iCat.include('./single/jquery.marquee', function(){
			// 	$('#J_scrollText').marquee({speed: 50});
			// });
			break;

		case 'alipay':
			$('.J_gotop').click(function(evt){
				$('body, html').animate({scrollTop:0}, 500);
			});
			break;
		case 'subject_goods':
			iCat.include('./single/waterfall', function(){
				var wf_page = 0, wf = $('#waterfall'),
					ajaxUrl = wf.attr('data-ajaxUrl');
				wf.waterfall({
					colWidth: '7.5rem',
					marginLeft: 6,
					marginTop: 6,
					isAnimation: false,
					ajaxFunc: function(success, error){
						$.ajax({
							type: 'GET',
							url: ajaxUrl,
							cache: false,
							data: {'page': ++wf_page},
							timeout: 60000,
							success: success,
							error: error
						});
					},
					createHtml: function(data){
						return '<div class="wf_item_inner wf_item_goods">' +
								  '<a href="'+ data.href +'" class="thumb">' +
									'<img class="thumb_img"  src="'+ data.img +'" />' +
								  '</a>' +
								  '<div class="txt"><a href="' + data.href + '">'+data.title+'</a></div>'
							  '</div>';
					}
				});
			});
			break;

		case 'recoment':
			// amigo天气-精品推荐页面
			var recomentWrap = $('#recoment');
			if (recomentWrap.length) {
				var input = recomentWrap.find('input').first();
				input.focus(function(){
					this.nextElementSibling.className = 'active';
				}).blur(function(){
					this.nextElementSibling.className = '';
				})
				recomentWrap.on('touchstart touchmove mousedown mousemove', 'button', function(){
					var that = $(this);
					that.addClass('active');
					setTimeout(function(){ that.removeClass('active'); }, 1000);
				}).on('touchend mouseup', function(){
					$(this).removeClass('active');
				});
			} else {
				recomentWrap = null;
			}
			break;
		// 推荐有奖
		case 'recommend':
			var wrap = $('#J_wrapTop');
			// var shareToWeixin = $('#J_shareToWeixin');
			var submitBtn = $('#J_submit');
			var ajaxUrl = submitBtn.attr('data-ajaxurl');
			var tmpl = function(val) {
				var arr = [
					'<div class="remind-wrap">',
						'<span>您填写的推荐人手机号</span>',
						'<div class="tel"><i></i>' + val + '</div>',
					'</div>',
					'<p>请关注我们微信号公众号@购物大厅关注抽奖结果</p>'
				];
				return arr.join('');
			}
			// 调用客户端接口分享到微信
			// shareToWeixin.click(function() {
			// 	window.location.href = "objc://shareToWechat";
			// })
			submitBtn.click(function() {
				var val = $('#J_tel').val();
				if (val == '') {
					GOU.showtip('手机号码不能为空');
					return;
				}
				if (!/^\d{11}$/.test(val)) {
					GOU.showtip('手机号码格式错误');
					return;
				}
				$.ajax({
					url: ajaxUrl,
					type: 'POST',
					data: {
						mobile: val
					},
					success: function(result) {
						if (!$.isPlainObject(result)) result = JSON.parse(result);
						if (result.success) {
							wrap.html(tmpl(val));
						} else {
							GOU.showtip(result.msg);
						}
					},
					error: function(result) {
						GOU.showtip("提交失败，请重试！");
					}
				})
			})
			break;

		case 'mjb':
			$('#J_howCollectEl').click(function() {
				$('#J_howCollect').toggle('slow');
			});
			$('#J_whatMjbEl').click(function() {
				$('#J_whatMjb').toggle('slow');
			})
			break;
	}

	GOU.fitScreen()
	
})(ICAT);