/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-10
 * Time: 15:16:09
 * Contact: hankewins@gmail.com
 */

/**
 * $.extend.hover
 */

$.extend($.fn, {
	hover: function(cname){
		var that = $(this), cname = cname || "hover";
		that.each(function(k,v){
			$(v).on("touchstart", function(){
				var tid = setTimeout(function(){
					$(v).addClass(cname);
				},50);

				$(document).one("touchmove touchend", function(){
					clearTimeout(tid);
					$(v).removeClass(cname);
				});
			});
		});
	}
});

$(function(){
	var timeId = setTimeout(function(){
		$(".gn-ad").addClass('ishide');
	},3000);

	$('.J_close').on('click',function(){
		clearTimeout(timeId);
		$(this).parent().addClass('ishide');
	});

	$('.btn-touch').hover('touchHover');

	$('.nav-drapdown-title').on('click',function(){
		var that = $(this);
		var drapdownTitle = $('.nav-drapdown-title'), arrowup = 'nav-drapdown-arrow-up';
		drapdownTitle.next().css({display:'none',height:'0'});
		if (that.hasClass(arrowup)) {
			drapdownTitle.removeClass(arrowup);
			that.removeClass(arrowup);
			//that.next().addClass('ishide');
			that.next().animate({
				height: '0',
				display: 'none',
			},50,'linear');
		} else {
			drapdownTitle.removeClass(arrowup);
			that.addClass(arrowup);
			//that.next().removeClass('ishide');
			that.next().animate({
				height: 'auto',
				display: 'block',
			},50,'linear');
			// alert(that.offset().top);
			// window.scrollTo(0,that.offset().top - 6);
		}
	});
});

//get baidu hot keywords for ajax
$(function(){
	var url = $('#api-baidu-hots').val();
	$.getJSON(url,function(res){
		$('.form-input').val(res.data.top2);
	});
});

//auto compalte
$(function(){
	var searchInput = $('.form-input');
	if(!searchInput[0]) return;
	searchInput.addClass("form-input-default");
	searchInput.on('focus',function(evt){
		var that = $(this), defaultValue = that.attr('defaultValue');
		if(that.val() == defaultValue) that.val('').removeClass("form-input-default");
	});

	searchInput.on('blur',function(evt){
		var that = $(this), defaultValue = that.attr('defaultValue');
		if(that.val() == "") that.val(defaultValue).addClass("form-input-default");
	});

	$('.form-button').on('click',function(evt){
		if(searchInput.val() == '') evt.preventDefault();
	});
});

// $('.J_button_drapdown span').on('click',function(){
// 	$(this).next().toggleClass('ishide');
// });

// $('.J_button_drapdown li').on('click',function(){
// 	var text = $(this).text();
// 	var engine = $(this).data('engine');
// 	var form = $('.J_button_drapdown form');
// 	var cur = $('.form-engine span[data-engine='+engine+']');
// 	$('.form-button-drapdown-text').text(text);
// 	$('.J_button_drapdown .form-input').val(cur.text());
// 	$('.J_button_drapdown .form-input').attr('name',cur.data('name'));
// 	$('.J_button_drapdown form').attr('action',cur.data('action'));
// 	$('.J_button_drapdown .ui-poptip').toggleClass('ishide');
// });


// $('.J_button_drapdown form').on('submit',function(e){
// 	e.preventDefault();
// 	var baseUrl = $(this).attr('action'),
// 		sname = $('.J_button_drapdown .form-input').attr('name'),
// 		svalue = $('.J_button_drapdown .form-input').val(),
// 		surl = baseUrl + '&' + sname + '=' + encodeURIComponent(svalue);
// 	//console.log(surl);
// 	window.location.href = surl;
// });
