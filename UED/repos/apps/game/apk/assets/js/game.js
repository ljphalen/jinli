(function(a,b){a.app("Gapk",function(){a.PathConfig();function c(k,j){if(!k||k.indexOf(",")==-1){return;}var h=k.split(","),e=h.length;if(navigator.gamehall){if(GameApk.ApiVersion=="v1"){e==2?navigator.gamehall.startlistactivity(function(){},function(){},{title:h[0],url:h[1],newArgs:{title:h[0],url:h[1]}}):navigator.gamehall.startdetailsactivity(function(){},function(){},{title:h[0],url:h[1],gameid:h[2],downurl:h[3],packagename:h[4],filesize:h[5],sdkinfo:h[6],resolution:h[7],newArgs:{title:h[0],url:h[1],downloadInfo:[{gameId:h[2],downUrl:h[3],packageName:h[4],fileSize:h[5],sdkVersion:h[6],resolution:h[7]}]}});}else{if(j==0){if(e==2){navigator.gamehall.startlistactivity(function(){},function(){},{title:"",url:"",newArgs:{title:h[0],url:h[1]}});}else{var f=[];for(var g=0;g<e;g+=2){var l={};if(g==0){l.title="热门";}else{l.title=h[g];}l.viewType="Webview";l.url=h[g+1];f.push(l);}navigator.gamehall.startlistactivity(function(){},function(){},{title:"",url:"",newArgs:{title:h[0],items:f}});}}else{if(j==1){if(e==3){navigator.gamehall.startdetailsactivity(function(){},function(){},{title:"",url:"",gameid:"",downurl:"",packagename:"",filesize:"",sdkinfo:"",resolution:"",newArgs:{title:h[0],url:h[1],gameId:h[2]}});}else{if(e==2){navigator.gamehall.startdetailsactivity(function(){},function(){},{title:"",url:"",gameid:"",downurl:"",packagename:"",filesize:"",sdkinfo:"",resolution:"",newArgs:{title:h[0],url:h[1]}});}else{navigator.gamehall.startdetailsactivity(function(){},function(){},{title:"",url:"",gameid:"",downurl:"",packagename:"",filesize:"",sdkinfo:"",resolution:"",newArgs:{title:h[0],url:h[1],gameId:h[2]}});}}}else{if(j==2){navigator.gamehall.startdetailsactivity(function(){},function(){},{title:"",url:"",gameid:"",downurl:"",packagename:"",filesize:"",sdkinfo:"",resolution:"",newArgs:{title:"礼包详情",url:h[1],viewType:"GiftDetailView",gameId:h[2],giftId:h[3]}});}else{if(e==2){navigator.gamehall.startlistactivity(function(){},function(){},{title:h[0],url:h[0]});}}}}}}else{location.href=h[1];}}var d={openMore:function(){b(".wrap").delegate(".J_openBtnWrap","click",function(){b(this).toggleClass("open").siblings("p").toggleClass("hidden");});return this;},slidePic:function(){var f=b("#J_screenshot");var e=b(".pic-wrap");if(!f[0]){return this;}a.include(["touchSwipe","./plugin/slidePic.js"],function(){f.slidePic(f.children().children().hasClass("J_arrow")?{slidePanel:".pic-wrap",slideItem:".pic-wrap span",specialWidth:true,isTouch:false,}:{slidePanel:".pic-wrap",slideItem:".pic-wrap span",specialWidth:true,});d.displayImg();},true);return this;},loadMore:function(){b("#page,#list-page").delegate(".J_loadMore","click",function(){var e=b(this),g=e.attr("data-hasnext");if(g==0||g=="false"){return false;}var f=parseInt(e.attr("data-curpage"));e.html('<img src="'+GameApk.loadingPic+'" />');b.post(e.attr("data-ajaxUrl"),{page:f+1,token:token},function(j){var h=b(".J_gameItem ul"),i="",k="";if(GameApk.ApiVersion!="v1"){k='data-type="{data-type}"';}strTemp="<li>										<a "+k+' data-infpage="{profile}">											<span class="icon"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>											<span class="desc">												<em>{name}</em>												<p><em>大小：{size}</em><em class="tips1"></em></p>												<b>{resume}</b>											</span>										</a>									</li>',tdMerge=function(n,s,o){if(!a.isString(n)||!/\{|\}/g.test(n)){return false;}var p=n.match(/(\{[a-zA-Z]+-[a-zA-Z]+\})|(\{[a-zA-Z]+[a-zA-Z]+\})/g);if(!p.length){return false;}a.foreach(p,function(){var r=this.replace(/\{|\}/g,""),u=new RegExp("{"+r+"}"),t=s[r];n=n.replace(u,t!==undefined?t:(o?"{"+r+"}":""));});if(GameApk.ApiVersion!="v1"&&((s.attach&&s.attach!="")||(s.device&&s.device!=0))){var l='<em class="tips1">';if(s.attach){var q=s.attach.split(",");for(var m=0;m<q.length;m++){if(q[m]=="礼"){l+='<span class="gift"></span>';}if(q[m]=="评"){l+='<span class="comment"></span>';}}if(s.device==1){l+='<span class="grip"></span>';}}else{if(s.device==1){l+='<span class="grip"></span>';}}l+="</em>";n=n.replace('<em class="tips1"></em>',l);}return n;};if(b(".J_categoryItem")[0]){h=b(".first-grade ul");strTemp="<li>										<a "+k+' data-infpage="{profile}">											<span class="pic"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>										</a>									</li>';}else{if(b(".J_subjectItem")[0]){h=b(".first-grade ul");strTemp="<li>										<a "+k+' data-infpage="{profile}">											<span class="pic"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>											<span class="desc">{title}</span>											<span class="mask"></span>										</a>									</li>';}else{if(b(".J_giftItem")[0]){h=b(".J_giftItem ul");strTemp="<li>										<a "+k+' data-infpage="{data-infpage}">											<span class="icon"><img src="'+GameApk.blankPic+'" data-src="{img}"></span>											<span class="desc">												<em>{title}</em>												<p><em >{giftNum}</em></p>											</span>										</a>									</li>';}}}a.foreach(j.data.list,function(l,m){i+=tdMerge(strTemp,l);});h.append(i);d.lazyLoad(document.body,100);if(j.data.hasnext==0||j.data.hasnext=="false"){e.hide();}e.attr("data-hasnext",j.data.hasnext).attr("data-curpage",j.data.curpage).html("<span>加载更多</span>");},"json");});return this;},openPage:function(e){b("body").delegate("a[data-infpage]","click",function(){var f=b(this).attr("data-infpage")||"";if(GameApk.ApiVersion=="v1"){c(f);}else{c(f,parseInt(b(this).attr("data-type"),10));}});},displayImg:function(){var f=b("#J_screenshot"),g=f.find("img");if(!f[0]){return this;}var e=[];g.each(function(h){e[h]=b(this).attr("data-bigpic");b(this).attr("data-index",h);});g.swipe({click:function(){var h=b(this).attr("data-index");var i=[];i=i.concat(h,e);if(navigator.gamehall){navigator.gamehall.startimagescanactivity(function(){a.log("正在为您跳转页面");},function(){},{url:i.join("|")});}}});},lazyLoad:function(f,e){var h=f.querySelectorAll('img[src$="blank.gif"]'),g=function(k){var j=k.getAttribute("data-src");a.__IMAGE_CACHE=a.__IMAGE_CACHE||{};if(!j){return;}if(!a.__IMAGE_CACHE[j]){var i=new Image();i.src=j;i.onload=function(){k.src=j;a.__IMAGE_CACHE[j]=true;i=null;};}else{k.src=j;}};a.foreach(h,function(k,j){e?setTimeout(function(){g(k);},j*e):g(j);});},};return{init:function(){d.slidePic();d.openMore().loadMore();d.openPage();d.lazyLoad(document.body,100);}};});})(ICAT,jQuery);