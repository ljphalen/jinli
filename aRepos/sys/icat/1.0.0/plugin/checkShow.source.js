// JavaScript Document
;(function($){
	$.fn.extend(
	{
		checkShow:function(options)
		{
			options = $.extend({toolsBar:"toolsBar",thName:"txt"},options);
			var toolsBar = options.toolsBar;
			var txt = options.thName;
			var mainTitle = new Array();//存放第一级表头
			var mColspan = new Array()//存放一级表头colspan的个数
			var mSlice = new Array();//拆分第二级表头
			var subTitle = new Array();//存放第二级表头
			var strTools="";//获取checkbox的html字符串
			var $tr = $("tr",this);
			
			var $thMain = $("th",$tr.eq(0));
			for(var i=0;i<$thMain.length;i++)
			{
				var colspan = $thMain.eq(i).attr("colspan");
				if(isNaN(colspan)){ colspan = 1; }
				mColspan[i] = parseInt(colspan);
			}
			for(var i=0;i<$thMain.length;i++)
			{
				if(i==0){ mSlice[i] = mColspan[i]; }
				else{ mSlice[i] = mSlice[i-1] + mColspan[i]; }
			}
			
			var $thSub = $("th",$tr.eq(1));
			for(var i=0;i<$thSub.length;i++){ subTitle[i] = $thSub.eq(i).text(); }
			for(var i=0;i<mSlice.length;i++)
			{
				if(i==0){ mainTitle[i] = subTitle.slice(0,mSlice[i]); }
				else{ mainTitle[i] = subTitle.slice(mSlice[i-1],mSlice[i]); }
			}
			

			function tdControl(ckID,ckClass,isCheck)
			{
				var num = $ckBox.index($("#"+ckID))
				//var isCheck = $ckBox.eq(num).attr("checked");
				for(var i=0;i<mainTitle.length;i++)
				{
					if(ckClass == i)
					{
						if(isCheck == true){mColspan[i] = mColspan[i] + 1;}
						else{mColspan[i] = mColspan[i] - 1;}
						document.all?$thMain[i].setAttribute("colSpan",mColspan[i]):$thMain.eq(i).attr("colspan",mColspan[i]);
					}
				}
				
				if(isCheck == true)
				{
					for(var i=0;i<mColspan.length;i++)
					{
						if(mColspan[i]>0){$thMain.eq(i).show();}
					}
					
					$thSub.eq(num).show();
					$tr.each(function(){$(this).find("td").eq(num).show();});
				}
				else
				{
					for(var i=0;i<mColspan.length;i++)
					{
						if(mColspan[i]==0){$thMain.eq(i).hide();}
						else if(mColspan[i]>0){$thMain.eq(i).show();}
					}
					
					$thSub.eq(num).hide();
					$tr.each(function(){$(this).find("td").eq(num).hide();});
				}

			}
			
			for(var i=0 in mainTitle){
				for(var j=0 in mainTitle[i])
				{
					strTools=strTools+"<input type='checkbox' class='"+i+"' id="+txt+i+j+" checked='checked'>"+mainTitle[i][j];
				}
			}
			strTools="<div id="+toolsBar+">"+strTools+"</div>";
			$(this).before(strTools);
			
			var $ckBox = $("#"+options.toolsBar).find(":checkbox");
			$ckBox.click(function(){
				var ckID = $(this).attr("id");
				var ckClass = $(this).attr("class");
				tdControl(ckID,ckClass,this.checked);
			});

			return this;
		}
	})
})(jQuery);