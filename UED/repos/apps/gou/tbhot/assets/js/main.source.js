/*
 * Applaction: tbhot
 * Author: vk
 * Date: 2013-11-18 09:07:51.
 */

(function(iCat){
	//定义应用
	iCat.app('TBHOT', function(){
		return {
			version: '0.0.1',
			init: function(){
				iCat.include(['jQuery', 'MVC'], function($){
					var wrap = $('#home'),
						ajaxUrl = wrap.attr('data-ajaxurl'),
						temp = '<div class="screen-pic">\
									<a href="<%=data.ad.link%>" style="background:<%=data.ad.color%>;"><img src="<%=data.ad.img%>" alt="<%=data.ad.title%>"></a>\
									<div class="close J_closePage"><span></span></div>\
								</div>';
					$.post(ajaxUrl, function(data){
						var jsonData = data && iCat.isString(data)?
								(/^[\[\{](.+:.+,?){1,}[\}\]]$/.test(data)? JSON.parse(data) : null) : data,
							hotHref = jsonData.data.tbhot_url, shtml = '',
							oImg;
						
						if(jsonData && jsonData.data.ad){
							shtml = iCat.util.render(temp, jsonData);
							wrap.html(shtml)
								.on('click', '.J_closePage', function(){
									location.href = hotHref;
								});

							oImg = wrap.find('img');
							oImg.bind('load', function(){
								console.log(0);
								oImg.unbind('load');
								setTimeout(function(){
									location.href = hotHref;
								}, 5000);
							});
						} else {
							location.href = hotHref;
						}
					});
				}, true);
			}
		};
	});

	//初始化
	TBHOT.init();
})(ICAT);