;(function() {
	function supportsCanvas() {
		return !!document.createElement('canvas').getContext;
	};

	//获取图片文件夹路径
	function getPicPath(){
		var mainJs = $('script[data-main]')[0],
			src = mainJs.hasAttribute ? mainJs.src : mainJs.getAttribute('src', 4);
		var picPath=src.replace(/assets\/js\/.*/g,'pic');
		return picPath;
	}

	//中奖状态显隐
	//中奖 领取奖励
	if(hasChance&&parseInt(WinCheck,10)>0){
		$("#getPrize").click(function(){
			$(".J_mask").removeClass('hidden');

			//确定按钮事件绑定
			//step1:验证数据合法性
			//step2: 提交数据
			//step3: 调起微博分享
			$("#submit").click(function(){
				var phoneNumber=$(".telephone").val().trim();
				var reg=/^1[34578]{1}[0-9]{9}$/;
				if(!reg.test(phoneNumber)){
					$(".mask-content").addClass('error');
					return false;
				} else{
					$(".mask-content").removeClass('error');
					$(".telephone").focus();
					var url=$(this).attr('data-ajaxUrl');
					// alert(phoneNumber);
					$.ajax({
						type:'POST',
						url: url,
						data:{
							mobile:phoneNumber,
							name:$(".username").val().trim(),
							prize:WinCheck,
							token:token,
							sign:sign,
							ret_id:ret_id
						},
						dataType:'json',
						complete:function(data){
							share();
							//关闭弹窗框
							$(".J_mask").addClass('hidden');
							$("#getPrize").unbind('click');
						}
					});
				}

			})
		});
	}
	function share(){
		var shareHref=$(".share").attr('href');
		// window.open(shareHref);
		window.location.href=shareHref;
		// alert(2);
	}

	$("#shareToHer").click(function(){
		share();
	})
	

	/**
	 * 刮奖 逻辑
	 */
	showAlert=true;
	perctenge=50;

	showLog=true;
	logPerct=30;
	 
	function scratcherChanged(ev) {
		var pct = (this.fullAmount(32) * 100)|0;

		//上报日志
		if(pct>=logPerct){
			if(showLog==true){
				var href=$("#Scratch_Holder").attr('data-href');
				$.post(href,{
					prize:WinCheck,
					token:token,
					sign:sign,
					ret_id:ret_id
				},function(data){
					ret_id=data.data['ret_id'];
				}
				);
				showLog=false;
			}
		}

		// 提示用户是否有中奖
		if(pct >= perctenge) { 
			if (showAlert==true)
			{
				//有中奖机会
				if(hasChance){
					if( parseInt(WinCheck,10)>0 ){
						$("#shareToHer").addClass('hidden');
						$("#getPrize").removeClass('hidden');
					} else{
						$("#shareToHer").removeClass('invisible');
						$("#getPrize").addClass('hidden');
					}
				}
			   
			   showAlert = false;
			}
		}
		$('#scraPercent').html('' + pct + '%');
	};
	/**
	 * Assuming canvas works here, do all initial page setup
	 */
	function initPage() {
		if(!hasChance){
			$(".J_prizetips").css('display','none');//隐藏刮奖提示
			$("#scratcher").css('display','none');//隐藏刮奖区
			$(".chance-tips").removeClass('hidden');//显示明天再来
			$("#shareToHer").removeClass('invisible');//显示分享给ta
			return false;

		}
		var canvasId="scratcher",
			picPath=getPicPath(),
			top=picPath+'/top.png',bottom;
		switch(WinCheck.toString()){
			case '0':
				bottom=picPath+'/bottom-none.png';
				break;
			case '1':
				bottom=picPath+'/bottom-full.png';
				break;
			case '2':
				bottom=picPath+'/bottom-half.png';
				break;
			case '3':
				bottom=picPath+'/bottom-quarter.png';
				break;
			default:
				bottom=picPath+'/bottom-none.png';
		}
		scratcher=new Scratcher(canvasId,bottom,top);
		
		// called each time a scratcher loads
		function onScratcherLoaded(ev) {
			
			//图片加载完了才能开始刮奖
			// get notifications of this scratcher changing
			scratcher.reset();
			scratcher.addEventListener('scratch', scratcherChanged);
		};

		scratcher.addEventListener('imagesloaded', onScratcherLoaded);
		
	};

	/**
	 * Handle page load
	 */
	$(function() {
		if (supportsCanvas()) {
			initPage();
		} else {
			$('#scratcher-box').hide();
			alert('浏览器不支持canvas！');
		}
	});
})();
