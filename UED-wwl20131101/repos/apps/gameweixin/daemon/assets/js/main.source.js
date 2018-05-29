
$(function() {
	window.ue;
	login();
	gift();
	editorInit();
	datePickerInit();
	imageText();
	keyword();
	appMsgDialog();
	menu();
	keywordTab();
	postLock=false;
});

$(window).resize(function() {
	waterfall();
});
$(window).load(function() {
	waterfall();
});

//自定义回复tab栏切换
function keywordTab(){
	if(!(".J_hash_container").length) return;

    $(".J_hash_container  a ").on('click', function (e) {
        $(this).addClass("active").parent('li').siblings('li').children('a').removeClass("active");
        $($(this).attr('href')).removeClass('hidden').siblings('.J_hash_content').addClass('hidden');
    });

    var hash = window.location.hash==''?$(".J_hash_container a[href='#J_hash_keyword']").attr('href'):window.location.hash;
    if (hash) $('.J_hash_container a[href$="'+hash+'"]').trigger('click');
}

var errorCallback = function() {
	//to do
};
function isUndefined(obj){
    return obj===undefined;
}
//ajax提交数据到服务器之后的回调——保存
function saveAction(data){
	var msg=data.msg?data.msg:'保存失败';
    if (data.success == 'false' || !data.success) {
        showGiftErrorMsg(msg);
		return;
    }
    data = data.data;
    var status = parseInt(data.status, 10);
    if (!status) {
        showGiftErrorMsg(msg);
        return;
    } else {
    	 showGiftErrorMsg('保存成功');
        setTimeout(function() {
            //保存成功，需要跳转就执行跳转，否则就刷新页面
            if(!isUndefined(data.redirectUrl)){
                location.href = data.redirectUrl;
            }else{
                location.reload();
            }
        }, 1000);
    }
}

//登录
function login() {
	//检查cookie信息
	if ($.cookie("rmbUser") == "true") {
		$("#chkRmb").attr("checked", true);
		$("#txtUserName").val($.cookie("username"));
		$("#txtPwd").val($.cookie("password"));
	}

	//登录事件绑定
	var loginBtn = $("#login"),
		okBtn = $("#confirm"),
		dialogMask = $(".J_dialog");
	loginBtn.click(function() {
		//dialogMask.removeClass('hidden');
		var username = $("#txtUserName").val().trim(),
			password = $("#txtPwd").val().trim(),
			ajaxUrl = loginBtn.attr('data-ajaxUrl');

		if ($('#chkRmb').is(":checked")) {
			$.cookie("rmbUser", "true", {
				expires: 7
			}); //存储一个带7天期限的cookie  
			$.cookie("username", username, {
				expires: 7
			});
			$.cookie("password", password, {
				expires: 7
			});
		} else {
			$.cookie("rmbUser", "false", {
				expire: -1
			});
			$.cookie("username", "", {
				expires: -1
			});
			$.cookie("password", "", {
				expires: -1
			});
		}

		//登录数据传递到服务端验证 及接收状态返回
		var data = {
			userName: username,
			password: password,
			token: token
		}
		var errorCallback = function() {
			dialogMask.removeClass('hidden');
		}
		var sucCallback = function(data) {
			if (data.success == 'false' || !data.success) {
				return;
			}
			data = data.data;
			var status = parseInt(data.status, 10);
			if (!status) {
				dialogMask.removeClass('hidden');
			} else {
				location.href = data.redirectUrl; //登录成功，重定向到需要跳转的页面
			}
		}

		postData(ajaxUrl, data, sucCallback, errorCallback);
	});

	okBtn.click(function() {
		dialogMask.addClass('hidden');
	})
}

//礼包
function gift() {
	var cancelBtn = $("#operateCancel");
	var dialogMask = $(".J_dialog");
	var ajaxUrl = $(".J_table").attr('data-ajaxUrl') || '';
	var delBtnList = $(".J_giftDel");

	cancelBtn.click(function(event) {
		dialogMask.addClass('hidden');
	});

	//删除礼包
	delBtnList.each(function(index, el) {
		$(this).click(function(event) {
			var id = $(this).attr('data-giftId');
			var status = $(this).attr('data-giftStatus');

			var giftDel = function() {
				var data = {
					giftId: id,
					token: token
				};
				var sucCallback = function(data) {
					dialogMask.addClass('hidden');
					setTimeout(function() {
						location.reload();
					}, 1000);
				}
				postData(ajaxUrl, data, sucCallback, sucCallback);
			}

			if (status == "1") { //有效
				dialogMask.removeClass('hidden');
				//确认删除事件绑定
				$("#gifDelConfirm").unbind('cilck').click(function(event) {
					giftDel();
				});
			} else { //无效，直接删除
				giftDel();
			}


		});
	});

	//活动是否有效
	var selStatus = $("#selectStatus");
	if (selStatus.val() == "0") {
		$(".J_giftEventDate").addClass('hidden');
	}
	selStatus.change(function(event) {
		var eventDate = $(".J_giftEventDate");
		if ($(this).val() == "0") {
			eventDate.addClass('hidden');
		} else {
			eventDate.removeClass('hidden');
		}
	});

	//激活码上传
	var startUpload = $("#startUploadCode");
	startUpload.click(function(event) {
		var id = 'giftFile';
		var fileObj = getFile(id);
		if (!fileObj) {
			showAlertMsg("请先选择一个文件");
			return;
		}
		if (fileObj.type != "text/plain") {
			showGiftErrorMsg('文件格式错误，请上传txt格式的文件！');
			return;
		}

		var data = {
			token: token
		};
		var ajaxUrl = $(this).attr('data-ajaxUrl');
		var sucCallback = function(data, status) {
			var msg=data.msg?data.msg:'上传失败';
			if (data.success == 'false' || !data.success) {
				showAlertMsg(msg);
				return;
			}

			var d = data.data;
			if (d.status == "0") {
				//上传成功
				showAlertMsg("上传失败");
				return;
			}

			$("#giftCodeName").html(d.name).attr("data-codeUrl", d.url).removeClass('hidden');
			showAlertMsg("上传成功");

		}
		ajaxFileUpload(ajaxUrl, id, data, sucCallback);
	});

	//提交礼包编辑数据
	var btnSave = $("#giftSave");
	btnSave.click(function(event) {
		

		var data = getGiftPostData();
		var isValidate = checkGiftDataValidate(data);
		if (isValidate) {
			if(postLock) return;
			postLock=true;
			var ajaxUrl = $(this).attr('data-ajaxUrl');
			postData(ajaxUrl, data, saveAction, errorCallback);
		}
	});

	//礼包预览
	var preview = $("#giftPreview");
	preview.click(function(event) {
		var giftName=$("#giftName1").val()
		$("#preview-giftName").html(giftName);
		$("#closePreviewDialog").siblings('span').html(giftName)
		var timePeriod = $("#exStartDate").val() + '-' + $("#exEndDate").val();
		$("#preview-giftExTime").html(timePeriod);
		$("#preview-gift").html(ue.getContent());
		$(".J_dialog").removeClass('hidden');

	});

	$("#closePreviewDialog").click(function(event) {
		$(".J_dialog").addClass('hidden');
	});

	//礼包类型切换
	$('#codeType').change(function(){
		var selectVar = $(this).children('option:selected').val();
		if(selectVar == 1) {
			$('#codeUploadDiv').hide();
			$('#giftCodeDiv').show();
		} else {
			$('#giftCodeDiv').hide();
			$('#codeUploadDiv').show();
		}
	});
}

//编辑器初始化
function editorInit() {
	if (!$("#myEditor").length) return;
	ue = UE.getEditor('myEditor');
}

//日期控件初始化
function datePickerInit() {
	var datePicker=$(".J_datePicker");
	if (!datePicker.length) return;

	datePicker.datetimepicker({
		dateFormat: "yy-mm-dd",
		timeFormat: 'HH:mm:ss'
	});
	datePicker.each(function(index, el) {
		if($(this).hasClass('J_readonly')){
			$(this).attr('readonly', 'true');
		}
	});
}

//素材管理
function imageText() {
	var ajaxUrl = $("#waterfall").attr('data-ajaxUrl');
	curImgText=null;

	$("body").on('click', '.J_del_imageText', function(event) {
		dialogShow();
		curImgText=$(this);
	});
	$("#imageTextDelConfirm").click(function(event) {//确认删除图文素材
		var id = curImgText.attr('data-id');
			var data = {
				id: id,
				token: token
			}
			var sucCallback = function(data) {
				if (data.success == 'false' || !data.success) {
					showAlertMsg("删除失败");
					return;
				}
				var d = data.data;
				if (d.status == "0") {
					showAlertMsg("删除失败");
					return;
				}

				showAlertMsg("删除成功");
				setTimeout(function() {
					location.reload();
				}, 1000);
			}

			var errorCallback = function() {
				// to do 
			};
			$(".J_dialog").addClass('hidden');
			postData(ajaxUrl, data, sucCallback, errorCallback);
	});

	function dialogShow(){
		$(".J_dialog").height($('body').height());
		$(".J_dialog").removeClass('hidden');
	}

	

	var imgTextAdd = $("#imgTextAdd"); //创建图文消息
	var editList = $(".J_edit_list");
	imgTextAdd.click(function(event) {
		editList.removeClass('hidden');
	});


	$(".J_appmsg_address").find('.radio').change(function(event) {
		changeAppMsgAddress();

		//更新数据
		$("#" + currentItemId).attr('data-linkType', $(this).val());
	});

	var appmsgType = $("#multi_appmsg_preview").length ? 'multi' : 'single'; //图文消息类型
	var currentItemId = "appmsgItem1",
		appmsgData = {};
	appmsgData.dataType = appmsgType;
	appmsgData.token = token;
	appmsgData.id = $("#" + currentItemId).parent("div.appmsg").attr('data-id');
	appmsgData.data = [];
	setAppmsgHeight(currentItemId);
	showAppmsgData(currentItemId);

	var appmsgAdd = $("#js_add_appmsg"); //多条图文素材添加与编辑的增加
	appmsgAdd.click(function(event) {
		var index = $(".js_appmsg_item").length;

		if(index==2){//只有2条的时候，新增一条会让第2条可以被删除了
			$("#appmsgItem2").find('.js_del').removeClass('hidden');
		}

		if (index >= 8) {
			showGiftErrorMsg('你最多可以加入8条图文消息');
			return;
		}
		var lastEleId=$(".js_appmsg_item").last().attr('id');
		var newIndex =parseInt(lastEleId.charAt(lastEleId.length-1),10) + 1;
		var html = '<div id="appmsgItem' + newIndex + '" data-id="" data-bagid="" data-giftName=""  data-title="" data-author="" data-imgSrc="" data-desc=""  data-linkType=""  data-link="" class="js_appmsg_item appmsg_item">\
	                <img src="" alt="" class="js_appmsg_thumb appmsg_thumb">\
	                <i class="appmsg_thumb default">封面图</i>\
	                <h4 class="J_appmsg_title appmsg_title"><a href="javascript:void(0);" target="_blank">标题</a></h4>\
	                <div class="appmsg_edit_mask">\
				        <a class="edit_gray js_edit"   href="javascript:void(0);"></a>\
				        <a class="del_gray js_del"   href="javascript:void(0);"></a>\
				    </div>\
	            </div>';
		$(html).insertBefore($(this));

		currentItemId = "appmsgItem" + newIndex;
		
		setAppmsgHeight(currentItemId);
		showAppmsgData(currentItemId);
	});

	//图文编辑
	$("#multi_appmsg_preview").on('click', '.js_edit', function(event) {
		var id = $(this).parents(".js_appmsg_item").attr('id');
		currentItemId = id;
		setAppmsgHeight(currentItemId);
		showAppmsgData(currentItemId);
	});

	//删除图文
	$("#multi_appmsg_preview").on('click', '.js_del', function(event) {
		var parentEle=$(this).parents(".js_appmsg_item")
		
		var nextToBeShowEle=parentEle.next('.js_appmsg_item');
		if(nextToBeShowEle.length){
			currentItemId=nextToBeShowEle.attr('id');
		}else{
			currentItemId='appmsgItem1';
		}
		parentEle.remove();
		if($(".js_appmsg_item").length==2){
			$(".js_appmsg_item").find('.js_del').addClass('hidden');
		}

		setAppmsgHeight(currentItemId);
		showAppmsgData(currentItemId);
	});


	//上传图片
	$("#startUploadPic").click(function(event) {
		var id = 'appMsgPic';
		var fileObj = getFile(id);
		if (!fileObj) {
			showAlertMsg("必须插入一张图片");
			return;
		}
		var imgInfo=fileObj.type.split("/");
		var fileExtReg=/^(png|jpg|jpeg)/i;
		if (imgInfo[0]!="image"||!(fileExtReg.test(imgInfo[1]))) {
			showAlertMsg("图片格式不正确，请重新上传！");
			return;
		}

		var data = {
			token: token
		};
		var ajaxUrl = $(this).attr('data-ajaxUrl');
		var sucCallback = function(data, status) {
			if (data.success == 'false' || !data.success) {
				showAlertMsg("上传失败");
				return;
			}

			var d = data.data;
			if (d.status == "0") {
				//上传成功
				showAlertMsg("上传失败");
				return;
			}

			$("#album").attr("src", attachroot + d.url).removeClass('hidden');
			showAppMsgAlbum(currentItemId, attachroot + d.url);

			//更新数据
			$("#" + currentItemId).attr('data-imgSrc',d.url);
			

		}
		ajaxFileUpload(ajaxUrl, id, data, sucCallback);
	});

	//显示标题
	$("#imgTextTitle").keyup(function(event) {
		var val = $(this).val();
		showAppMsgTitle(currentItemId, val);

		//更新数据
		$("#" + currentItemId).attr('data-title', val);
		
	});

	//显示摘要
	$("#imgTextAbstract").keyup(function(event) {
		var val = $(this).val();
		showAppMsgAbstract(currentItemId, $(this).val());

		//更新数据
		$("#" + currentItemId).attr('data-desc', val);
		
	});

	$("#imgTextAuthor").keyup(function(event) {
		//更新数据
		$("#" + currentItemId).attr('data-author', $(this).val());
		
	});

	$("#linkAddress").blur(function(event) {
		//更新数据
		var index = parseInt(currentItemId.charAt(currentItemId.length - 1), 10);

		$("#" + currentItemId).attr('data-link', $(this).val());
		$("#" + currentItemId).attr('data-linkType', 'link');
		
	});

	$("#imgTextSave").click(function(event) {
		appmsgData.data=[];
		$(".js_appmsg_item").each(function(index, el) {
			var id=$(this).attr('id');
			var data=getAppmsgData(id);
			appmsgData.data.push(data);
		});

		console.log(appmsgData);
		//验证数据合法
		var isValidate = true;
		if (appmsgData.dataType == 'single') {
			isValidate = checkAppmsgValidate(appmsgData.data[0]);
		} else {
			for (var i = 0, len = appmsgData.data.length; i < len; i++) {
				isValidate = checkAppmsgValidate(appmsgData.data[i]);
				if (!isValidate) {
					currentItemId = "appmsgItem" + (i + 1);
					showAppmsgData(currentItemId);
					setAppmsgHeight(currentItemId);
					break;
				}
			}
		}

		if (isValidate) {
			if(postLock) return;
			postLock=true;
			var ajaxUrl = $(this).attr('data-ajaxUrl');
			postData(ajaxUrl, appmsgData, saveAction, errorCallback);
		}
	});

	//选择礼包 与确定选择
	var giftAddBtn = $("#giftAdd");
	giftAddBtn.click(function(event) {
		// gift_Iframe.location.reload(); //iframe 重新加载
		$(".J_dialog").removeClass('hidden');
		$("body").css('overflow', 'hidden');
	});

	var giftAddConfirm = $("#chooseGift");
	giftAddConfirm.click(function(event) {
		var chkRadio = $(window.frames["gift_Iframe"].document).find("input[name=giftRadio]:checked");
		if (chkRadio.length) {
			var bagId = chkRadio.attr('data-id');
			var url = chkRadio.attr('data-url');
			var giftName = chkRadio.parents('tr').children('td:eq(2)').html();

			$("#giftName").html(giftName).removeClass('hidden');

			$("#" + currentItemId).attr('data-linkType', 'gift');
			$("#" + currentItemId).attr('data-link', url);
			$("#" + currentItemId).attr('data-bagId', bagId);
			$("#" + currentItemId).attr('data-giftName', giftName);

			
			$(".J_dialog").addClass('hidden');
			$("body").css('overflow', 'auto');
		}
	});

	var giftAddCancel = $("#chooseGiftCancel");
	giftAddCancel.click(function(event) {
		$(".J_dialog").addClass('hidden');
		$("body").css('overflow', 'auto');
	});
}



function checkAppmsgValidate(data) {
	if (data.title == "") {
		showGiftErrorMsg('标题名称不能为空！');
		return false;
	}

	if (data.title.length > 64) {
		showGiftErrorMsg('标题名称最长不能超过64个字！');
		return false;
	}

	if (data.author != '' && data.author.length > 8) {
		showGiftErrorMsg('作者名称最长不超过8个字！');
		return false;
	}

	if (data.imgSrc == '') {
		showGiftErrorMsg('必须插入一张图片');
		return false;
	}

	if (data.desc && data.desc != "" && data.desc.length > 120) {
		showGiftErrorMsg('摘要最长不超过120个字！');
		return false;
	}



	if (data.linkType == 'link') {
		var urlReg = /^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?$/i;
		if (!urlReg.test(data.link)) {
			showGiftErrorMsg('请输入合法的链接地址');
			return false;
		}
	} else {
		if (data.bagId == "") {
			showGiftErrorMsg('请先选择一个礼包');
			return false;
		}
	}

	return true;
}

function setAppmsgHeight(itemId) {
	if (itemId == 'appmsgItem1') {
		$(".J_editing_area").css("marginTop", "12px");
		$(".arrow").css("top", "74px");
		$("#resolution").html('900*500');

	} else {
		$("#resolution").html('200*200');
		var height = 0;
		$("#" + itemId).prevAll(".js_appmsg_item").each(function(index, el) {
			height += $(this).outerHeight();
		});
		$(".J_editing_area").css("marginTop", height + 'px');
		$(".arrow").css("top", height + 74);
	}
}

function getAppmsgData(itemId) {
	var item = $("#" + itemId);
	var data = {};
	data.id = item.attr('data-id');
	data.bagId = item.attr('data-bagId');
	data.title = item.attr('data-title');
	data.author = item.attr('data-author');
	data.imgSrc = item.attr('data-imgSrc');
	data.desc = item.attr('data-desc');
	data.linkType = item.attr('data-linkType');
	data.link = item.attr('data-link');
	data.giftName = item.attr('data-giftName');

	return data;
}

function showAppmsgData(itemId) { //初始化编辑区的数据

	var item = $("#" + itemId);

	$("#imgTextTitle").val(item.attr('data-title'));
	$("#imgTextAuthor").val(item.attr('data-author'));
	if (item.attr('data-imgSrc')) {
		$("#album").attr('src', attachroot + item.attr('data-imgSrc')).removeClass('hidden');
	} else {
		$("#album").attr('src', '').addClass('hidden');
	}
	$("#imgTextAbstract").val(item.attr('data-desc'));
	
	if (item.attr('data-linkType') == 'link') {
		$("#giftType").removeAttr('checked');
		$("#giftName").html(item.attr('data-giftName'));

		$("#linkType").prop('checked', true);
		$("#linkAddress").val(item.attr('data-link'));
	} else if (item.attr('data-linkType') == 'gift') {
		$("#linkType").removeAttr('checked');
		$("#linkAddress").val('');

		$("#giftType").prop('checked', true);
		$("#giftName").html(item.attr('data-giftName'));

	}else{
		$("#linkType").removeAttr('checked');
		$("#linkAddress").val('');

		$("#giftType").prop('checked', true);
		$("#giftName").html('');
	}

	changeAppMsgAddress();
}

//编辑图文素材-显示相册封面
function showAppMsgAlbum(itemId, src) {
	var thumb = $("#" + itemId).find('.js_appmsg_thumb');
	thumb.attr('src', src).addClass('has_thumb');
	thumb.siblings('i').remove();

}
	//编辑图文素材-显示标题
function showAppMsgTitle(itemId, title) {
	var titleEle = $("#" + itemId).find('.J_appmsg_title');
	title = title || '标题';
	titleEle.children('a').html(title);
}

//编辑图文素材-显示素材
function showAppMsgAbstract(itemId, title) {
	var abstractEle = $("#" + itemId).find('.J_appmsg_desc');
	abstractEle.html(title);

}

function changeAppMsgAddress() {
	var selectdVal = $("input[name='address']:checked").val();
	if (selectdVal == 'gift') {
		$("#giftAdd").removeClass('hidden');
		$("#giftName").removeClass('hidden');
		$("#linkAddress").addClass('hidden');
	} else {
		$("#giftAdd").addClass('hidden');
		$("#giftName").addClass('hidden');
		$("#linkAddress").removeClass('hidden');
	}
}


function postData(ajaxUrl, data, sucCallback, errorCallback) {
	$.ajax({
		url: ajaxUrl,
		dataType: 'json',
		type: "POST",
		data: data,
		timeout: 10000,
		success: function(data) {
			sucCallback(data);
			setTimeout(function(){
				postLock=false;
			},1500);
			
		},
		error: function() {
			errorCallback();
			setTimeout(function(){
				postLock=false;
			},1500);
		}
	})
}

function getGiftPostData() {
	var data = {};
	data.token = token;
	data.id = getUrlParam("id") || '';
	data.type = data.id == '' ? 'add' : 'edit';
	data.giftName = $("#giftName1").val().trim();
	data.codeType=$("#codeType option:selected").val();
	data.giftInfo = ue.getContent();
	data.giftInfoLength=ue.getContentTxt().length;
	if(data.codeType==1){
		data.giftCode = $("#giftCode").val().trim();
		data.codeFileName = '';
	}else{
		data.giftCode = $("#giftCodeName").attr('data-codeUrl');
		data.codeFileName = $("#giftCodeName").html().trim();
	}
	
	data.exchangeStartDate = $("#exStartDate").val() ? Date.parse($("#exStartDate").val()) / 1000 : '';
	data.exchangeEndDate = $("#exEndDate").val() ? Date.parse($("#exEndDate").val()) / 1000 : '';
	data.giftRate = $("#giftRate").val().trim();
	data.giftStatus = $("#selectStatus").val();
	data.eventStartDate = $("#eventStartDate").val() ? Date.parse($("#eventStartDate").val()) / 1000 : '';
	data.eventEndDate = $("#eventEndDate").val() ? Date.parse($("#eventEndDate").val()) / 1000 : '';
	// data.giftLink=$("#giftLink").val();

	return data;
}

function checkGiftDataValidate(data) {
	if (data.giftName == "") {
		showGiftErrorMsg(msgCost.giftNameEmpty);
		return false;
	}
	if(data.giftName.length>20){
		showGiftErrorMsg("礼包名称字数超过20字，请修改！");
		return false;
	}

	if (data.giftInfo == '') {
		showGiftErrorMsg(msgCost.giftInfoEmpty);
		return false;
	}
	if(data.giftInfoLength>600){
		showGiftErrorMsg('礼包信息字数超过600字，请修改！');
		return false;
	}

	if (!data.giftCode) {
		var msg=data.codeType==1?msgCost.giftCodeNull:msgCost.giftCodeEmpty;
		showGiftErrorMsg(msg);
		return false;
	}

	if (data.exchangeStartDate == '' || data.exchangeEndDate == '') {
		showGiftErrorMsg(msgCost.giftExDateEmpty);
		return false;
	}

	if (data.giftRate == '' || !(data.giftRate <= 1 && data.giftRate >0)) {
		showGiftErrorMsg(msgCost.giftRateError);
		return false;
	}
	var decimalLen=DecimalLength(data.giftRate);
	if(decimalLen>4){
		showGiftErrorMsg('概率设置不能超过4位小数，请修改！');
		return false;
	}

	if (data.giftStatus == "1") {
		if (data.eventStartDate == '' || data.eventEndDate == '') {
			showGiftErrorMsg(msgCost.giftEventDateEmpty);
			return false;
		}
	}

	return true;
}
function DecimalLength(value){  
     if(value!=null&&value!=''){   
         var decimalIndex=value.indexOf('.');   
         if(decimalIndex=='-1'){   
             return 0;   
         }else{   
             var decimalPart=value.substring(decimalIndex+1,value.length); 
             return decimalPart.length;   
         }   
     }   
     return 0;   
 }

function showGiftErrorMsg(msg) {
	$(".J_gift_error_tips").html(msg).removeClass('hidden').css('display', 'block');
	setTimeout(function() {
		$(".J_gift_error_tips").fadeOut('slow', function() {
			$(this).addClass('hidden');
		});
	}, 1500);
}

function showAlertMsg(msg) {
	//to do
	alert(msg);
}

function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg); //匹配目标参数
	if (r != null) return unescape(r[2]);
	return null; //返回参数值
}

function getFile(inputId) {
	var fileEle = $('#' + inputId)[0];
	var file = fileEle.files[0];
	if (!fileEle || !file) return null;
	var obj = {};
	obj.name = file.name;
	obj.size = file.size;
	obj.type = file.type;

	return obj;
}

var msgCost = {
	giftNameEmpty: '礼包名称不能为空！',
	giftInfoEmpty: '礼包信息不能为空！',
	giftCodeEmpty: '请先上传礼包激活码！',
	giftCodeNull: '请先填写礼包激活码！',
	giftExDateEmpty: '兑换有效时间不能为空！',
	giftEventDateEmpty: '活动有效时间不能为空！',
	giftRateError: '请填写正确格式的中奖概率！'
}


function ajaxFileUpload(url, fileElementId, data, sucCallback, errorCallback) {
	$.ajaxFileUpload({
		url: url,
		secureuri: false,
		fileElementId: fileElementId, //'giftFile',
		data: data,
		dataType: 'json',
		success: function(data, status) {
			sucCallback(data, status);

		},
		error: function(data, status, e) {
			alert(e);
		}
	})

	return false;
}


function waterfall() {
	if (!$("#waterfallContainer").length) return;

	var wfArea = $("#waterfall");
	var wfList = $(".appmsg");
	var wfWidth = wfList.outerWidth(true);
	var wfArr = new Array();
	var maxCol = Math.floor($("#waterfallContainer").width() / wfWidth);
	for (var i = 0; i < wfList.length; i++) {
		colHeight = wfList.eq(i).outerHeight(true);
		if (i < maxCol) { //如果i小于maxCol，那就说明是在一行里面（例如每行有4列，那就是4个为一组）
			wfArr[i] = colHeight;
			wfList.eq(i).css("top", 0);
			wfList.eq(i).css("left", i * wfWidth);
		} else {
			minHeight = Math.min.apply(null, wfArr);
			minCol = getArrayKey(wfArr, minHeight);
			wfArr[minCol] += colHeight;
			wfList.eq(i).css("top", minHeight);
			wfList.eq(i).css("left", minCol * wfWidth);
		}
		wfList.eq(i).attr('id', "post_" + i);
	};

	function getArrayKey(s, v) {
		for (k in s) {
			if (s[k] == v) {
				return k;
			}
		}
	};
	var fwLastLayerT = parseInt(wfList.last().css("top"), 10);
	var fwLastLayerH = 0;
	var heightArr = [];
	var fwLastLayerH;
	var cols = wfList.length / maxCol < 1 ? parseInt(wfList.length / maxCol, 10) + 1 : parseInt(wfList.length / maxCol, 10);
	var lastLineStartIndex = (cols - 1) * maxCol;
	for (var i = lastLineStartIndex, len = wfList.length; i < len; i++) {
		heightArr.push($(wfList[i]).outerHeight(true));
	}
	fwLastLayerH = Math.max.apply(Math, heightArr);
	var wfAreaH = fwLastLayerT + fwLastLayerH + "px";
	wfArea.css({
		"width": wfWidth * maxCol,
		"height": wfAreaH
	});

	$("#waterfallContainer").removeClass('invisible');
}


//消息管理
function keyword() {
	var cancelBtn = $("#operateCancel");
	var dialogMask = $(".J_dialog");
	var ajaxUrl = $(".J_table").attr('data-ajaxUrl') || '';
	var delBtnList = $(".J_keywordDel");
	var keywordData = {};

	cancelBtn.click(function(event) {
		dialogMask.addClass('hidden');
	});

	//删除关键字
	delBtnList.each(function(index, el) {
		$(this).click(function(event) {
			var id = $(this).attr('data-keywordId');

			var keywordDel = function() {
				var data = {
					keywordId: id,
					token: token
				};
				var sucCallback = function(data) {
					dialogMask.addClass('hidden');
					setTimeout(function() {
						location.reload();
					}, 1000);
				}
				postData(ajaxUrl, data, sucCallback, sucCallback);
			}

			dialogMask.removeClass('hidden');
			//确认删除事件绑定
			$("#keywordDelConfirm").unbind('cilck').click(function(event) {
				keywordDel();
			});
		});
	});

	$("#chooseMsg").click(function(event) {
		$(".J_dialog").removeClass('hidden');
	});

	//提交添加或编辑的数据
	var saveBtn = $("#keywordSave");
	var saveAjaxUrl = saveBtn.attr('data-ajaxUrl');
	saveBtn.click(function(event) {
		keywordData = getKeywordData();
		if (checkKeywordDataVaildate(keywordData)) {
			if(postLock) return;
			postLock=true;
			postData(saveAjaxUrl, keywordData, saveAction, errorCallback);
		}
	});


	var dialog = $(".J_dialog");
	$(".J_close").click(function(event) {
		dialog.addClass('hidden');
	});
	//选择图文
	$("#chooseAppMsg").click(function(event) {
		var selectedMsg = $(window.frames["appMsg_Iframe"].document).find(".J_appmsg.selected");
		if (selectedMsg.length) {
			var id = selectedMsg.attr('data-id');
			var title = selectedMsg.attr('data-title');


			$("#appMsgName").attr('data-id', id).attr('data-name', title).html('已选择：' + title);

			$(".J_dialog").addClass('hidden');
		}

	});

	setTimeout(function() {
		//此处有个小bug，需要研究下radio checked为何触发了多次，且最后一次取不到值
		changeKeywordType();
	}, 1);

	$("input[name='event']").change(function() {
		changeKeywordType();
	})


	changeReplyType();

	//被添加回复 消息类型切换
	$("[name=msgType]").change(function(event) {
		changeReplyType();

	});

	//被添加回复 保存
	$("#replySave").click(function(event) {
		var replyType = $("[name=msgType]:checked").val();
		var saveAjaxUrl = $(this).attr('data-ajaxUrl');
		var data = {};
		/*imgTextId	图文素材Id
		imgTextName*/
		if (replyType == 'text') {
			var replyContent = $("#textTypeArea").val();
			if (replyContent == '') {
				showGiftErrorMsg('文本消息不能为空');
				return;
			}
			data.replyContent = replyContent;
			data.imgTextId = '';
			data.imgTextName = '';
		} else {
			var imgTextId = $('#reply-appMsgName').attr('data-id');
			if (!imgTextId) {
				showGiftErrorMsg('请先选择一条图文');
				return;
			}
			data.imgTextId = imgTextId;
			data.imgTextName = $("#reply-appMsgName").attr('data-name');
			data.replyContent = '';
		}
		data.token = token;
		data.replyType = replyType;

		if(postLock) return;
		postLock=true;
		postData(saveAjaxUrl, data, saveAction, errorCallback);
	});

	//被添加回复-选择图文
	$("#reply-chooseMsg").click(function(event) {
		$(".J_appMsg_dialog").removeClass('hidden');
	});
	//被添加回复 选择素材
	$(".J_reply_close").click(function(event) {
		$(".J_appMsg_dialog").addClass('hidden');
	});
	//被添加回复 选择图文
	$("#reply_chooseAppMsg").click(function(event) {
		var selectedMsg = $(window.frames["appMsg_Iframe"].document).find(".J_appmsg.selected");
		if (selectedMsg.length) {
			var id = selectedMsg.attr('data-id');
			var title = selectedMsg.attr('data-title');


			$("#reply-appMsgName").attr('data-id', id).attr('data-name', title).html('已选择：' + title);

			$(".J_appMsg_dialog").addClass('hidden');
		}

	});
}

function changeReplyType() {
	var type = $("[name='msgType']:checked").val();
	if (type == 'text') {
		$("#textTypeArea").removeClass('hidden');
		$(".J_reply_appMsg").addClass('hidden');
	} else {
		$("#textTypeArea").addClass('hidden');
		$(".J_reply_appMsg").removeClass('hidden');
	}
}

function changeKeywordType() {
	var selectdVal = $("input[name='event']:checked").val();
	if (selectdVal == 'appMsg') {
		$(".J_appMsg").removeClass('hidden');
		$(".J_sysReply").addClass('hidden');
		$("#textTypeArea").addClass('hidden');
	} else if (selectdVal == 'text') {
		$("#textTypeArea").removeClass('hidden');
		$(".J_appMsg").addClass('hidden');
		$(".J_sysReply").addClass('hidden');
	} else if (selectdVal == 'sysReply') {
		$("#textTypeArea").addClass('hidden');
		$(".J_appMsg").addClass('hidden');
		$(".J_sysReply").removeClass('hidden');
	}
}

function checkKeywordDataVaildate(data) {
	if (data.keyword == '') {
		showGiftErrorMsg('关键词不能为空');
		return false;
	}
	if (data.keyword.trim().length > 20) {
		showGiftErrorMsg('关键字最长不超过20个字，请修改！');
		return false;
	}
	if (data.eventType == 'appMsg' && !data.imgTextId) {
		showGiftErrorMsg('请先选择一条图文');
		return false;
	}
	if (data.eventType == 'text' && data.replyContent == '') {
		showGiftErrorMsg('请填写文本消息!');
		return false;
	}

	return true;
}

function getKeywordData() {
	var data = {};
	data.token = token;
	data.id = $("#keywordSave").attr('data-id');
	data.keyword = $("#keyword").val();
	data.type = $("[name=type]:checked").val();
	data.eventType = $("[name=event]:checked").val();
	data.replyType = $("#reply option:selected").val();
	data.imgTextId = '';
	data.imgTextName = '';
	data.replyContent = '';

	if (data.eventType == 'appMsg') {
		data.imgTextId = $('#appMsgName').attr('data-id');
		data.imgTextName = $("#appMsgName").attr('data-name');
	} else if (data.eventType == 'text') {
		data.replyContent = $("#textTypeArea").val().trim();
	}

	return data;
}


function appMsgDialog() {
	if (!$(".appMsgDialog-body").length) return;

	$("#waterfall").delegate('.appmsg_mask', 'click', function(event) {
		var container = $(this).parent('div.J_appmsg');
		container.addClass('selected');
		container.children('i.icon_card_selected').show();

		var siblings = container.siblings('div.J_appmsg');
		siblings.removeClass('selected');
		siblings.children('i.icon_card_selected').hide();
	});

}


//菜单管理
function menu() {
	var btnSave = $("#menuSave");
	var btnEdit = $("#menuEdit");
	var btnAddMainMenu = $("#addMainMenu");
	var ajaxUrl = btnSave.attr('data-ajaxUrl');
	var mainMenuStr = getMainMenuStr();
	var subMenuStr = getSubMenuStr();
	var mainMenuKeywordStr = getMainMenuKeywordStr();
	var table = $("#menuTable");


	//编辑
	btnEdit.click(function(event) {
		elementEditStatus();
		$(this).addClass('hidden');
		btnSave.removeClass('hidden');
		btnAddMainMenu.removeClass('hidden');
	});

	//保存
	btnSave.click(function(event) {
		var menuData = getMenuData();
		//检查数据合法性
		var isValidate = true;
		window.mainMenuHash={};
		for (var i = 0, len = menuData.menu.length; i < len; i++) {
			isValidate = checkMenuDataValidate(menuData.menu[i],mainMenuHash);
			if (!isValidate) {
				break;
			}
		}

		if (isValidate) {
			if(postLock) return;
			postLock=true;
			postData(ajaxUrl, menuData, saveAction, errorCallback);
		}
	});

	//添加主菜单
	btnAddMainMenu.click(function(event) {
		//主菜单不能超过3
		var mainMenuLen = table.find('.J_mainItem').length;
		if (mainMenuLen >= 3) {
			showGiftErrorMsg('主菜单不能超过3个');
			return;
		};

		table.append(mainMenuStr);
	});

	//取消删除菜单
	$("#delMenucancel").click(function(event) {
		$(".J_dialog").addClass('hidden');
	});

	//删除主菜单
	table.on('click', '.J_mainMenuDel', function(event) {
		var that = $(this);
		var delAction = function() {
			var currentMainMenu = that.parents('tr.J_mainItem');
			var subMenuList = currentMainMenu.nextUntil('tr.J_mainItem', 'tr.J_subItem');
			currentMainMenu.remove();
			subMenuList.remove();
		}
		deleteMenuAction(delAction);
	});

	//删除子菜单
	table.on('click', '.J_subMenuDel', function(event) {
		var that = $(this);
		var delAction = function() {
			var currentSubMenu = that.parents('tr.J_subItem');
			var prevMainMenuList = currentSubMenu.prevAll('tr.J_mainItem');
			var currentMainMenu = $(prevMainMenuList[0]);

			currentSubMenu.remove();
			var subMenuList = currentMainMenu.nextUntil('tr.J_mainItem', 'tr.J_subItem');
			//删除子菜单后，如果当前主菜单已没有对应的子菜单了，则该主菜单会需要配置关键词或链接
			if (subMenuList.length == 0) {
				currentMainMenu.children('td.keyword-item').html(mainMenuKeywordStr);
			}
		}
		deleteMenuAction(delAction);

	});

	/*添加子菜单*/
	table.on('click', '.J_subMenuAdd', function(event) {
		var currentMainMenu = $(this).parents('tr.J_mainItem');
		//子菜单不能超过5个
		var subMenuList = currentMainMenu.nextUntil('tr.J_mainItem', 'tr.J_subItem');
		if (subMenuList.length >= 5) {
			showGiftErrorMsg('子菜单不能超过5个');
			return;
		}

		var nextSilb = $(currentMainMenu.nextAll('tr.J_mainItem')[0]);
		if (nextSilb.length > 0) {
			nextSilb.before(subMenuStr);
		} else {
			table.append(subMenuStr);
		}

		currentMainMenu.children('.keyword-item').html('');
	});

	//关键词类型切换、初始化
	table.find(".J_chooseKeyType").each(function(index, el) {
		switchKeywordStatus($(this));
	});
	table.on('change', '.J_chooseKeyType', function(event) {
		switchKeywordStatus($(this));
	});
}

function checkMenuDataValidate(data,mainMenuHash) {
	var mainMenuOrderReg = /^[1-9]$/;
	var urlReg = /^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?$/i;
	var subMenuOrderReg = /^0[1-9]$/;

	if (data.mainOrder == '') {
		showGiftErrorMsg('主菜单次序不能为空！');
		return false;
	}

	if (!mainMenuOrderReg.test(data.mainOrder)) {
		showGiftErrorMsg('主菜单次序填写错误，请填写1~9的整数！');
		return false;
	}

	if(mainMenuHash[data.mainOrder]){
		showGiftErrorMsg('主菜单次序不能重复！');
		return false;
	}
	mainMenuHash[data.mainOrder]=true;


	if (data.mainMenuName == '') {
		showGiftErrorMsg('主菜单名称不能为空！');
		return false;
	}
	if (data.mainMenuName.trim().length > 4) {
		showGiftErrorMsg('主菜单名称字数超过限制，最多填写4个字！');
		return false;
	}

	if (data.subMenu.length > 0) {
		var hash={};
		for (var i = 0, len = data.subMenu.length; i < len; i++) {
			var subMenuData = data.subMenu[i];

			if (subMenuData.subOrder == '') {
				showGiftErrorMsg('子菜单次序不能为空！');
				return false;
			}

			if (!subMenuOrderReg.test(subMenuData.subOrder)) {
				showGiftErrorMsg('子菜单次序填写错误，请填写01~09的整数！');
				return false;
			}
			//判断子菜单是否有重复
			if(hash[subMenuData.subOrder]){
				showGiftErrorMsg('子菜单次序不能重复！');
				return false;
			}
			hash[subMenuData.subOrder]=true;

			if (subMenuData.subMenuName == '') {
				showGiftErrorMsg('子菜单名称不能为空！');
				return false;
			}
			if (subMenuData.subMenuName.trim().length > 8) {
				showGiftErrorMsg('子菜单名称字数超过限制，最多填写8个字！');
				return false;
			}
			if (subMenuData.subMenuDataType == 'link') {
				if (subMenuData.subMenuKeywordLink == '') {
					showGiftErrorMsg('链接地址不能为空！');
					return false;
				}
				if (!urlReg.test(subMenuData.subMenuKeywordLink)) {
					showGiftErrorMsg('链接不合法，请修改！');
					return false;
				}
			} else {
				if (!subMenuData.subMenuKeywordId) {
					showGiftErrorMsg('关键字名称不能为空！');
					return false;
				}
			}
		}

	} else { //不含子菜单
		if (data.mainMenuDataType == 'link') {
			if (data.mainMenuKeywordLink == '') {
				showGiftErrorMsg('链接地址不能为空！');
				return false;
			}
			if (!urlReg.test(data.mainMenuKeywordLink)) {
				showGiftErrorMsg('链接不合法，请修改！');
				return false;
			}
		} else {
			if (!data.mainMenuKeywordId) {
				showGiftErrorMsg('关键字名称不能为空！');
				return false;
			}
		}
	}

	return true;
}

function getMenuData() {
	var data = {};
	data.token = token;
	data.menu = [];
	$("#menuTable").find('.J_mainItem').each(function(index, el) {
		var menuItem = {};
		menuItem.subMenu = [];
		var currentMainMenu = $(this);
		var subMenuList = currentMainMenu.nextUntil('tr.J_mainItem', 'tr.J_subItem');

		menuItem.mainOrder = $.trim(currentMainMenu.find('input.J_mainOrder').val());
		menuItem.mainMenuName = $.trim(currentMainMenu.find('input.J_mainKeyword').val());
		if (subMenuList.length) {
			menuItem.mainMenuDataType = ''
			menuItem.mainMenuKeywordId = '';
			menuItem.mainMenuKeywordLink = '';

			subMenuList.each(function(index, el) {
				var subMenuItem = {};
				subMenuItem.subOrder = $.trim($(this).find('input.J_subOrder').val());
				subMenuItem.subMenuName = $.trim($(this).find('input.J_subKeyword').val());
				subMenuItem.subMenuDataType = $(this).find('.J_chooseKeyType option:selected').attr('data-type');
				subMenuItem.subMenuKeywordId = '';
				subMenuItem.subMenuKeywordLink = '';
				if (subMenuItem.subMenuDataType == 'link') {
					subMenuItem.subMenuKeywordLink = $.trim($(this).find('.J_link').val());
				} else {
					subMenuItem.subMenuKeywordId = $(this).find('.J_keywordContainer select option:selected').attr('data-keywordId');
				}

				menuItem.subMenu.push(subMenuItem);
			});


		} else { //无子菜单
			menuItem.mainMenuDataType = currentMainMenu.find('.J_chooseKeyType option:selected').attr('data-type');
			menuItem.mainMenuKeywordId = '';
			menuItem.mainMenuKeywordLink = '';
			if (menuItem.mainMenuDataType == 'link') {
				menuItem.mainMenuKeywordLink = $.trim(currentMainMenu.find('.J_link').val());
			} else {
				menuItem.mainMenuKeywordId = currentMainMenu.find('.J_keywordContainer select option:selected').attr('data-keywordId');
			}
		}

		data.menu.push(menuItem);
	});

	return data;
}

function deleteMenuAction(callback) {
	$(".J_dialog").removeClass('hidden');
	$("#delMenuConfirm").unbind('click');
	$("#delMenuConfirm").bind('click', function() {
		callback();

		$(".J_dialog").addClass('hidden');
	});
}


function getkeyWordDropdownBox() {
	var htmlStr = $("#hiddenKeyWord").html();
	return htmlStr;
}

function getMainMenuStr() {
	var keywordStr = getkeyWordDropdownBox();
	var htmlStr = '<tr class="J_mainItem line">\
			<td class="order-menu">\
				<div class="J_mainOrder hidden"></div>\
				<input class="J_mainOrder order-input " type="text" />\
			</td>\
			<td class="main-menu">\
				<div class="J_mainKeyword hidden"></div>\
				<input class="J_mainKeyword keyword-input " type="text" />\
				<icon class="J_mainMenuDel icon-remove "></icon>\
			</td>\
			<td class="sub-menu">\
				<button class="J_subMenuAdd btn btn-menuAdd ">添加子菜单</button>\
			</td>\
			<td class="keyword-item">\
				<input  type="text" class="J_keywordVal hidden"  disabled value=""/>\
				<div class="J_keywordEditContainer ">\
					<select class="J_chooseKeyType">\
						<option   data-type="keyword">关键字</option>\
						<option   data-type="link">链接</option>\
					</select>' + keywordStr + '\
					<div class="J_linkContainer linkContainer hidden">\
						<input  type="text" class="J_link" value=""/>\
					</div>\
				</div>\
			</td>\
		</tr>';
	return htmlStr;
}

function getSubMenuStr() {
	var keywordStr = getkeyWordDropdownBox();
	var htmlStr = '<tr class="J_subItem">\
			<td class="order-menu">\
				<div class="J_subOrder hidden"></div>\
				<input class="J_subOrder order-input " type="text" />\
			</td>\
			<td class="main-menu">\
				<div class="cross-line"></div>\
			</td>\
			<td class="sub-menu">\
				<div data-menuId="1" class="J_subKeyword hidden"></div>\
				<input class="J_subKeyword keyword-input" type="text" />\
				<icon class="J_subMenuDel icon-remove "></icon>\
			</td>\
			<td class="keyword-item">\
				<input  type="text" class="J_keywordVal hidden"  disabled value=""/>\
				<div class="J_keywordEditContainer ">\
					<select class="J_chooseKeyType">\
						<option  data-type="keyword">关键字</option>\
						<option  data-type="link">链接</option>\
					</select>' + keywordStr + '\
					<div class="J_linkContainer linkContainer hidden">\
						<input  type="text" class="J_link" value=""/>\
					</div>\
				</div>\
			</td>\
		</tr>';

	return htmlStr;
}

function getMainMenuKeywordStr() {
	var keywordStr = getkeyWordDropdownBox();
	var htmlStr = '<input  type="text" class="J_keywordVal hidden"  disabled value=""/>\
					<div class="J_keywordEditContainer ">\
						<select class="J_chooseKeyType">\
							<option   data-type="keyword">关键字</option>\
							<option   data-type="link">链接</option>\
						</select>' + keywordStr + '\
						<div class="J_linkContainer linkContainer hidden">\
							<input  type="text" class="J_link" value=""/>\
						</div>\
					</div>';
	return htmlStr;

}

function switchKeywordStatus($ele) {
	var dataType = $ele.find('option:selected').attr('data-type');
	if (dataType == 'link') {
		$ele.siblings('.J_linkContainer').removeClass('hidden');
		$ele.siblings('.J_keywordContainer').addClass('hidden');
	} else if (dataType == 'keyword') {
		$ele.siblings('.J_linkContainer').addClass('hidden');
		$ele.siblings('.J_keywordContainer').removeClass('hidden');
	}
}

function elementEditStatus() {
	//主次序
	$("div.J_mainOrder").each(function(index, el) {
		var orderText = $(this).text();
		var siblingOrder = $(this).siblings('.J_mainOrder');
		siblingOrder.val(orderText).removeClass('hidden');
		$(this).addClass('hidden');
	});
	//子次序
	$("div.J_subOrder").each(function(index, el) {
		var orderText = $(this).text();
		var siblingOrder = $(this).siblings('.J_subOrder');
		siblingOrder.val(orderText).removeClass('hidden');
		$(this).addClass('hidden');
	});

	//主菜单
	$("div.J_mainKeyword").each(function(index, el) {
		var orderText = $(this).text();
		var siblingOrder = $(this).siblings('.J_mainKeyword');
		siblingOrder.val(orderText).removeClass('hidden');
		$(this).siblings('.J_mainMenuDel').removeClass('hidden');
		$(this).addClass('hidden');
	});

	//子菜单
	$(".J_subMenuAdd").removeClass('hidden'); //添加子菜单按钮显示
	$("div.J_subKeyword").each(function(index, el) {
		var orderText = $(this).text();
		var siblingOrder = $(this).siblings('.J_subKeyword');
		siblingOrder.val(orderText).removeClass('hidden');
		$(this).siblings('.J_subMenuDel').removeClass('hidden');
		$(this).addClass('hidden');
	});

	//关键词或链接
	$(".J_keywordVal").each(function(index, el) {
		$(this).siblings('.J_keywordEditContainer').removeClass('hidden');
		$(this).addClass('hidden');
	});
}