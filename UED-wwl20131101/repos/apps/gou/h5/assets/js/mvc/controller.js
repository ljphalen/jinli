(function(a){var h,d,g,b,e,c;e=/super\.|client\.|app\./i.test(location.host)?false:true;c=/app\./i.test(location.host)?true:false;if(e){h="header,";d="header#iHeader.hd + div#iScroll";g="topBanner, header, gotop, recommend, notice, mall, theme, tuan, helper, footer";}else{if(!e&&!c){h="";d="div#iScroll";b="gotop, banner, points, notice, mall, theme, tuan, helper, footer1";}else{if(!e&&c){h="header,";d="header#iHeader.hd + div#iScroll";b="header, gotop, recommend, notice, mall, theme, tuan, helper, footer1";}}}var f=a.Controller.extend({config:{baseBed:".module"},routes:{home:"homeInit",cod:"codInit",shops:"shopsInit","new":"newInit",mall:"mallInit",jhnews:"jhnewsInit"},gotop:function(){window.onscroll=function(){if(document.body.scrollTop<50){$(".gotop").hide();}else{$(".gotop").show();}};},homeInit:function(){var j=this,i=Gou.view.setting.header.nav;i.forEach(function(k,l){l==0?k.selected=true:delete k.selected;});j.init({view:Gou.view,model:Gou.model,adjustLayout:(e?"div.top-banner+":"")+d,modules:e?g:b});j.gotop();},codInit:function(){var j=this,i=Gou.view.setting.header.nav;i.forEach(function(k,l){l==1?k.selected=true:delete k.selected;});j.init({view:Gou.view,model:Gou.model,adjustLayout:d,scrollBox:document,modules:h+"gotop, codSearch, list"});j.gotop();},shopsInit:function(){var j=this,i=Gou.view.setting.header.nav;i.forEach(function(k,l){l==2?k.selected=true:delete k.selected;});j.init({view:Gou.view,model:Gou.model,scrollBox:document,adjustLayout:d+" > div.panel.shop-info",modules:h+"gotop, shopList"});j.gotop();},newInit:function(){var i=this;Gou.view.setting.subheader.subtitle="新品女装";i.init({view:Gou.view,model:Gou.model,scrollBox:document,adjustLayout:"header#iHeader.hd + div#iScroll",modules:"gotop, subheader, newlist"});i.gotop();},mallInit:function(){var k=this,i=/super\./i.test(location.host)?true:false,j=Gou.view.setting.subheader;a.mix(j.config,{ajaxUrl:"/api/fitting/type",hooks:{"&>0:1":".parts-nav.J_partsNav"}});j.subtitle="手机配件";k.init({view:Gou.view,model:Gou.model,scrollBox:document,adjustLayout:i?"header#iHeader.hd.super-hd + div#iScroll":"header#iHeader.hd + div#iScroll",modules:"gotop, subheader, partslist"});k.gotop();},jhnewsInit:function(){this.init({view:Gou.view,model:Gou.model,scrollBox:document,modules:"jhnews"});}});new f("mainPage");})(ICAT);