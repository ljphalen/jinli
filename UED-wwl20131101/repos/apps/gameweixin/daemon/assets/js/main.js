$(function(){window.ue;login();gift();editorInit();datePickerInit();imageText();keyword();appMsgDialog();menu();keywordTab();postLock=false;});$(window).resize(function(){waterfall();
});$(window).load(function(){waterfall();});function keywordTab(){if(!(".J_hash_container").length){return;}$(".J_hash_container  a ").on("click",function(b){$(this).addClass("active").parent("li").siblings("li").children("a").removeClass("active");
$($(this).attr("href")).removeClass("hidden").siblings(".J_hash_content").addClass("hidden");});var a=window.location.hash==""?$(".J_hash_container a[href='#J_hash_keyword']").attr("href"):window.location.hash;
if(a){$('.J_hash_container a[href$="'+a+'"]').trigger("click");}}var errorCallback=function(){};function isUndefined(a){return a===undefined;}function saveAction(b){var c=b.msg?b.msg:"保存失败";
if(b.success=="false"||!b.success){showGiftErrorMsg(c);return;}b=b.data;var a=parseInt(b.status,10);if(!a){showGiftErrorMsg(c);return;}else{showGiftErrorMsg("保存成功");
setTimeout(function(){if(!isUndefined(b.redirectUrl)){location.href=b.redirectUrl;}else{location.reload();}},1000);}}function login(){if($.cookie("rmbUser")=="true"){$("#chkRmb").attr("checked",true);
$("#txtUserName").val($.cookie("username"));$("#txtPwd").val($.cookie("password"));}var c=$("#login"),a=$("#confirm"),b=$(".J_dialog");c.click(function(){var i=$("#txtUserName").val().trim(),e=$("#txtPwd").val().trim(),g=c.attr("data-ajaxUrl");
if($("#chkRmb").is(":checked")){$.cookie("rmbUser","true",{expires:7});$.cookie("username",i,{expires:7});$.cookie("password",e,{expires:7});}else{$.cookie("rmbUser","false",{expire:-1});
$.cookie("username","",{expires:-1});$.cookie("password","",{expires:-1});}var h={userName:i,password:e,token:token};var d=function(){b.removeClass("hidden");
};var f=function(l){if(l.success=="false"||!l.success){return;}l=l.data;var j=parseInt(l.status,10);if(!j){b.removeClass("hidden");}else{location.href=l.redirectUrl;
}};postData(g,h,f,d);});a.click(function(){b.addClass("hidden");});}function gift(){var g=$("#operateCancel");var e=$(".J_dialog");var f=$(".J_table").attr("data-ajaxUrl")||"";
var d=$(".J_giftDel");g.click(function(i){e.addClass("hidden");});d.each(function(i,j){$(this).click(function(m){var o=$(this).attr("data-giftId");var l=$(this).attr("data-giftStatus");
var n=function(){var q={giftId:o,token:token};var p=function(r){e.addClass("hidden");setTimeout(function(){location.reload();},1000);};postData(f,q,p,p);
};if(l=="1"){e.removeClass("hidden");$("#gifDelConfirm").unbind("cilck").click(function(p){n();});}else{n();}});});var a=$("#selectStatus");if(a.val()=="0"){$(".J_giftEventDate").addClass("hidden");
}a.change(function(j){var i=$(".J_giftEventDate");if($(this).val()=="0"){i.addClass("hidden");}else{i.removeClass("hidden");}});var c=$("#startUploadCode");
c.click(function(m){var o="giftFile";var l=getFile(o);if(!l){showAlertMsg("请先选择一个文件");return;}if(l.type!="text/plain"){showGiftErrorMsg("文件格式错误，请上传txt格式的文件！");
return;}var n={token:token};var j=$(this).attr("data-ajaxUrl");var i=function(q,p){var s=q.msg?q.msg:"上传失败";if(q.success=="false"||!q.success){showAlertMsg(s);
return;}var r=q.data;if(r.status=="0"){showAlertMsg("上传失败");return;}$("#giftCodeName").html(r.name).attr("data-codeUrl",r.url).removeClass("hidden");showAlertMsg("上传成功");
};ajaxFileUpload(j,o,n,i);});var b=$("#giftSave");b.click(function(l){var m=getGiftPostData();var i=checkGiftDataValidate(m);if(i){if(postLock){return;
}postLock=true;var j=$(this).attr("data-ajaxUrl");postData(j,m,saveAction,errorCallback);}});var h=$("#giftPreview");h.click(function(j){var l=$("#giftName1").val();
$("#preview-giftName").html(l);$("#closePreviewDialog").siblings("span").html(l);var i=$("#exStartDate").val()+"-"+$("#exEndDate").val();$("#preview-giftExTime").html(i);
$("#preview-gift").html(ue.getContent());$(".J_dialog").removeClass("hidden");});$("#closePreviewDialog").click(function(i){$(".J_dialog").addClass("hidden");
});$("#codeType").change(function(){var i=$(this).children("option:selected").val();if(i==1){$("#codeUploadDiv").hide();$("#giftCodeDiv").show();}else{$("#giftCodeDiv").hide();
$("#codeUploadDiv").show();}});}function editorInit(){if(!$("#myEditor").length){return;}ue=UE.getEditor("myEditor");}function datePickerInit(){var a=$(".J_datePicker");
if(!a.length){return;}a.datetimepicker({dateFormat:"yy-mm-dd",timeFormat:"HH:mm:ss"});a.each(function(b,c){if($(this).hasClass("J_readonly")){$(this).attr("readonly","true");
}});}function imageText(){var f=$("#waterfall").attr("data-ajaxUrl");curImgText=null;$("body").on("click",".J_del_imageText",function(m){l();curImgText=$(this);
});$("#imageTextDelConfirm").click(function(o){var q=curImgText.attr("data-id");var p={id:q,token:token};var n=function(r){if(r.success=="false"||!r.success){showAlertMsg("删除失败");
return;}var s=r.data;if(s.status=="0"){showAlertMsg("删除失败");return;}showAlertMsg("删除成功");setTimeout(function(){location.reload();},1000);};var m=function(){};
$(".J_dialog").addClass("hidden");postData(f,p,n,m);});function l(){$(".J_dialog").height($("body").height());$(".J_dialog").removeClass("hidden");}var h=$("#imgTextAdd");
var b=$(".J_edit_list");h.click(function(m){b.removeClass("hidden");});$(".J_appmsg_address").find(".radio").change(function(m){changeAppMsgAddress();$("#"+a).attr("data-linkType",$(this).val());
});var i=$("#multi_appmsg_preview").length?"multi":"single";var a="appmsgItem1",e={};e.dataType=i;e.token=token;e.id=$("#"+a).parent("div.appmsg").attr("data-id");
e.data=[];setAppmsgHeight(a);showAppmsgData(a);var d=$("#js_add_appmsg");d.click(function(p){var m=$(".js_appmsg_item").length;if(m==2){$("#appmsgItem2").find(".js_del").removeClass("hidden");
}if(m>=8){showGiftErrorMsg("你最多可以加入8条图文消息");return;}var q=$(".js_appmsg_item").last().attr("id");var o=parseInt(q.charAt(q.length-1),10)+1;var n='<div id="appmsgItem'+o+'" data-id="" data-bagid="" data-giftName=""  data-title="" data-author="" data-imgSrc="" data-desc=""  data-linkType=""  data-link="" class="js_appmsg_item appmsg_item">	                <img src="" alt="" class="js_appmsg_thumb appmsg_thumb">	                <i class="appmsg_thumb default">封面图</i>	                <h4 class="J_appmsg_title appmsg_title"><a href="javascript:void(0);" target="_blank">标题</a></h4>	                <div class="appmsg_edit_mask">				        <a class="edit_gray js_edit"   href="javascript:void(0);"></a>				        <a class="del_gray js_del"   href="javascript:void(0);"></a>				    </div>	            </div>';
$(n).insertBefore($(this));a="appmsgItem"+o;setAppmsgHeight(a);showAppmsgData(a);});$("#multi_appmsg_preview").on("click",".js_edit",function(m){var n=$(this).parents(".js_appmsg_item").attr("id");
a=n;setAppmsgHeight(a);showAppmsgData(a);});$("#multi_appmsg_preview").on("click",".js_del",function(o){var n=$(this).parents(".js_appmsg_item");var m=n.next(".js_appmsg_item");
if(m.length){a=m.attr("id");}else{a="appmsgItem1";}n.remove();if($(".js_appmsg_item").length==2){$(".js_appmsg_item").find(".js_del").addClass("hidden");
}setAppmsgHeight(a);showAppmsgData(a);});$("#startUploadPic").click(function(q){var t="appMsgPic";var p=getFile(t);if(!p){showAlertMsg("必须插入一张图片");return;
}var s=p.type.split("/");var m=/^(png|jpg|jpeg)/i;if(s[0]!="image"||!(m.test(s[1]))){showAlertMsg("图片格式不正确，请重新上传！");return;}var r={token:token};var o=$(this).attr("data-ajaxUrl");
var n=function(v,u){if(v.success=="false"||!v.success){showAlertMsg("上传失败");return;}var w=v.data;if(w.status=="0"){showAlertMsg("上传失败");return;}$("#album").attr("src",attachroot+w.url).removeClass("hidden");
showAppMsgAlbum(a,attachroot+w.url);$("#"+a).attr("data-imgSrc",w.url);};ajaxFileUpload(o,t,r,n);});$("#imgTextTitle").keyup(function(m){var n=$(this).val();
showAppMsgTitle(a,n);$("#"+a).attr("data-title",n);});$("#imgTextAbstract").keyup(function(m){var n=$(this).val();showAppMsgAbstract(a,$(this).val());$("#"+a).attr("data-desc",n);
});$("#imgTextAuthor").keyup(function(m){$("#"+a).attr("data-author",$(this).val());});$("#linkAddress").blur(function(n){var m=parseInt(a.charAt(a.length-1),10);
$("#"+a).attr("data-link",$(this).val());$("#"+a).attr("data-linkType","link");});$("#imgTextSave").click(function(q){e.data=[];$(".js_appmsg_item").each(function(r,s){var u=$(this).attr("id");
var t=getAppmsgData(u);e.data.push(t);});console.log(e);var o=true;if(e.dataType=="single"){o=checkAppmsgValidate(e.data[0]);}else{for(var n=0,m=e.data.length;
n<m;n++){o=checkAppmsgValidate(e.data[n]);if(!o){a="appmsgItem"+(n+1);showAppmsgData(a);setAppmsgHeight(a);break;}}}if(o){if(postLock){return;}postLock=true;
var p=$(this).attr("data-ajaxUrl");postData(p,e,saveAction,errorCallback);}});var g=$("#giftAdd");g.click(function(m){$(".J_dialog").removeClass("hidden");
$("body").css("overflow","hidden");});var c=$("#chooseGift");c.click(function(o){var n=$(window.frames.gift_Iframe.document).find("input[name=giftRadio]:checked");
if(n.length){var q=n.attr("data-id");var m=n.attr("data-url");var p=n.parents("tr").children("td:eq(2)").html();$("#giftName").html(p).removeClass("hidden");
$("#"+a).attr("data-linkType","gift");$("#"+a).attr("data-link",m);$("#"+a).attr("data-bagId",q);$("#"+a).attr("data-giftName",p);$(".J_dialog").addClass("hidden");
$("body").css("overflow","auto");}});var j=$("#chooseGiftCancel");j.click(function(m){$(".J_dialog").addClass("hidden");$("body").css("overflow","auto");
});}function checkAppmsgValidate(b){if(b.title==""){showGiftErrorMsg("标题名称不能为空！");return false;}if(b.title.length>64){showGiftErrorMsg("标题名称最长不能超过64个字！");
return false;}if(b.author!=""&&b.author.length>8){showGiftErrorMsg("作者名称最长不超过8个字！");return false;}if(b.imgSrc==""){showGiftErrorMsg("必须插入一张图片");return false;
}if(b.desc&&b.desc!=""&&b.desc.length>120){showGiftErrorMsg("摘要最长不超过120个字！");return false;}if(b.linkType=="link"){var a=/^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?$/i;
if(!a.test(b.link)){showGiftErrorMsg("请输入合法的链接地址");return false;}}else{if(b.bagId==""){showGiftErrorMsg("请先选择一个礼包");return false;}}return true;}function setAppmsgHeight(b){if(b=="appmsgItem1"){$(".J_editing_area").css("marginTop","12px");
$(".arrow").css("top","74px");$("#resolution").html("900*500");}else{$("#resolution").html("200*200");var a=0;$("#"+b).prevAll(".js_appmsg_item").each(function(c,d){a+=$(this).outerHeight();
});$(".J_editing_area").css("marginTop",a+"px");$(".arrow").css("top",a+74);}}function getAppmsgData(c){var a=$("#"+c);var b={};b.id=a.attr("data-id");
b.bagId=a.attr("data-bagId");b.title=a.attr("data-title");b.author=a.attr("data-author");b.imgSrc=a.attr("data-imgSrc");b.desc=a.attr("data-desc");b.linkType=a.attr("data-linkType");
b.link=a.attr("data-link");b.giftName=a.attr("data-giftName");return b;}function showAppmsgData(b){var a=$("#"+b);$("#imgTextTitle").val(a.attr("data-title"));
$("#imgTextAuthor").val(a.attr("data-author"));if(a.attr("data-imgSrc")){$("#album").attr("src",attachroot+a.attr("data-imgSrc")).removeClass("hidden");
}else{$("#album").attr("src","").addClass("hidden");}$("#imgTextAbstract").val(a.attr("data-desc"));if(a.attr("data-linkType")=="link"){$("#giftType").removeAttr("checked");
$("#giftName").html(a.attr("data-giftName"));$("#linkType").prop("checked",true);$("#linkAddress").val(a.attr("data-link"));}else{if(a.attr("data-linkType")=="gift"){$("#linkType").removeAttr("checked");
$("#linkAddress").val("");$("#giftType").prop("checked",true);$("#giftName").html(a.attr("data-giftName"));}else{$("#linkType").removeAttr("checked");$("#linkAddress").val("");
$("#giftType").prop("checked",true);$("#giftName").html("");}}changeAppMsgAddress();}function showAppMsgAlbum(c,b){var a=$("#"+c).find(".js_appmsg_thumb");
a.attr("src",b).addClass("has_thumb");a.siblings("i").remove();}function showAppMsgTitle(c,b){var a=$("#"+c).find(".J_appmsg_title");b=b||"标题";a.children("a").html(b);
}function showAppMsgAbstract(c,b){var a=$("#"+c).find(".J_appmsg_desc");a.html(b);}function changeAppMsgAddress(){var a=$("input[name='address']:checked").val();
if(a=="gift"){$("#giftAdd").removeClass("hidden");$("#giftName").removeClass("hidden");$("#linkAddress").addClass("hidden");}else{$("#giftAdd").addClass("hidden");
$("#giftName").addClass("hidden");$("#linkAddress").removeClass("hidden");}}function postData(c,d,b,a){$.ajax({url:c,dataType:"json",type:"POST",data:d,timeout:10000,success:function(e){b(e);
setTimeout(function(){postLock=false;},1500);},error:function(){a();setTimeout(function(){postLock=false;},1500);}});}function getGiftPostData(){var a={};
a.token=token;a.id=getUrlParam("id")||"";a.type=a.id==""?"add":"edit";a.giftName=$("#giftName1").val().trim();a.codeType=$("#codeType option:selected").val();
a.giftInfo=ue.getContent();a.giftInfoLength=ue.getContentTxt().length;if(a.codeType==1){a.giftCode=$("#giftCode").val().trim();a.codeFileName="";}else{a.giftCode=$("#giftCodeName").attr("data-codeUrl");
a.codeFileName=$("#giftCodeName").html().trim();}a.exchangeStartDate=$("#exStartDate").val()?Date.parse($("#exStartDate").val())/1000:"";a.exchangeEndDate=$("#exEndDate").val()?Date.parse($("#exEndDate").val())/1000:"";
a.giftRate=$("#giftRate").val().trim();a.giftStatus=$("#selectStatus").val();a.eventStartDate=$("#eventStartDate").val()?Date.parse($("#eventStartDate").val())/1000:"";
a.eventEndDate=$("#eventEndDate").val()?Date.parse($("#eventEndDate").val())/1000:"";return a;}function checkGiftDataValidate(b){if(b.giftName==""){showGiftErrorMsg(msgCost.giftNameEmpty);
return false;}if(b.giftName.length>20){showGiftErrorMsg("礼包名称字数超过20字，请修改！");return false;}if(b.giftInfo==""){showGiftErrorMsg(msgCost.giftInfoEmpty);return false;
}if(b.giftInfoLength>600){showGiftErrorMsg("礼包信息字数超过600字，请修改！");return false;}if(!b.giftCode){var c=b.codeType==1?msgCost.giftCodeNull:msgCost.giftCodeEmpty;
showGiftErrorMsg(c);return false;}if(b.exchangeStartDate==""||b.exchangeEndDate==""){showGiftErrorMsg(msgCost.giftExDateEmpty);return false;}if(b.giftRate==""||!(b.giftRate<=1&&b.giftRate>0)){showGiftErrorMsg(msgCost.giftRateError);
return false;}var a=DecimalLength(b.giftRate);if(a>4){showGiftErrorMsg("概率设置不能超过4位小数，请修改！");return false;}if(b.giftStatus=="1"){if(b.eventStartDate==""||b.eventEndDate==""){showGiftErrorMsg(msgCost.giftEventDateEmpty);
return false;}}return true;}function DecimalLength(c){if(c!=null&&c!=""){var b=c.indexOf(".");if(b=="-1"){return 0;}else{var a=c.substring(b+1,c.length);
return a.length;}}return 0;}function showGiftErrorMsg(a){$(".J_gift_error_tips").html(a).removeClass("hidden").css("display","block");setTimeout(function(){$(".J_gift_error_tips").fadeOut("slow",function(){$(this).addClass("hidden");
});},1500);}function showAlertMsg(a){alert(a);}function getUrlParam(a){var b=new RegExp("(^|&)"+a+"=([^&]*)(&|$)");var c=window.location.search.substr(1).match(b);
if(c!=null){return unescape(c[2]);}return null;}function getFile(a){var c=$("#"+a)[0];var b=c.files[0];if(!c||!b){return null;}var d={};d.name=b.name;d.size=b.size;
d.type=b.type;return d;}var msgCost={giftNameEmpty:"礼包名称不能为空！",giftInfoEmpty:"礼包信息不能为空！",giftCodeEmpty:"请先上传礼包激活码！",giftCodeNull:"请先填写礼包激活码！",giftExDateEmpty:"兑换有效时间不能为空！",giftEventDateEmpty:"活动有效时间不能为空！",giftRateError:"请填写正确格式的中奖概率！"};
function ajaxFileUpload(c,a,e,d,b){$.ajaxFileUpload({url:c,secureuri:false,fileElementId:a,data:e,dataType:"json",success:function(g,f){d(g,f);},error:function(g,f,h){alert(h);
}});return false;}function waterfall(){if(!$("#waterfallContainer").length){return;}var a=$("#waterfall");var g=$(".appmsg");var m=g.outerWidth(true);var f=new Array();
var p=Math.floor($("#waterfallContainer").width()/m);for(var c=0;c<g.length;c++){colHeight=g.eq(c).outerHeight(true);if(c<p){f[c]=colHeight;g.eq(c).css("top",0);
g.eq(c).css("left",c*m);}else{minHeight=Math.min.apply(null,f);minCol=o(f,minHeight);f[minCol]+=colHeight;g.eq(c).css("top",minHeight);g.eq(c).css("left",minCol*m);
}g.eq(c).attr("id","post_"+c);}function o(q,i){for(k in q){if(q[k]==i){return k;}}}var h=parseInt(g.last().css("top"),10);var b=0;var j=[];var b;var l=g.length/p<1?parseInt(g.length/p,10)+1:parseInt(g.length/p,10);
var n=(l-1)*p;for(var c=n,e=g.length;c<e;c++){j.push($(g[c]).outerHeight(true));}b=Math.max.apply(Math,j);var d=h+b+"px";a.css({width:m*p,height:d});$("#waterfallContainer").removeClass("invisible");
}function keyword(){var h=$("#operateCancel");var d=$(".J_dialog");var f=$(".J_table").attr("data-ajaxUrl")||"";var c=$(".J_keywordDel");var e={};h.click(function(i){d.addClass("hidden");
});c.each(function(i,j){$(this).click(function(l){var n=$(this).attr("data-keywordId");var m=function(){var p={keywordId:n,token:token};var o=function(q){d.addClass("hidden");
setTimeout(function(){location.reload();},1000);};postData(f,p,o,o);};d.removeClass("hidden");$("#keywordDelConfirm").unbind("cilck").click(function(o){m();
});});});$("#chooseMsg").click(function(i){$(".J_dialog").removeClass("hidden");});var g=$("#keywordSave");var b=g.attr("data-ajaxUrl");g.click(function(i){e=getKeywordData();
if(checkKeywordDataVaildate(e)){if(postLock){return;}postLock=true;postData(b,e,saveAction,errorCallback);}});var a=$(".J_dialog");$(".J_close").click(function(i){a.addClass("hidden");
});$("#chooseAppMsg").click(function(j){var i=$(window.frames.appMsg_Iframe.document).find(".J_appmsg.selected");if(i.length){var m=i.attr("data-id");var l=i.attr("data-title");
$("#appMsgName").attr("data-id",m).attr("data-name",l).html("已选择："+l);$(".J_dialog").addClass("hidden");}});setTimeout(function(){changeKeywordType();},1);
$("input[name='event']").change(function(){changeKeywordType();});changeReplyType();$("[name=msgType]").change(function(i){changeReplyType();});$("#replySave").click(function(l){var n=$("[name=msgType]:checked").val();
var j=$(this).attr("data-ajaxUrl");var m={};if(n=="text"){var i=$("#textTypeArea").val();if(i==""){showGiftErrorMsg("文本消息不能为空");return;}m.replyContent=i;
m.imgTextId="";m.imgTextName="";}else{var o=$("#reply-appMsgName").attr("data-id");if(!o){showGiftErrorMsg("请先选择一条图文");return;}m.imgTextId=o;m.imgTextName=$("#reply-appMsgName").attr("data-name");
m.replyContent="";}m.token=token;m.replyType=n;if(postLock){return;}postLock=true;postData(j,m,saveAction,errorCallback);});$("#reply-chooseMsg").click(function(i){$(".J_appMsg_dialog").removeClass("hidden");
});$(".J_reply_close").click(function(i){$(".J_appMsg_dialog").addClass("hidden");});$("#reply_chooseAppMsg").click(function(j){var i=$(window.frames.appMsg_Iframe.document).find(".J_appmsg.selected");
if(i.length){var m=i.attr("data-id");var l=i.attr("data-title");$("#reply-appMsgName").attr("data-id",m).attr("data-name",l).html("已选择："+l);$(".J_appMsg_dialog").addClass("hidden");
}});}function changeReplyType(){var a=$("[name='msgType']:checked").val();if(a=="text"){$("#textTypeArea").removeClass("hidden");$(".J_reply_appMsg").addClass("hidden");
}else{$("#textTypeArea").addClass("hidden");$(".J_reply_appMsg").removeClass("hidden");}}function changeKeywordType(){var a=$("input[name='event']:checked").val();
if(a=="appMsg"){$(".J_appMsg").removeClass("hidden");$(".J_sysReply").addClass("hidden");$("#textTypeArea").addClass("hidden");}else{if(a=="text"){$("#textTypeArea").removeClass("hidden");
$(".J_appMsg").addClass("hidden");$(".J_sysReply").addClass("hidden");}else{if(a=="sysReply"){$("#textTypeArea").addClass("hidden");$(".J_appMsg").addClass("hidden");
$(".J_sysReply").removeClass("hidden");}}}}function checkKeywordDataVaildate(a){if(a.keyword==""){showGiftErrorMsg("关键词不能为空");return false;}if(a.keyword.trim().length>20){showGiftErrorMsg("关键字最长不超过20个字，请修改！");
return false;}if(a.eventType=="appMsg"&&!a.imgTextId){showGiftErrorMsg("请先选择一条图文");return false;}if(a.eventType=="text"&&a.replyContent==""){showGiftErrorMsg("请填写文本消息!");
return false;}return true;}function getKeywordData(){var a={};a.token=token;a.id=$("#keywordSave").attr("data-id");a.keyword=$("#keyword").val();a.type=$("[name=type]:checked").val();
a.eventType=$("[name=event]:checked").val();a.replyType=$("#reply option:selected").val();a.imgTextId="";a.imgTextName="";a.replyContent="";if(a.eventType=="appMsg"){a.imgTextId=$("#appMsgName").attr("data-id");
a.imgTextName=$("#appMsgName").attr("data-name");}else{if(a.eventType=="text"){a.replyContent=$("#textTypeArea").val().trim();}}return a;}function appMsgDialog(){if(!$(".appMsgDialog-body").length){return;
}$("#waterfall").delegate(".appmsg_mask","click",function(b){var a=$(this).parent("div.J_appmsg");a.addClass("selected");a.children("i.icon_card_selected").show();
var c=a.siblings("div.J_appmsg");c.removeClass("selected");c.children("i.icon_card_selected").hide();});}function menu(){var c=$("#menuSave");var b=$("#menuEdit");
var e=$("#addMainMenu");var h=c.attr("data-ajaxUrl");var d=getMainMenuStr();var a=getSubMenuStr();var f=getMainMenuKeywordStr();var g=$("#menuTable");b.click(function(i){elementEditStatus();
$(this).addClass("hidden");c.removeClass("hidden");e.removeClass("hidden");});c.click(function(o){var n=getMenuData();var m=true;window.mainMenuHash={};
for(var l=0,j=n.menu.length;l<j;l++){m=checkMenuDataValidate(n.menu[l],mainMenuHash);if(!m){break;}}if(m){if(postLock){return;}postLock=true;postData(h,n,saveAction,errorCallback);
}});e.click(function(j){var i=g.find(".J_mainItem").length;if(i>=3){showGiftErrorMsg("主菜单不能超过3个");return;}g.append(d);});$("#delMenucancel").click(function(i){$(".J_dialog").addClass("hidden");
});g.on("click",".J_mainMenuDel",function(l){var j=$(this);var i=function(){var n=j.parents("tr.J_mainItem");var m=n.nextUntil("tr.J_mainItem","tr.J_subItem");
n.remove();m.remove();};deleteMenuAction(i);});g.on("click",".J_subMenuDel",function(l){var j=$(this);var i=function(){var o=j.parents("tr.J_subItem");
var p=o.prevAll("tr.J_mainItem");var n=$(p[0]);o.remove();var m=n.nextUntil("tr.J_mainItem","tr.J_subItem");if(m.length==0){n.children("td.keyword-item").html(f);
}};deleteMenuAction(i);});g.on("click",".J_subMenuAdd",function(m){var j=$(this).parents("tr.J_mainItem");var i=j.nextUntil("tr.J_mainItem","tr.J_subItem");
if(i.length>=5){showGiftErrorMsg("子菜单不能超过5个");return;}var l=$(j.nextAll("tr.J_mainItem")[0]);if(l.length>0){l.before(a);}else{g.append(a);}j.children(".keyword-item").html("");
});g.find(".J_chooseKeyType").each(function(i,j){switchKeywordStatus($(this));});g.on("change",".J_chooseKeyType",function(i){switchKeywordStatus($(this));
});}function checkMenuDataValidate(e,a){var b=/^[1-9]$/;var j=/^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?$/i;
var c=/^0[1-9]$/;if(e.mainOrder==""){showGiftErrorMsg("主菜单次序不能为空！");return false;}if(!b.test(e.mainOrder)){showGiftErrorMsg("主菜单次序填写错误，请填写1~9的整数！");return false;
}if(a[e.mainOrder]){showGiftErrorMsg("主菜单次序不能重复！");return false;}a[e.mainOrder]=true;if(e.mainMenuName==""){showGiftErrorMsg("主菜单名称不能为空！");return false;
}if(e.mainMenuName.trim().length>4){showGiftErrorMsg("主菜单名称字数超过限制，最多填写4个字！");return false;}if(e.subMenu.length>0){var d={};for(var f=0,g=e.subMenu.length;
f<g;f++){var h=e.subMenu[f];if(h.subOrder==""){showGiftErrorMsg("子菜单次序不能为空！");return false;}if(!c.test(h.subOrder)){showGiftErrorMsg("子菜单次序填写错误，请填写01~09的整数！");
return false;}if(d[h.subOrder]){showGiftErrorMsg("子菜单次序不能重复！");return false;}d[h.subOrder]=true;if(h.subMenuName==""){showGiftErrorMsg("子菜单名称不能为空！");return false;
}if(h.subMenuName.trim().length>8){showGiftErrorMsg("子菜单名称字数超过限制，最多填写8个字！");return false;}if(h.subMenuDataType=="link"){if(h.subMenuKeywordLink==""){showGiftErrorMsg("链接地址不能为空！");
return false;}if(!j.test(h.subMenuKeywordLink)){showGiftErrorMsg("链接不合法，请修改！");return false;}}else{if(!h.subMenuKeywordId){showGiftErrorMsg("关键字名称不能为空！");
return false;}}}}else{if(e.mainMenuDataType=="link"){if(e.mainMenuKeywordLink==""){showGiftErrorMsg("链接地址不能为空！");return false;}if(!j.test(e.mainMenuKeywordLink)){showGiftErrorMsg("链接不合法，请修改！");
return false;}}else{if(!e.mainMenuKeywordId){showGiftErrorMsg("关键字名称不能为空！");return false;}}}return true;}function getMenuData(){var a={};a.token=token;
a.menu=[];$("#menuTable").find(".J_mainItem").each(function(d,f){var e={};e.subMenu=[];var c=$(this);var b=c.nextUntil("tr.J_mainItem","tr.J_subItem");
e.mainOrder=$.trim(c.find("input.J_mainOrder").val());e.mainMenuName=$.trim(c.find("input.J_mainKeyword").val());if(b.length){e.mainMenuDataType="";e.mainMenuKeywordId="";
e.mainMenuKeywordLink="";b.each(function(h,i){var g={};g.subOrder=$.trim($(this).find("input.J_subOrder").val());g.subMenuName=$.trim($(this).find("input.J_subKeyword").val());
g.subMenuDataType=$(this).find(".J_chooseKeyType option:selected").attr("data-type");g.subMenuKeywordId="";g.subMenuKeywordLink="";if(g.subMenuDataType=="link"){g.subMenuKeywordLink=$.trim($(this).find(".J_link").val());
}else{g.subMenuKeywordId=$(this).find(".J_keywordContainer select option:selected").attr("data-keywordId");}e.subMenu.push(g);});}else{e.mainMenuDataType=c.find(".J_chooseKeyType option:selected").attr("data-type");
e.mainMenuKeywordId="";e.mainMenuKeywordLink="";if(e.mainMenuDataType=="link"){e.mainMenuKeywordLink=$.trim(c.find(".J_link").val());}else{e.mainMenuKeywordId=c.find(".J_keywordContainer select option:selected").attr("data-keywordId");
}}a.menu.push(e);});return a;}function deleteMenuAction(a){$(".J_dialog").removeClass("hidden");$("#delMenuConfirm").unbind("click");$("#delMenuConfirm").bind("click",function(){a();
$(".J_dialog").addClass("hidden");});}function getkeyWordDropdownBox(){var a=$("#hiddenKeyWord").html();return a;}function getMainMenuStr(){var a=getkeyWordDropdownBox();
var b='<tr class="J_mainItem line">			<td class="order-menu">				<div class="J_mainOrder hidden"></div>				<input class="J_mainOrder order-input " type="text" />			</td>			<td class="main-menu">				<div class="J_mainKeyword hidden"></div>				<input class="J_mainKeyword keyword-input " type="text" />				<icon class="J_mainMenuDel icon-remove "></icon>			</td>			<td class="sub-menu">				<button class="J_subMenuAdd btn btn-menuAdd ">添加子菜单</button>			</td>			<td class="keyword-item">				<input  type="text" class="J_keywordVal hidden"  disabled value=""/>				<div class="J_keywordEditContainer ">					<select class="J_chooseKeyType">						<option   data-type="keyword">关键字</option>						<option   data-type="link">链接</option>					</select>'+a+'					<div class="J_linkContainer linkContainer hidden">						<input  type="text" class="J_link" value=""/>					</div>				</div>			</td>		</tr>';
return b;}function getSubMenuStr(){var a=getkeyWordDropdownBox();var b='<tr class="J_subItem">			<td class="order-menu">				<div class="J_subOrder hidden"></div>				<input class="J_subOrder order-input " type="text" />			</td>			<td class="main-menu">				<div class="cross-line"></div>			</td>			<td class="sub-menu">				<div data-menuId="1" class="J_subKeyword hidden"></div>				<input class="J_subKeyword keyword-input" type="text" />				<icon class="J_subMenuDel icon-remove "></icon>			</td>			<td class="keyword-item">				<input  type="text" class="J_keywordVal hidden"  disabled value=""/>				<div class="J_keywordEditContainer ">					<select class="J_chooseKeyType">						<option  data-type="keyword">关键字</option>						<option  data-type="link">链接</option>					</select>'+a+'					<div class="J_linkContainer linkContainer hidden">						<input  type="text" class="J_link" value=""/>					</div>				</div>			</td>		</tr>';
return b;}function getMainMenuKeywordStr(){var a=getkeyWordDropdownBox();var b='<input  type="text" class="J_keywordVal hidden"  disabled value=""/>					<div class="J_keywordEditContainer ">						<select class="J_chooseKeyType">							<option   data-type="keyword">关键字</option>							<option   data-type="link">链接</option>						</select>'+a+'						<div class="J_linkContainer linkContainer hidden">							<input  type="text" class="J_link" value=""/>						</div>					</div>';
return b;}function switchKeywordStatus(b){var a=b.find("option:selected").attr("data-type");if(a=="link"){b.siblings(".J_linkContainer").removeClass("hidden");
b.siblings(".J_keywordContainer").addClass("hidden");}else{if(a=="keyword"){b.siblings(".J_linkContainer").addClass("hidden");b.siblings(".J_keywordContainer").removeClass("hidden");
}}}function elementEditStatus(){$("div.J_mainOrder").each(function(a,c){var d=$(this).text();var b=$(this).siblings(".J_mainOrder");b.val(d).removeClass("hidden");
$(this).addClass("hidden");});$("div.J_subOrder").each(function(a,c){var d=$(this).text();var b=$(this).siblings(".J_subOrder");b.val(d).removeClass("hidden");
$(this).addClass("hidden");});$("div.J_mainKeyword").each(function(a,c){var d=$(this).text();var b=$(this).siblings(".J_mainKeyword");b.val(d).removeClass("hidden");
$(this).siblings(".J_mainMenuDel").removeClass("hidden");$(this).addClass("hidden");});$(".J_subMenuAdd").removeClass("hidden");$("div.J_subKeyword").each(function(a,c){var d=$(this).text();
var b=$(this).siblings(".J_subKeyword");b.val(d).removeClass("hidden");$(this).siblings(".J_subMenuDel").removeClass("hidden");$(this).addClass("hidden");
});$(".J_keywordVal").each(function(a,b){$(this).siblings(".J_keywordEditContainer").removeClass("hidden");$(this).addClass("hidden");});}