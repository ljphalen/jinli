(function(a,c){function b(){this.sHtml='<div class="msgMode popWrap hide" style="opacity:0;left:'+this.getPos().left+"px;top:"+this.getPos().top+'px;"></div>';this.wrap=c("#page");this.count=1}b.prototype={construtor:b,templete:function(){c("#page").append(this.sHtml)},message:function(f,d){var e=c(".msgMode");if(this.count==1){this.templete()}this.count++;c(".msgMode").html(f).removeClass("hide").addClass("show").animate({opacity:1},300,"linear");if(c(".msgMode").hasClass("show")){setTimeout(function(){c(".msgMode").animate({opacity:0},300,"linear",function(){c(".msgMode").removeClass("show").addClass("hide")})},1500)}return d},getPos:function(){var d=c(window).width(),e=c(window).height();return{left:d/2-80,top:e/2+100}}};a.app("MH",function(){a.inc("../tempcore.js");return{version:"1.1",getStyle:function(f,h){var d=function(i){return i.replace(/-(\w)/g,function(j,k){return k.toUpperCase()})};if(arguments.length!=2){return false}var g=f.style[d(h)];if(!g){if(document.defaultView&&document.defaultView.getComputedStyle){var e=document.defaultView.getComputedStyle(f,null);g=e?e.getPropertyValue(h):null}else{if(f.currentStyle){g=f.currentStyle[d(h)]}}}return g=="auto"?"":g},scrollLoad:(function(d){var f=(arguments.length==0)?{src:"xSrc",time:300}:{src:d.src||"xSrc",time:d.time||300};var e=function(){var m=window.pageYOffset?window.pageYOffset:window.document.documentElement.scrollTop,n=m+Number(window.innerHeight?window.innerHeight:document.documentElement.clientHeight),l=document.images,h=l.length;if(!h){return false}for(var k=0;k<h;k++){var j=l[k].getAttribute(f.src),p=l[k],g=p.nodeName.toLowerCase();if(p){postPage=p.getBoundingClientRect().top+window.document.documentElement.scrollTop+window.document.body.scrollTop;postWindow=postPage+Number(MH.getStyle(p,"height").replace("px",""));if((postPage>m&&postPage<n)||(postWindow>m&&postWindow<n)){if(g==="img"&&j!==null){p.setAttribute("src",j);p.removeAttribute("class")}p=null}}}c(window).bind("scroll.lazyload",function(){setTimeout(function(){e()},f.time)})};return e()}),}});a.mix(MH,{init:function(){MH.slidePic();MH.addItem();MH.dialog();MH.waterfall();MH.loginForm();MH.common();var d=c("body").attr("dt-cr");if(d){c("a").click(function(e){e.preventDefault();var f=this.getAttribute("dt-labelCla")?"&tid="+this.getAttribute("dt-labelCla"):"";location.href=d+"?url="+encodeURIComponent(this.href)+f})}},slidePic:function(){if(!c(".banner, .cla-menu .wrap, .proPic .wrap, .pic-show .wrap")[0]){return}a.incfile(["/zepto/touchSwipe.js","../slidePic.js"],function(){c(".banner").slidePic({slideItem:".pic>a",circle:true,auto:true});c(".cla-menu .wrap").slidePic({slidePanel:".list",slideItem:".list li",prev:".cla-menu .prevBtn",next:".cla-menu .nextBtn",handleItem:"",fixCurrent:function(){var e=c(".cla-menu .list li"),d=0;c.each(e,function(g,f){var h=c(this).find(".selected");if(h.length){d=g}});return d}});c(".proPic .wrap").slidePic({slidePanel:".pic",slideItem:".pic span",handlePanel:".proPic .handle",handleItem:".proPic .handle span"});c(".pic-show .wrap").slidePic({slidePanel:".list",slideItem:".list li"})})},addItem:function(){var f=c(".J_itemWrap"),d=f.attr("data-ajaxUrl");var e={curpage:0,hasnext:0,num:1};if(!f[0]){return}e.curpage=c("#curInfo").attr("curpage");e.hasnext=c("#curInfo").attr("hasnext");if(e.hasnext==false){return}c(document).bind("scroll.loaditem",function(){if(e.hasnext==false){return}c.post(d,{page:(+e.curpage)+1,token:token},function(h){var g=h;if(g.data.hasnext==false){if(e.num!=1){return}e.num=2;e.curpage=g.data.curpage;e.hasnext=g.data.hasnext;f.append(template("J_itemView",g))}else{if(e.curpage!=g.data.curpage){e.curpage=g.data.curpage;e.hasnext=g.data.hasnext;f.append(template("J_itemView",g))}}},"json");MH.scrollLoad()})},dialog:function(){var f=c(window).width(),i=c(window).height(),h=c(".J_dialogBox"),e,g,d=false;if(!h[0]){return}if(h.css("display")=="none"){e=h.css({"margin-left":"-4999px"}).show().width();g=(f-e)/2;h.css({"margin-left":"0"}).hide()}c(window).bind("resize",function(){f=c(window).width(),i=c(window).height();e=h.width();g=(f-e)/2;h.css("left",g+"px")});function j(m,o,q){var p=m||null,o=o||{token:token},q=q||"json",l=c("#J_reward_btn").attr("data-url"),n=0;c.post(p,o,function(r){var s=r;if(s.success){if(s.data.type=="redirect"&&s.data.url){location.href=s.data.url;return}if((s.data.maxCheckNum==s.data.hadCheckNum)&&(s.data.picId==o.picId)){c(".J_checkInDialog").addClass("disabled");c(".J_dialogBox .btn").text("\u6211\u77e5\u9053\u4e86");n=1}else{c(".cell ul li").each(function(t){if(t<s.data.hadCheckNum){c(this).find("span").not(".done").addClass("done")}});c(".J_checkInDialog").text("\u5df2\u7b7e\u5230").unbind("click");c(".J_dialogBox .btn").text("\u597d\u7684")}d=true}else{c(".J_checkInDialog").removeClass("disabled");d=false}h.find("p").html(s.msg);if(s.data.isLogin=="0"){c(".J_checkInDialog").addClass("disabled");c(".J_dialogBox .btn-wrap").html('<span id="J_cancel_btn" class="btn gray w50">\u53d6\u6d88</span><span class="btn gray w50" id="J_login_btn">\u767b\u5f55</span>');h.css("left",g+"px").show();c("#J_cancel_btn").live("click",function(){h.hide();c(".J_checkInDialog").removeClass("disabled")});c("#J_login_btn").live("click",function(){h.hide();c(".J_checkInDialog").removeClass("disabled");location.href=s.data.loginUrl})}else{if(n==1){h.find(".btn-wrap").hide();h.find("p").css("border-bottom","none").html("\u60a8\u5df2\u7ecf\u62fc\u5b8c\u6574\u5f20\u7b7e\u5230\u62fc\u56fe\uff0c\u73b0\u5728\u53ef\u4ee5\u53bb\u5151\u5956\u4e86\uff01");h.css("left",g+"px").show();setTimeout(function(){h.hide();c(".JS-dbMask").hide();c("#J_reward_btn").removeClass("disabled").attr("href",l);c("#J_reward_btn").bind("click",function(){c(this).addClass("disabled")});c(".cell ul li span").addClass("done")},3000)}else{h.css("left",g+"px").show();c(".J_dialogBox .btn").bind("click",function(t){h.hide()})}}},q)}function k(){h.css("left",g+"px").show().find(".btn").bind("click",function(){c(".JS-dbMask").hide();h.hide()})}c(".J_showDialog").click(function(l){l.preventDefault();if(this.getAttribute("data-ajaxUrl")){var m=this.getAttribute("data-ajaxUrl");j(m)}else{c("form input").blur();k()}});c(".J_checkInDialog").bind("click",function(l){l.preventDefault();if(c(this).hasClass("disabled")){return}c(this).addClass("disabled");if(this.getAttribute("data-ajaxUrl")){var n=this.getAttribute("data-ajaxUrl"),m=this.getAttribute("data-picId");j(n,{picId:m,token:token})}else{k()}});c("#J_prizeBtn").bind("click",function(m){m.preventDefault();if(c(this).hasClass("disabled")){return}c(this).addClass("disabled");if(c(this).attr("data-ajaxUrl")){var n=this.getAttribute("data-ajaxUrl");var l=c(".prize-list ul li");c.post(n,{token:token},function(p){var o=p;if(o.success){if(o.data.prizeId){l.each(function(q){var r=c(this).find("img").attr("data-prizeId");if(r==o.data.prizeId){c(this).find("img").animate({width:"8rem",height:"8rem","box-shadow":"2px 2px 5px rgba(0,0,0,.5)",left:"-0.8rem",top:"-0.8rem","z-index":100},300,"ease-in-out",function(){setTimeout(function(){var s=o.data.redirectUrl.indexOf("?")>-1?"&":"?";location.href=o.data.redirectUrl+s+"prize_id="+o.data.prizeId},2000)},1500)}else{c(this).find("span").css({opacity:0.3,background:"none"})}})}else{h.find("p").html("\u672a\u4e2d\u5956\uff0c\u4e0b\u6b21\u518d\u8bd5\u8bd5\u624b\u6c14\uff01");h.css("left",g+"px").show();c(".J_dialogBox .btn").live("click",function(q){h.hide()})}}else{h.find("p").html(o.msg);h.css("left",g+"px").show();c(".J_dialogBox .btn").live("click",function(q){h.hide()})}},"json")}});c("#J_update_userInfo").bind("click",function(m){m.preventDefault();console.log(c(this).attr("data-ajaxUrl"));if(c(this).attr("data-ajaxUrl")){var o=c(this).attr("data-ajaxUrl");var l=false;var n={token:token,realname:c("input[name=realname]").val(),mobile:c("input[name=mobile]").val(),qq:c("input[name=qq]").val(),address:c("input[name=address]").val()};c.post(o,n,function(q){var p=q;console.log(p);if(p.success){h.find("p").html(p.msg);if(p.data.redirectUrl){l=true}}else{h.find("p").html(p.msg);l=false}h.css("left",g+"px").show();c(".J_dialogBox .btn").live("click",function(r){if(l===true){location.href=p.data.redirectUrl}else{h.hide()}})},"json")}});return this},loginForm:function(){var e=new b();var d={username:document.querySelector("#J_UserNameTxt"),password:document.querySelector("#J_PassWordTxt"),passshow:document.querySelector(".J_showText"),plaintxt:document.querySelector(".J_ctrlShow")};if(d.plaintxt){d.plaintxt.addEventListener("click",function(){if(this.checked===true){d.password.style.display="none";d.passshow.style.display="block";d.passshow.value=d.password.value}else{d.password.style.display="block";d.passshow.style.display="none";d.password.value=d.passshow.value}},false)}c("#J_login").submit(function(f){f.preventDefault();if(d.username.value==""){return e.message("\u8bf7\u8f93\u5165\u624b\u673a\u53f7\uff01",false)}if(d.plaintxt){if(d.plaintxt.checked){d.password.value=d.passshow.value}}if(d.password.value==""){return e.message("\u8bf7\u8f93\u5165\u5bc6\u7801\uff01",false)}if(d.password.value!=""&&d.password.value.length<6){return e.message("\u5bc6\u7801\u81f3\u5c116\u4f4d\uff01",false)}d.actionUrl=c(this).attr("action");c.post(d.actionUrl,{username:d.username.value,password:d.password.value,token:token},function(g){var h=g;if(h.success==false){return e.message(h.msg,false)}else{if(e.message(h.msg,true)){if(h.data.type=="redirect"&&h.data.url){setTimeout(function(){location.href=h.data.url},2000)}}}},"json")})},waterfall:function(){var h=c(".pic-box"),i=[];var g={curpage:0,hasnext:0,num:1};var f=document.createDocumentFragment();var e=c(".pic-box").attr("data-ajaxUrl"),d=c(".chartlet .btn");g.curpage=c("#curInfo").attr("curpage");g.hasnext=c("#curInfo").attr("hasnext");if(!h[0]){return}if(g.hasnext==false){d.show();return}else{d.hide()}c(window).bind("scroll.loaditem",function(){if(g.hasnext==false){console.log(g.curpage);d.show();return}c.post(e,{page:parseInt(g.curpage)+1,token:token},function(o){var l=o;if(l.success){if(l.data.hasnext==false){if(g.num!=1){return}g.num=2;g.curpage=l.data.curpage;g.hasnext=l.data.hasnext;c("#curInfo").attr("curpage",l.data.curpage);c("#curInfo").attr("hasnext",l.data.hasnext);c(f).append(template("J_picItemView",l));i=f.querySelectorAll("li");for(var n=0,k=i.length;n<k;n++){var j=c(".pic-box .l-wrap ul"),m=c(".pic-box .r-wrap ul");if(j.height()>m.height()){m.append(i[n])}else{j.append(i[n])}}}else{if(g.curpage!=l.data.curpage){g.curpage=l.data.curpage;g.hasnext=l.data.hasnext;c(f).append(template("J_picItemView",l));i=f.querySelectorAll("li");for(var n=0,k=i.length;n<k;n++){var j=c(".pic-box .l-wrap ul"),m=c(".pic-box .r-wrap ul");if(j.height()>m.height()){m.append(i[n])}else{j.append(i[n])}}}}}},"json");MH.scrollLoad()})},common:function(){var g=c("#J_load_more"),f=c("#J_box_expand"),e=c("#J_box_collapse"),d=g.find("span");g.bind("click",function(){if(d.hasClass("expend")){d.removeClass("expend");d.html("\u5c55\u5f00<i>&gt;&gt;</i>");e.show();f.hide()}else{d.addClass("expend");d.html("\u6536\u8d77<i>&gt;&gt;</i>");f.show();e.hide()}});c(".item-list li > a").live("touchstart",function(){c(this).css("background","#d5d5d5")});c(".item-list li > a").live("touchmove",function(){c(this).css("background","transparent")});c(".item-list li > a").live("touchend",function(){c(this).css("background","transparent")})}});c(function(){MH.init()})})(ICAT,Zepto);