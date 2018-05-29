/*!
 * @module gionee's ebook
 * @author valleykid(valleykiddy@gmail.com)
 * @date 2012-08-14
 */

(function(iCat, $){
	iCat.app('EBOOK', function(){
		return {
			version: '1.0'
		};
	});
	
	iCat.mix(EBOOK, {
		scrollPic: function(){
			iCat.include('/jtouchSwipe/jtouchSwipe.js', function(){
				if(!$('.J_scrollPic')[0]) return;
				var arrImg = [
					iCat.appRef+'/pic/cover.jpg|',
					iCat.appRef+'/pic/3.jpg|',
					iCat.appRef+'/pic/2.jpg|article_jiaoqiao.php',
					iCat.appRef+'/pic/1.jpg|article_daxiong.php',
					iCat.appRef+'/pic/4.jpg|article_xinjie.php',
					iCat.appRef+'/pic/5.jpg|article_tangguo.php',
					iCat.appRef+'/pic/6.jpg|article_lushang.php'
				],
				picBox = $('.J_scrollPic .pic'), more = picBox.next('.more'),
				w = picBox.width(), i = 0, len = arrImg.length;
				
				picBox.swipe({swipeLeft:swipe1, swipeRight:swipe2, allowPageScroll:'auto'});
				
				function swipe1(){
					if(i>=len-1){
						i=len-1;
						return false;
					} else {
						i++;
					}
					
					var imgInf = arrImg[i].split('|');
					picBox.append('<a class="loading" href="'+imgInf[1]+'" style="z-index:'+i+';"><img src="'+iCat.appRef+'/pic/loading.gif" alt="" /></a>');
					/*var img = new Image(); img.src = imgInf[0];
					if(img.complete){
						picBox.find('a:eq(1)').removeClass('loading').html('<img src="'+imgInf[0]+'" alt="" />');
						delete img;
					} else {
						img.onload = function(){
							picBox.find('a:eq(1)').removeClass('loading').html('<img src="'+imgInf[0]+'" alt="" />');
							delete img;
						}
					}*/
					setTimeout(function(){
						picBox.find('a:eq(1)').removeClass('loading').html('<img src="'+imgInf[0]+'" alt="" />');
					},800);
					$('a:eq(0)', this).css('z-index',i+1).animate({left:-w+'px'},1000, function(){
						$(this).remove();
						more.find('a').attr('href', imgInf[1]);
						i==0? more.hide() : more.show();
					});
					return false;
				};
				
				function swipe2(){
					if(i<=0){
						i=0;
						return false;
					} else {
						i--;
					}
					
					var imgInf = arrImg[i].split('|');
					picBox.append('<a href="'+imgInf[1]+'" style="z-index:'+i+'; left:-'+w+'px;"><img src="'+imgInf[0]+'" alt="" /></a>');
					/*var img = new Image(); img.src = imgInf[0];
					if(img.complete){
						picBox.find('a:eq(1)').removeClass('loading').html('<img src="'+imgInf[0]+'" alt="" />');
						delete img;
					} else {
						img.onload = function(){
							picBox.find('a:eq(1)').removeClass('loading').html('<img src="'+imgInf[0]+'" alt="" />');
							delete img;
						}
					}*/
					$('a:eq(0)', this).css('z-index',i-1);
					$('a:eq(1)', this).animate({left:0},1000, function(){
						
						$(this).prev('a').remove();
						more.find('a').attr('href', imgInf[1]);
						i==0? more.hide() : more.show();
					});
					/*$(this).append('<span><img src="'+imgInf[0]+'" alt="" /></span>')
						.find('span').live('click', function(){
							location.href = imgInf[1];
						});*/
					return false;
				};
			});
		}
	});
	
	//执行入口
	$(function(){
		var str = localStorage.getItem('test')? localStorage.getItem('test')+',a':'aaa';
		localStorage.setItem('test', str);
		alert(localStorage.getItem('test'));
		EBOOK.scrollPic();
	});
})(ICAT, jQuery);