(function() {
	var enumMsg = {
		loginFirst:'请先登录',
		answeringFinish:'今天题目已答完',
		hasAnswering:'亲，这道题已经答过了哦',
		networkError:'网络异常，请稍候重试'
	};
	var activity = {
		ajaxUrl:$("body").attr('data-ajaxurl'),
		loginUrl:$("body").attr("data-href"),
		uid:$(".nickname").attr('data-uid')?$(".nickname").attr('data-uid'):'',
		//开始答题
		startAnswering:function(){

			/*var startBtn=$(".J_begin .btn-orange");
			if(!startBtn[0]) return;*/
			var nextBtn=$(".J_next"),
				subject=$(".J_subject"),
				answer='';//答案

			/*if(activity.uid!=''){
				activity.getData(answer);
			} else{
				$(".J_begin").removeClass('hidden');
				//开始答题
				startBtn.click(function(){
					activity.getData(answer);
				});
			}*/

			//答题，判错，并显示下一题
			subject.find('span[data-answer]').click(function(){
				answer=$(this).attr('data-answer');
				$(".J_subject").find('span[data-answer]').unbind('click');
				activity.getData(answer);
			})

			//答错，点击进入下一题
			nextBtn.click(function(){
				answer='';
				activity.getData(answer,-1,'');
			});

			//求助
			$("#J_help").click(function(){
				$(".J_questionBank").addClass('hidden');
				$(".J_help").removeClass('hidden');
				//关闭求助
				$(".J_close").click(function(){
					$(".J_questionBank").removeClass('hidden');
					$(".J_help").addClass('hidden');
				})
			})
		},

		getData:function(answer){
			var subjectID=$(".J_subject").attr('data-id')?$(".J_subject").attr('data-id'):-1,
				subjectNO=$(".J_subject").attr('data-number')?$(".J_subject").attr('data-number'):'',
				sign=$("body").attr('data-sign')?$("body").attr('data-sign'):'';
			subjectID=arguments[1]!=undefined?arguments[1]:subjectID;
			subjectNO=arguments[2]!=undefined?arguments[2]:subjectNO;
			$.ajax({
					url:activity.ajaxUrl,
					timeout:8000,
					dataType:'json',
					type:'POST',
					data:{
						id:subjectID,
						number:subjectNO,
						uname:activity.uid,
						daan:answer,
						sign:sign,
						token:token
					},
					success:function(d){
						//切记，以后的ajax需要加锁机制，防疯狂发请求
						var success=d.success;
						var d=d.data;
						if(d.score!=undefined){//更新积分
							$("#points").html(d.score);
						}
						if(d.sign!=undefined){
							$("body").attr('data-sign',d.sign);
						}
						if((success!=undefined&&success==false)||(d.isLogin&&d.isLogin==-1)){//未登录
							activity.showTip(enumMsg.loginFirst);
							location.href=activity.loginUrl;
							return false;
						} 
						if(d.end!=undefined&&d.end==-1){//活动结束
							$(".quizContainer").empty().html('<div class="acFinish "><h1>活动已结束</h1>本次活动已结束，谢谢您的参与。</div>');
						}
						if(d.hasChance!=undefined&&d.hasChance=='false'){//今日答题机会已用完
							activity.showTip(enumMsg.answeringFinish);
							$(".J_begin").addClass('hidden');
							$(".J_questionBank").removeClass('hidden');
							$(".bankArea").addClass('hidden');
							$(".J_finishContainter").removeClass('hidden');
							if(subjectID!=-1){
								activity.getRankData();//更新积分排行榜数据
							}
							return false;
						}
						if(d.status!=undefined&&d.status==2){//此题已答过
							activity.showTip(enumMsg.hasAnswering);
							activity.getData('',-1,'');//重新请求最新数据
							return false;
						}
						else{ //取题显示在页面上
							
							if(d['daan']){//题目错误
								$('span[data-answer='+d['daan']+']').addClass('right');
								$('span[data-answer='+answer+']').addClass('wrong');
								$(".J_subject").find('span[data-answer]').unbind('click');
								$(".J_next").removeClass('hidden');
							} else{ //获取下一题
								$(".J_subject").attr('data-id',d.id).attr('data-number',d.number);
								$("span[data-answer]").removeClass('right').removeClass('wrong');
								$(".J_next").addClass('hidden');
								var title="问题"+d.number+":"+d.title;
								$(".J_title").html(title);
								$(".J_answerA").html("A:"+d.wenti['A']);
								$(".J_answerB").html("B:"+d.wenti['B']);
								$(".J_answerC").html("C:"+d.wenti['C']);
								$(".J_answerD").html("D:"+d.wenti['D']);
								$(".J_begin").addClass('hidden');
								$(".J_questionBank").removeClass('hidden');
								$("span[data-answer]").unbind('click');
								$("span[data-answer]").bind('click',function(){
									answer=$(this).attr('data-answer');
									activity.getData(answer);
								})
							}
						}
					},
					error:function(){
						activity.showTip(enumMsg.networkError);
						return false;
					}
			})
		},

		//显示弱提示
		showTip :function(msg){
			var tip = $('.J_tipBox'), dw = $(document).width(), bw = tip.width();
			tip.find('p').html(msg);
			var len=msg.length*6;
			tip.css('left', ((dw-bw)/2-len)+'px').removeClass('hidden');
			
			setTimeout(function(){
				tip.addClass('hidden');
				tip.find('p').html('');
			},1000);
		},
		getRankData:function(){
			var ajaxUrl=$(".J_rank").attr('data-ajaxurl');
			$.ajax({
				url:ajaxUrl,
				dataType:'json',
				type:'POST',
				timeout:8000,
				data:{
					token:token
				},
				success:function(d){
					var d=d.data;
					var html=$("ul.list").find('.first')[0].outerHTML;
					for(var i=0,len=d.length;i<len;i++){
						var top=i+1;
						html+="<li><span class='rank'>TOP"+top+"</span><span class='telephone'>"+d[i].uname+
								"</span><span class='points'>"+d[i].score+"</span></li>";
						$("ul.list").html(html);
					}
				},
				error:function(){
					activity.showTip(enumMsg.networkError);
					return false;
				}
			})
		},
		init: function() {
			this.startAnswering();
		}
	};
	$(function() {
		activity.init(); 
	})
})();