<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <title>3D Gallery with CSS3 and jQuery</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" type="text/css" href="css/main.source.css" />
    </head>
    <body>
		<header class="panel" >
		    <div class="roundedChk">
		      <input type="checkbox" value="5" id="ck5" name="check" checked />
		      <label for="ck5"></label>
		    </div>
		    <span class="prizename">幸福奖</span> 
		    <div class="roundedChk">
			    <input type="checkbox" value="4" id="ck4" name="check" />
			     <label for="ck4"></label>
		  	</div>
			<span class="prizename">四等奖</span>
		  	<div class="roundedChk">
			      <input type="checkbox" value="3" id="ck3" name="check" />
			      <label for="ck3"></label>
			</div>
			<span class="prizename">三等奖</span>
			<div class="roundedChk">
			    <input type="checkbox" value="2" id="ck2" name="check" />
			    <label for="ck2"></label>
			</div>
			<span class="prizename">二等奖</span>
			<div class="roundedChk">
			    <input type="checkbox" value="1" id="ck1" name="check" />
			    <label for="ck1"></label>
			</div>
			<span class="prizename">一等奖</span>
			<div class="roundedChk">
			    <input type="checkbox" value="9" id="ck0" name="check" />
			    <label for="ck0"></label>
			</div>
			<span class="prizename">特等奖</span>
		</header>
		<section>
  			<div class="container">
				<section id="dg-container" class="dg-container">
					<div class="dg-wrapper">
						<!-- <a href="#"><img src="ava/1.jpg" alt="image01"></a>
						<a href="#"><img src="ava/2.jpg" alt="image02"></a>
						<a href="#"><img src="ava/3.jpg" alt="image03"></a>
						<a href="#"><img src="ava/4.jpg" alt="image04"></a>
						<a href="#"><img src="ava/5.jpg" alt="image05"></a>
						<a href="#"><img src="ava/6.jpg" alt="image06"></a>
						<a href="#"><img src="ava/7.jpg" alt="image07"></a>
						<a href="#"><img src="ava/8.jpg" alt="image08"></a>
						<a href="#"><img src="ava/9.jpg" alt="image09"></a>
						<a href="#"><img src="ava/10.jpg" alt="image10"></a>
						<a href="#"><img src="ava/11.jpg" alt="image11"></a>
						<a href="#"><img src="ava/12.jpg" alt="image12"></a> -->
					</div>
				</section>
				<a href="javascript:void(0);" id="btn" class="button button-red"><span>开始</span></a>
	        </div>
	    </section>
	    <div class="mask">
	    	<a id="close" href="javascript:;"></a>
	    	<img class="congratulation" src="img/congratulation.png" >
	    	<ul class="winnerList"></ul>
	    </div>
	    </div>
		<script type="text/javascript" src="http://code.jquery.com/jquery-2.0.3.js"></script>
		<script type="text/javascript" src="js/jquery.gallery.js"></script>
		<script type="text/javascript">
			$(function() { 
				var membersList=[],
					gallery,
					prizeList=[];
				//初始化成员列表
				$.ajax({
					url:'prize.php',
					type:'POST',
					dataType:'json',
					success:function(data){
						membersList=data.data;
						gallery=updateGallery(membersList);
					}
				});
				
				$(window).resize(function(event) {
					$('.mask').width(document.body.scrollWidth).height(document.body.scrollHeight);
				});
				//开始按钮事件
				$('#btn').on('click', function(event) {
					var span=$(this).find('span'),
						prizeFlag=$('input[type=checkbox]:checked').attr('value');
					if(span.text()=='开始'){
						if(prizeFlag==undefined) {
							alert('请选择一个奖项')
							return false;
						}
						span.text('结束');
						gallery.options.autoplay=true;
						gallery.start();
						$.ajax({
							url:'chance.php',
							data:{"level":prizeFlag},
							type:'post',
							dataType:'json',
							success:function(data){
								prizeList=data;
								membersList=distinctArr(membersList,data);
								
							}
						})
					} else {
						span.text('开始');
						gallery.options.autoplay=false;
						gallery.stop();
						showDialog(prizeList);
						gallery=updateGallery(membersList);
					}
				});

				$('input[name=check]').click(function(){
					$(this).parent('div').siblings('div').children('input[type=checkbox]').attr('checked',false);
				});
				//close
				$("#close").click(function(){
					$('.winnerList').empty();
					$('.mask').hide();
				});
			});
			//设置轮播的图片
			function updateGallery(list){
				var galleryStr='';
				for(var i=0,len=list.length;i<len;i++){
					galleryStr+='<a href="#"><img src="'+list[i].avatar+'" data-id="'+list[i]['_id']+'"></a>';
				}
				$(".dg-wrapper").html(galleryStr);
				//图片播放初始化                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
				gallery=$('#dg-container').gallery({
				});
				return gallery;
			}
			//数组去重
			function distinctArr(arr1,arr2){
				 var temparray=arr1;
				 for(var i=0;i<arr1.length;i++){
				 	for(var j=0;j<arr2.length;j++){
				 		if(arr1[i]['_id']==arr2[j]['_id']){
				 			temparray.splice(i,1);
				 		}
				 	}
				 }
				 return temparray;
			}

			//弹出中奖成员
			function showDialog(list){
				if(!list||list.length==0) return;
				$('.mask').show();
				var str='';
				for(var i=0;i<list.length;i++){
					str+='<li><img src="'+list[i]['avatar']+'" alt=""></li>';
				}
				$('.winnerList').html(str);
			}
		</script/>
    </body>
</html>