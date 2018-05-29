	// JavaScript Document
//首页的图片滚动
function IndexScrollAll(config){
	this.goods_event=config.goods_event;//小图的列表
	this.scroll_cont=config.scroll_cont;//滚动的内容
	this.imgFlashIco=config.imgFlashIco;//小图标
	this.PageSizeNum=config.PageSizeNum||1;//每页的个数默认1
};
IndexScrollAll.prototype={
	Start:function(){
		var goods_event=this.goods_event,
			scroll_cont=this.scroll_cont,
			imgFlashIco=this.imgFlashIco,
			PageSizeNum=this.PageSizeNum,
			len = $(goods_event).length,
			_goodsW = $(goods_event).height(),
			_scrollW = $(scroll_cont),
			_Num=1,
			CurrentSum=1,
			addOn="on",
			_Page=_goodsW*PageSizeNum,//移动的距离
			_PageSize=Math.ceil(len/PageSizeNum);
			//分页数量
		
		//初始化宽度和margin-top
		_scrollW.css({"margin-top":0});
		//自动
		var timer=null,
			setTimes=3000;
		if(!timer)
		{
			timer=window.setTimeout(imgAuto,setTimes);
		}
		
		//按钮函数
		function ShowPrev(){
			if (!_scrollW.is(":animated")) {
				if(CurrentSum<_PageSize)
				{
					CurrentSum=CurrentSum+PageSizeNum;
					_Num=CurrentSum;
					var _CurrentPage=Math.ceil(_Num/PageSizeNum);
					if(_CurrentPage<=_PageSize)
					{
						_scrollW.animate({marginTop:-_Page*(_CurrentPage-1)+"px"},500);
						BaseShowOn(CurrentSum-1);
					}
				}
			}
		};
		function ShowNext(){
			if (!_scrollW.is(":animated")) {
				if(CurrentSum>PageSizeNum)
				{
					CurrentSum=CurrentSum-PageSizeNum;
					_Num=CurrentSum;
					var _CurrentPage=Math.ceil(_Num/PageSizeNum);
					if(_CurrentPage<=_PageSize)
					{
						_scrollW.animate({marginTop:-_Page*(_CurrentPage-1)+"px"},500);
						BaseShowOn(CurrentSum-1);
					}
				}
			}
		};
		
		//各种状态
		function BaseShowOn(i){
			$(imgFlashIco).siblings().removeClass(addOn).eq(i).addClass(addOn);
		};
		
		//处理自动轮播和点击小图标的切换状态
		function AllCurrent(i){
			if(len>PageSizeNum)
			{
				var _AllCurrentPage=Math.ceil(i/PageSizeNum);
					CurrentSum=(_AllCurrentPage-1)*PageSizeNum+1;
					if(_AllCurrentPage<=_PageSize)
					{
						_scrollW.animate({marginTop:-_Page*(_AllCurrentPage-1)+"px"},500);
					}
			}
		};
		//处理自动播放的函数
		function imgAuto()
		{
			if(_Num<len)
			{
				BaseShowOn(_Num);
				_Num=_Num+1;
				AllCurrent(_Num);
			}
			else
			{
				_Num=0;
			}
			clearTimeout(timer);
			timer=window.setTimeout(imgAuto,setTimes);
		};	
		
		//各种事件
			
		$(goods_event).hover(function(){clearTimeout(timer);},function(){timer=window.setTimeout(imgAuto,setTimes);});
		var n=0;	
		$(imgFlashIco).hover(function(){
			if(n==0)
			{
				clearTimeout(timer);
				var _this=$(this),
					_index=_this.index();
					_Num=_index+1;
					BaseShowOn(_index);	
					AllCurrent(_Num);
					n=1;
			}
		},function(){if(n==1){timer=window.setTimeout(imgAuto,setTimes);n=0;}});
	}
};
//处理图片的问题
$(function(){
	//计算图片距离左边屏幕的距离
	function sumLeft(){
		var w_left=0,
			move_left=0,
			setImgW=2600,//这个是图片的宽度,这个效果的图片宽度全部统一,根据实际情况自行修改
			setBodyW=990;//这个是页面中间的宽度,根据实际情况自行修改
		w_left=parseInt(($(window).width()-setBodyW)/2,10);
		move_left=parseInt((setImgW-setBodyW)/2,10);
		var $js_img=$(".js_img");
		if(w_left>0)
		{
			$js_img.find(".img_wrap").css({"left":-move_left+w_left});
			$js_img.width("auto");
		}
		else
		{
			$js_img.find(".img_wrap").css({"left":-move_left});
			$js_img.width(setBodyW);
		}
	}
	sumLeft();
	$(window).resize(function(){
		setTimeout(function(){sumLeft();},100);//减少浏览器负担
	});
})