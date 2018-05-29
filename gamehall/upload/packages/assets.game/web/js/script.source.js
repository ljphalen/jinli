$(function(){
	//轮播
	if($('#sliderPlay').length && $('#sliderPlay').find("ul.slide_list").children('li').length>1){
		$('#sliderPlay').find("div.banner_slide").unslider({
			dots:true
		});
	}

	//懒加载
	lazyload(100);
	//排行
	$('.rank_list li').hover(function(){
	    $(this).addClass('first').siblings().removeClass('first');
	});
	//回到顶端
	if ($('.other_fixed_assist').length > 0) {
	    if ('undefined' == typeof (document.body.style.maxHeight) && !($.support.style)) {
	        var totop_top = $('.other_fixed_assist').position().top;
	        var wheight = $(window).height();
	        $(window).scroll(function () {
	            var top = $(window).scrollTop();
	            if ($(window).scrollTop() > 200) {
	                $('.other_fixed_assist').show().css({ 'top': totop_top + top - 80 });
	                $('#top').show().css("display", "block");
	            } else {
	                $('.other_fixed_assist').show().css({ 'top': totop_top + top });
	                $('#top').hide();
	            }
	        });
	    } else {
	        $(window).scroll(function () {
	            if ($(window).scrollTop() > 200)
	                $('#top').show().css("display", "block");
	            else
	                $('#top').hide();
	        });
	    }
	    $('#top').click(function () {
	        $('html,body').animate({ 'scrollTop': 0 }, 100);
	    });
	}

	//用户反馈表单验证
	// var radioV=0;
	$(".edit_list input").focus(function(){
	    $(this).parent().addClass('cur').siblings().removeClass('cur');
    });
	$(".edit_list li .ico,.edit_list li .input_txt").click(function(){
	    $(this).parents('li').addClass('cur').siblings().removeClass('cur');
	    var inputs=$(this).parents('li').siblings('li').children('input');
	    inputs.each(function(){
	    	$(this).val($(this).attr('placeholder'));
	    })
		// radioV=$(this).parents('li').index();
    });

	// $('.edit_list input:text').focus(function(){
	// 	$(this).val('').parent().siblings().find('input').val('');
	// })

	$('#btn_feedback_submit').removeAttr('disabled');
    $('#form_feedback').submit(function(){
    	$('.feedback_error').hide();
		var content = $.trim($('#content').val());
    	var mobile_text = $.trim($('#mobile').val());
    	var email_text = $.trim($('#email').val());
    	var qq_text= $.trim($('#qq').val());
    	var re_mobile = /^(0|86|17951)?(13[0-9]|15[012356789]|18[02356789]|14[57])[0-9]{8}$/g;
    	var re_email = /\w@\w*\.\w/g;
    	var re_qq	= /^[1-9]\d{5,11}$/g ;
    	var cur_input_id=$("li.cur").children('input').attr('id');

		
		if (content == '' || content == '请填写反馈内容') {
			$('#feedback_error_content_empty').show();
			return false;
		}

		if (content.length > 1000) {
			$('#feedback_error_content_max').show();
			return false;
		}
		if(cur_input_id=='mobile') {
			if (mobile_text == '' || mobile_text == $('#mobile').attr('placeholder') || !re_mobile.test(mobile_text)) {
				$('#feedback_error_mobile').show();
				return false;
			}else{
				$('#feedback_error_mobile').hide();
			}
		}
		if(cur_input_id=='email') {
			if (email_text == '' || email_text == $('#email').attr('placeholder') || !re_email.test(email_text)) {
				$('#feedback_error_email').show();
				return false;
			}else{
				$('#feedback_error_email').hide();
			}	
		}
		if(cur_input_id=='qq') {
			if (qq_text == '' || qq_text == $('#qq').attr('placeholder') || !re_qq.test(qq_text)) {
				$('#feedback_error_qq').show();
				return false;
			}else{
				$('#feedback_error_qq').hide();
			}	
		}	
    });
	
	//搜索框表单验证 空字符检测
	$('#form1').submit(function(){
		var header_search_content = $.trim($("#header_search_content").val());

		if (header_search_content == '' || header_search_content == '搜索游戏名称/标签') {
			alert('输入关键字不能为空');
             return false;
		}
		return true;
	})
	
	//show more game info in detail page 
	$("#j_toggle").click(function(){
		if($("#j_toggle").hasClass('show')){
			$("#j_toggle").html('收起');
			 $('#j_content').addClass('auto');
			 $("#j_toggle").removeClass('show');
			 $("#j_toggle").addClass('hide');
			return ;
		}
		if($("#j_toggle").hasClass('hide')){
			$("#j_toggle").html('展开更多');
			$('#j_content').removeClass('auto');
			 $("#j_toggle").removeClass('hide')
			 $("#j_toggle").addClass('show');
			return ;
		}
	});

	// when there is no notice in notice page
	equal_height();
	
	//share
	window.jiathis_config={};
	share();

	//// 让不支持placeholder的浏览器实现此属性
	var input_placeholder = $("input[placeholder],textarea[placeholder]");
	if (input_placeholder.length !== 0 && !supports_input_placeholder()) {
	
		var color_place = "#A9A9A9";	
		
		$.each(input_placeholder, function(i){
			var isUserEnter = 0; // 是否为用户输入内容,placeholder允许用户的输入和默认内容一样
			var ph = $(this).attr("placeholder");
			var curColor = $(this).css("color");
			
			$(this).val(ph).css("color", color_place);
		
			$(this).focus(function(){
				if ($(this).val() == ph && !isUserEnter) $(this).val("").css("color", curColor);
			})
			.blur(function(){
				if ($(this).val() == "") {
					$(this).val(ph).css("color", color_place);
					isUserEnter = 0;
				}
			})
			.keyup(function(){
				if ($(this).val() !== ph) isUserEnter = 1;
			});
			
		});
	}


	/*返回顶部*/
	$("#top").click(function() {
		$('body,html').animate({
			scrollTop: 0
		},500);
		return false;
	});
	/*底部微信展示*/
	$("#weixin").mouseover(function(){
			$("#wx").show();
		});
	$("#weixin").mouseout(function(){
		$("#wx").hide();
	});
	/*分享show*/
	var t;
	$(".share_c").mouseover(function(){
		$(".share_c .share_show").show();
	    clearTimeout(t);
	});
	$(".share_c").mouseout(function(){
		 t = setTimeout("shareHide()",1000);
	});
});
$(window).resize(function(){
	lazyload(100);
});
$(window).bind('scroll', function() {
	var navHeight = $( '.head' ).height();
	 if ($(window).scrollTop() > navHeight -10) {
		 $('.menu').addClass('fixed');
	 }
	 else {
		 $('.menu').removeClass('fixed');
	 }
	 lazyload(100);
});


function shareHide(){
     $(".share_c .share_show").hide();        
}

function supports_input_placeholder(){
	var i = document.createElement("input");
	return "placeholder" in i;
}

//分享
function share(){
	var container=$("[data-tag=share]");
	if(!container[0]) return;
	with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src=
			'http://v1.jiathis.com/code/jia.js'];
	container.on('mouseover',function(){
		jiathis_config.title = $(this).attr("data-text");
  		jiathis_config.url = $(this).attr("data-url");
  		jiathis_config.pic=$(this).attr("data-pic");
  		jiathis_config.summary=' ';
  		
	});
}
//加入收藏
function AddFavorite(sURL, sTitle) {
    sURL = encodeURI(sURL);
    try {
        window.external.addFavorite(sURL, sTitle);
    } catch (e) {
        try {
            window.sidebar.addPanel(sTitle, sURL, "");
        } catch (e) {
            alert("加入收藏失败，请使用Ctrl+D进行添加,或手动在浏览器里进行设置.");
        }
    }
}
//设为首页
function SetHome(url) {
    if (document.all) {
        document.body.style.behavior = 'url(#default#homepage)';
        document.body.setHomePage(url);
    } else {
        alert("您好,您的浏览器不支持自动设置页面为首页功能,请您手动在浏览器里设置该页面为首页!");
    }
}
//添加桌面
function ToDesktop(sUrl, sName) {
    try {
        var WshShell = new ActiveXObject("WScript.Shell");
        var oUrlLink = WshShell.CreateShortcut(WshShell.SpecialFolders("Desktop") + "\\" + sName + ".url");
        oUrlLink.TargetPath = sUrl;
        oUrlLink.Save();
    }
    catch (e) {
        alert("当前浏览器安全级别不允许操作！");
    }
}


//排行高度
var HEIGHT_RANK_BOX  = $('.rank_box').innerHeight();
var HEIGHT_MAIN = $('.main').innerHeight();
function equal_height(){
	$('.notice_box').css('height', 'auto');
    var height_main_box = $('.main').innerHeight();
    if (HEIGHT_RANK_BOX > height_main_box) {
    	$('.notice_box').innerHeight(HEIGHT_RANK_BOX + 20);
    } else {
    	$('.rank_box').innerHeight(HEIGHT_RANK_BOX);
    	// $('.main').innerHeight(height_notice_box);
    	// $('.notice_box').innerHeight(height_notice_box);
    }
}


//lazyload
function lazyload(t){
 	var imgCache={};
	var imgs = $('img[data-original]'),
		_fn = function(o){
			var src = o.getAttribute('data-original');
			if(!src) return;

			o.src=src;
			o.removeAttribute("data-original");
		};
	imgs.each(function(i,v){
		t ? setTimeout(function(){ _fn(v); }, i*t) : _fn(v);
	});
}









