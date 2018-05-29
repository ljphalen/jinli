/**
 * 主题 各页面的业务逻辑，和UI分开来写
var处变量=对应的html文件内的id名,表示属于该页面下的各种业务逻辑
*/

var zt_usr_02={
	funName:"zt_usr_02",
	/*上传*/
	initImgUpload:function(){
		var url='plugins/jqfile/server/php/index.php';

		$('#fileLoader').fileupload({
			dropZone:$("#cell_drag1"),//拖拽上传区域
	        url: url,
	        dataType: 'json',
	        previewMaxWidth: 160,//预览图片宽
	        previewMaxHeight: 270,//预览图片高
	        previewCrop: true,
	        autoUpload:false//是否开启自动上传
	    }).on('fileuploadprocessalways', function (e, data) {
	    	$.each(data.files, function (index, file) {

	       		$(file.preview).appendTo("#cell_img_canvas1");//显示预览图

	    	});
	   
		});
	},

	initApprove2:function(){
		$("#zt_user02Approve").find("li").on("click",function(){
			var res=$(this).text();
			if(res!="通过"){
				$("#zt_user02Editor").removeClass("none");
			}
			else{
				$("#zt_user02Editor").addClass("none");
			}
		});
	}

};

var zt_admin_edit={
	funName:"zt_admin_edit",

	/*上传
	initImgUpload:function(){
		var url='plugins/jqfile/server/php/index.php';

		$('#fileLoader').fileupload({
			dropZone:$("#cell_drag1"),//拖拽上传区域
	        url: url,
	        dataType: 'json',
	        previewMaxWidth: 160,//预览图片宽
	        previewMaxHeight: 270,//预览图片高
	        previewCrop: true,
	        autoUpload:false//是否开启自动上传
	    }).on('fileuploadprocessalways', function (e, data) {
	    	
	    	$.each(data.files, function (index, file) {

	       		$(file.preview).appendTo("#cell_img_canvas1");//显示预览图

	    	});
	   
		});
	},*/

	initApprove:function(){
		$("#zt_editApprove").find("li").on("click",function(){
			var res=$(this).text();
			if(res!="通过"){
				$("#zt_editEditor").removeClass("none");
			}
			else{
				$("#zt_editEditor").addClass("none");
			}
		});
	}
};

var zt_usr_upload02={
	funName:"zt_usr_upload02",
	initPopViewList:function(){
		$("#upload02ViewList").on("click",function(){
			//window.location.href="/Admin/File/index";
		});

	},
	initPopUploadMore:function(){
		$("#upload02UploadMore").on("click",function(){
			window.location.href="zt_usr_upload01.html";
		});
	},

};

var zt_operation_03={
    funName:"zt_operation_03",
    pageCount: 1,
    recordCount: 1,
    //显示模式切换		
    initViewModeSelect: function () {
        $(".btn_switch").on("click", function () {
            //选中的模式保持批量管理功能
            var openId = $(this).attr("openId");
            $("#" + openId).addClass("admin_mode");
            $(".btn_check").removeClass("on");
        });
    },
    //初始化获取第一页的数据
    initAjaxDataFetch: function () {
       
        this.dataFetch(1,0,null);
         console.log(zt_operation_03.pageCount);
    },
   initPagerDataFetch: function () {
       
       console.log(this.pageCount);
           $("#pager").paginate({
            count: this.pageCount, //有多少页
            start: 1, //起始页面
            display: 10, //显示多少个分页数字
            images: false,
            mouse: 'press',
            onChange: function (page) {
              var tid = $(".tag_one").find(".on").attr("tid");
              var search =$(".search_name").val();
              zt_operation_03.dataFetch(page,tid,search);
            }
        });
        //输入页面号码 点击确定按钮去到对应页面 原控件不带此功能 需自己写
        $("#pageOk").click(function () {
            var goPageNum = $("#goPageNum").val() - 1;
            $("#pager .jPag-pages li:eq(" + goPageNum + ")").trigger("click");
        });
    },
        dataFetch: function (pageNum,tid,search) {
        //ajax 请求数据
        $.ajax({
            url: "/Admin/Fileadmin/getTheme",
            type: "post",
            async: false,
            dataType: "json",
            data: {"pageNum": pageNum,"token":token,"tid":tid,"search":search},
            success: function (data) {
                $("#viewTbody").find("tr").empty();//清空原有数据
                //储存分页汇总数据
                zt_operation_03.pageCount = data.pageCount;
                zt_operation_03.recordCount = data.recordCount;
                //遍历后台json数据 不同页面此处的内容很可能不一致 根据具体业务来
                if (data.datas.length > 0) {
     
                    $(data.datas).each(function (idx, ele) {
                        //获取数据格式，去掉隐藏class
                        var copyObj = $("#demoTr").clone();
                        copyObj.removeAttr("class");
                        copyObj.removeAttr("id");
                        copyObj.find(".btn_check").attr("data-img-src", ele.url);
                        copyObj.find(".btn_check").attr("data-img-name", ele.picName);
                        copyObj.find(".btn_check").attr("mid", ele.id);
                        copyObj.find("img").attr("src", ele.url);
                        
                        copyObj.find("td:eq(1)").text(ele.picName);
                        copyObj.find("td:eq(2)").text(ele.tagOne);
                        copyObj.find("td:eq(3)").text(ele.tagTwo);
                        copyObj.find("td:eq(4)").text(ele.uploadDate);
                        copyObj.find("td:eq(5)").text(ele.resolution);
                        copyObj.find("td:eq(6)").text(ele.times);
                        copyObj.find("td:eq(7)").text(ele.status);
                        //渲染新数据至页面
                        $(copyObj).appendTo($("#viewTbody"));
                    });
                }
                //在页面更新分页汇总数据
                $("#recordCount").text(zt_operation_03.recordCount);
                $("#pageCount").text(zt_operation_03.pageCount);

            },
             error: function () {
                //TODO 错误的处理
                alert("AJAX请求出错！请修改webgBusiness_bz.js->zt_operation_03");
            }
        });

    },
        //下一步;
	initOperationBtnAddNext:function(){
		$("#ztSeminarAddNext").on("click",function(){
			//window.location.href="/Admin/subject/addtwo";
		});
	},
        
         //TODO web本地存储 清空待选
    initClearImg: function () {
        webUI.delData("PICKED_IMG");
    },
    
     initImgAdd: function () {
        $("#pack01Add").on("click", function () {
            var tempImgArr = '';
            var checkedData = $(".check_data").filter(".on");
            var checkedDataLength = checkedData.length;
            if (checkedDataLength > 0) {
                checkedData.each(function (idx, ele) {
                    if (idx < checkedDataLength - 1) {
                        tempImgArr += $(this).attr("data-img-src") + "*" + $(this).attr("data-img-name") + "*" + $(this).attr("mid")+"#";
                    }
                    else {
                        tempImgArr += $(this).attr("data-img-src") + "*" + $(this).attr("data-img-name")+"*" + $(this).attr("mid");
                    }
                    $(this).remove();
                });
                //获取之前已选好的壁纸数据
                var imgData = webUI.getData("PICKED_IMG");
                if (imgData != null) {
                    tempImgArr = tempImgArr + "#" + imgData;
                }
                webUI.setData("PICKED_IMG", tempImgArr);
                //更新添加按钮上的 已选壁纸数量
                $(".icon_num").text(webUI.getData("PICKED_IMG").split("#").length);
            }
            else {
                alert("请选择主题");
            }
        });
    },
    //验证 web本地存储中的 PICKED_IMG 是否符合规则
    checkPickedImg: function () {

    }
};

var zt_operation_04={
	funName:"zt_operation_04",
        
    
	initOperationBtnAddNext:function(){
            
		$("#ztSeminarAddNext").on("click",function(){
			window.location.href="zt_operation_07.html";
		});
	}
};

var zt_operation_05={
	funName:"zt_operation_05",
            initImgData: function () {
          
         
        var imgData = webUI.getData("PICKED_IMG");
        
        if (imgData != null) {
            var dataRecord = imgData.split("#");
            //console.log(dataRecord);
            $(dataRecord).each(function (idx, ele) {
                var recordDeatil = ele.split("*");
                var str = "<li role='txtPack' targetImg=" + recordDeatil[2] + " data-url='" + recordDeatil[0] + "'>" + recordDeatil[1] + "</li>";
                $(str).appendTo($("#dragSortTxtPack"));
                //var obj=$("<li role='txtPack'/>").text(recordDeatil[1]).attr("data-url",recordDeatil[0]);
                //obj.appendTo($("#dragSortTxtPack"));
                //<li role="txtPack" targetImg="img01" data-url="image/t_img01.jpg">壁纸一</li>
            });
        }
    },
	initShowAdvertisment:function(){
		$("#seminarType .radio").on("click",function(){
			if($(this).text()=="广告专题"){
				$("#seminarAdvertisment").removeClass("none");			
				$(".zt_specialNone").addClass("none");

				$(".zt_labelname1").addClass("none");
				$(".zt_labelname2").removeClass("none");								
			}
			else{
				$("#seminarAdvertisment").addClass("none");				
				$(".zt_specialNone").removeClass("none");

				$(".zt_labelname1").removeClass("none");
				$(".zt_labelname2").addClass("none");	
			}
		});
	
	},
	initImgUpload:function(){
		var url='/Admin/Subject/uploadimgtheme?token='+token;

		$('#fileLoader').fileupload({
			dropZone:$("#cell_drag1"),//拖拽上传区域
	        url: url,
	        dataType: 'json',
	        previewMaxWidth: 385,//预览图片宽
	        previewMaxHeight: 192,//预览图片高
	        previewCrop: true,
	        autoUpload:true//是否开启自动上传
	    }).on('fileuploadprocessalways', function (e, data) {
	    	$.each(data.files, function (index, file) {
	       		$(file.preview).appendTo("#cell_img_canvas1");//显示预览图
	    	});
	   
		}).on('fileuploaddone', function (e, data) {
                      //绑定相关事件
                      var imgurl = data.result.url;
                
                      $(".subjectimg").val(imgurl);
                      var src = $(".fixed_elem img").attr("src");
             $(".fixed_elem img").attr("src",src+"/"+imgurl);;
                });
		$('#fileLoader2').fileupload({
			dropZone:$("#cell_drag2"),//拖拽上传区域
	        url: url,
	        dataType: 'json',
	        previewMaxWidth: 280,//预览图片宽
	        previewMaxHeight: 495,//预览图片高
	        previewCrop: true,
	        autoUpload:true//是否开启自动上传
	    }).on('fileuploadprocessalways', function (e, data) {
	    	$.each(data.files, function (index, file) {
	       		$(file.preview).appendTo("#cell_img_canvas2");//显示预览图

	    	});
	   
		}).on('fileuploaddone', function (e, data) {
                      //绑定相关事件
                      var imgurl = data.result.url;
                
                      $(".adv_img").val(imgurl);
                  });
		
	},
	//壁纸名称拖拽排序效果
	initDragSort:function(){
		this.refactorImgDragSort();
	},
	initBzListSort:function(){
		this.refactorImgBzListSort();
	},
	//添加专题 图片基本交互效果进阶-拖拽交互效果
	refactorImgDragSort:function(){
		//webUI.requireJs('plugins/dragsort/jquery.dragsort-0.5.2.min.js');
		$("#dragSortTxtPack,#dragSortTxtPack2").dragsort({ 
			dragSelector: "li", 
			dragBetween: true, 
			dragEnd:function(){
				console.log(this);
				//如果待选壁纸被移动到了已选壁纸区域中
				if(this.attr("role")=="txtPack" & this.parent().attr("id")=="dragSortTxtPack2"){
					//设定最新角色为已选壁纸
					$(this).removeClass("on");
					this.attr("role","txtPack2");
					//通过targetImg值来找对应缩略图进行增/删操作
					var targetImg=this.attr("targetImg");
					//缩略图中新增图片 demo代码
					var dataUrl=$(this).attr("data-url");
					$("<li targetImg="+targetImg+" class='img_list'><img src='"+dataUrl+"'></li>").appendTo($("#dragSortImgPack"));
					webUI.imgSimpleRearrange();
				}
				//如果已选壁纸被移动到了待选壁纸中
				if(this.attr("role")=="txtPack2" & this.parent().attr("id")=="dragSortTxtPack"){
					//设定最新角色为待选壁纸
					$(this).removeClass("on");
					this.attr("role","txtPack");
					var targetImg=this.attr("targetImg");
					//缩略图中移除对应图片
					$("#dragSortImgPack").find("[targetImg="+targetImg+"]").remove();
					webUI.imgSimpleRearrange();
				}
			} 
		});
		
		$("#dragSortImgPack").dragsort({ 
			dragSelector: ".img_list", 
			dragBetween: true, 
			dragEnd:function(){
				webUI.imgSimpleRearrange();
			} 
		});
	},
	//添加专题 图片基本交互效果
	refactorImgBzListSort:function(){
		$(".drag_sort_list").on("click","li",function(){
			//$(this).siblings().removeClass("on");
			$(this).toggleClass("on");
		});
		//按钮添加功能
		$("#packBzAdd").on("click",function(){
			if($("#dragSortTxtPack").find(".on").length==0){
				alert("请先选择待选主题ddd");
			}
			else{
				$("#dragSortTxtPack").find(".on").each(function(){
					//设定最新角色为已选壁纸
					$(this).removeClass("on");
					$(this).attr("role","txtPack2");
					var targetImg=$(this).attr("targetImg");
					$($(this).get(0).outerHTML).appendTo("#dragSortTxtPack2");
					//缩略图中新增图片 demo代码
					var dataUrl=$(this).attr("data-url");
					$("<li targetImg="+targetImg+" class='img_list'><img src='"+dataUrl+"'style='width:91px;'></li>").appendTo($("#dragSortImgPack"));
					$(this).remove();
					webUI.imgSimpleRearrange();
				});
			}		
		});
		//按钮删除功能
		$("#packBzDel").on("click",function(){
			if($("#dragSortTxtPack2").find(".on").length==0){
				alert("请先选择已选主题");
			}
			else{
				$("#dragSortTxtPack2").find(".on").each(function(){									
					//设定最新角色为待选壁纸
					$(this).removeClass("on");
					$(this).attr("role","txtPack");
					var targetImg=$(this).attr("targetImg");
					$($(this).get(0).outerHTML).appendTo("#dragSortTxtPack");
					//缩略图中移除对应图片
					$("#dragSortImgPack").find("[targetImg="+targetImg+"]").remove();
					$(this).remove();
					webUI.imgSimpleRearrange();
				});
			}
		});
		
	}
	
};

var zt_operation_07={
	funName:"zt_operation_07",
        
        initImgData: function () {
          
        var imgData = webUI.getData("PICKED_IMG");
        
        if (imgData != null) {
            var dataRecord = imgData.split("#");
            //console.log(dataRecord);
            $(dataRecord).each(function (idx, ele) {
                var recordDeatil = ele.split("*");
                var str = "<li role='txtPack' targetImg=" + recordDeatil[2] + " data-url='" + recordDeatil[0] + "'>" + recordDeatil[1] + "</li>";
                $(str).appendTo($("#dragSortTxtPack"));
                //var obj=$("<li role='txtPack'/>").text(recordDeatil[1]).attr("data-url",recordDeatil[0]);
                //obj.appendTo($("#dragSortTxtPack"));
                //<li role="txtPack" targetImg="img01" data-url="image/t_img01.jpg">壁纸一</li>
            });
        }
    },
        
	initSeminarPopBtnGoList:function(){
		$("#bzSeminarAdd02GoList").on("click",function(){
			//window.location.href="/Admin/Subject/index";
		});
	},
	initSeminarPopBtnAddMore:function(){
		$("#bzSeminarAdd02AddMore").on("click",function(){
			window.location.href="zt_operation_03.html";
		});
	},
	initSeminarBtnPrev:function(){
		$("#bzSeminarAdd02Prev").on("click",function(){
			window.location.href="zt_operation_03.html";
		});
	},
	initShowAdvertisment:function(){
		$("#seminarType .radio").on("click",function(){
			if($(this).text()=="广告专题"){
				$("#seminarAdvertisment").removeClass("none");			
				$(".zt_specialNone").addClass("none");

				$(".zt_labelname1").addClass("none");
				$(".zt_labelname2").removeClass("none");								
			}
			else{
				$("#seminarAdvertisment").addClass("none");				
				$(".zt_specialNone").removeClass("none");

				$(".zt_labelname1").removeClass("none");
				$(".zt_labelname2").addClass("none");	
			}
			
		});
	
	},
	initImgUpload:function(){
		var url='/Admin/Subject/uploadimgtheme?token='+token;

		$('#fileLoader').fileupload({
			dropZone:$("#cell_drag1"),//拖拽上传区域
	        url: url,
	        dataType: 'json',
	        previewMaxWidth: 385,//预览图片宽
	        previewMaxHeight: 192,//预览图片高
	        previewCrop: true,
	        autoUpload:true//是否开启自动上传
	    }).on('fileuploadprocessalways', function (e, data) {
	    	
	    	$.each(data.files, function (index, file) {
	       		$(file.preview).appendTo("#cell_img_canvas1");//显示预览图
	    	});
	   
		}).on('fileuploaddone', function (e, data) {
                      //绑定相关事件
                      var imgurl = data.result.url;
                
                      $(".subjectimg").val(imgurl);
                      var src = $(".fixed_elem img").attr("src");
             $(".fixed_elem img").attr("src",src+"/"+imgurl);

        });
		
		$('#fileLoader2').fileupload({
			dropZone:$("#cell_drag2"),//拖拽上传区域
	        url: url,
	        dataType: 'json',
	        previewMaxWidth: 280,//预览图片宽
	        previewMaxHeight: 495,//预览图片高
	        previewCrop: true,
	        autoUpload:true//是否开启自动上传
	    }).on('fileuploadprocessalways', function (e, data) {
	    	
	    	$.each(data.files, function (index, file) {

	       		$(file.preview).appendTo("#cell_img_canvas2");//显示预览图

	    	});
		}).on('fileuploaddone', function (e, data) {
                      //绑定相关事件
                      var imgurl = data.result.url;
                      $(".adv_img").val(imgurl);
         

        });
		
	},
	//壁纸名称拖拽排序效果
	initDragSort:function(){
		this.refactorImgDragSort();
	},
	initBzListSort:function(){
		this.refactorImgBzListSort();
	},
	//添加专题 图片基本交互效果进阶-拖拽交互效果
	refactorImgDragSort:function(){
		
		$("#dragSortTxtPack,#dragSortTxtPack2").dragsort({ 
			dragSelector: "li", 
			dragBetween: true, 
			dragEnd:function(){
				console.log(this);
				//如果待选壁纸被移动到了已选壁纸区域中
				if(this.attr("role")=="txtPack" & this.parent().attr("id")=="dragSortTxtPack2"){
					//设定最新角色为已选壁纸
					this.removeClass("on");
					this.attr("role","txtPack2");
					//通过targetImg值来找对应缩略图进行增/删操作
					var targetImg=this.attr("targetImg");
					//缩略图中新增图片 demo代码
					var dataUrl=$(this).attr("data-url");
					$("<li targetImg="+targetImg+" class='img_list'><img src='"+dataUrl+"'stype='width:91px'></li>").appendTo($("#dragSortImgPack"));
					webUI.imgSimpleRearrange();
				}
				//如果已选壁纸被移动到了待选壁纸中
				if(this.attr("role")=="txtPack2" & this.parent().attr("id")=="dragSortTxtPack"){
					//设定最新角色为待选壁纸
					this.removeClass("on");
					this.attr("role","txtPack");
					var targetImg=this.attr("targetImg");
					//缩略图中移除对应图片
					$("#dragSortImgPack").find("[targetImg="+targetImg+"]").remove();
					webUI.imgSimpleRearrange();
				}
			} 
		});
		
		$("#dragSortImgPack").dragsort({ 
			dragSelector: ".img_list", 
			dragBetween: true, 
			dragEnd:function(){
				webUI.imgSimpleRearrange();
			} 
		});
	},
	//添加专题 图片基本交互效果
	refactorImgBzListSort:function(){
		$(".drag_sort_list").on("click","li",function(){
			//$(this).siblings().removeClass("on");
			$(this).toggleClass("on");
		});
		//按钮添加功能
		$("#packBzAdd").on("click",function(){
			if($("#dragSortTxtPack").find(".on").length==0){
				alert("请先选择待选主题");
			}
			else{
				$("#dragSortTxtPack").find(".on").each(function(){
					//设定最新角色为已选壁纸
					$(this).removeClass("on");
					$(this).attr("role","txtPack2");
					var targetImg=$(this).attr("targetImg");
					$($(this).get(0).outerHTML).appendTo("#dragSortTxtPack2");
					//缩略图中新增图片 demo代码
					var dataUrl=$(this).attr("data-url");
					$("<li targetImg="+targetImg+" class='img_list'><img src='"+dataUrl+"'stype='width:91px'></li>").appendTo($("#dragSortImgPack"));
					$(this).remove();
					webUI.imgSimpleRearrange();
				});
			}		
		});
		//按钮删除功能
		$("#packBzDel").on("click",function(){
			if($("#dragSortTxtPack2").find(".on").length==0){
				alert("请先选择已选主题");
			}
			else{
				$("#dragSortTxtPack2").find(".on").each(function(){									
					//设定最新角色为待选壁纸
					$(this).removeClass("on");
					$(this).attr("role","txtPack");
					var targetImg=$(this).attr("targetImg");
					$($(this).get(0).outerHTML).appendTo("#dragSortTxtPack");
					//缩略图中移除对应图片
					$("#dragSortImgPack").find("[targetImg="+targetImg+"]").remove();
					$(this).remove();
					webUI.imgSimpleRearrange();
				});
			}
		});
		
	}
	
};

var zt_admin_tag={
	funName:"zt_admin_tag",
	//用户修改输入框内容的交互效果
	initTagInput:function(){
		$(".tag_add_sec").on("change",".tag_input",function(){
			var repeat=-1;//判断重名计数器
			var thisTxt=$(this).val();
			$(".tag_input").each(function(){
				($(this).val()==thisTxt)?repeat++:"";
			});
			//如果重名，保存按钮不能点击，同时出现错误提示
			if(repeat>0){
				var erro="<p class='erro_mes'>该名称已被使用，请使用其他名称！</p>";
				$(this).addClass("error");
				$(erro).insertAfter($(this));
				$("#tagSave").addClass("disable");
				$("#tagSave").removeAttr("openPopId");
			}
			//如果无重名，保存按钮可点击
			else{
				$("#tagSave").removeClass("disable");
				$("#tagSave").attr("openPopId","fixTipView");
			}
			
			//如果是最后一行，在它下方添加一行新记录
			var nodeAdd=$(this).parent().parent();
			if(nodeAdd.hasClass("tg_row_disable")){
				//左侧
				if(nodeAdd.hasClass("tg_row_lft")){
					var url='plugins/jqfile/server/php/index.php';
					var codeAdd=$("<tr class='tg_row_lft tg_row_disable'></tr>");
					var htmlCode=$("#tg_lft_disable").html();
					$(htmlCode).appendTo(codeAdd);
					$(codeAdd).insertAfter(nodeAdd);
					//动态添加的元素，需重新绑定上传功能
					codeAdd.find(".file_loader").fileupload({
						dropZone:null,//禁止拖拽上传
				        url: url,
				        dataType: 'json',
				        previewMaxWidth: 108,//预览图片宽
				        previewMaxHeight: 108,//预览图片高
				        previewCrop: true,
				        autoUpload:false//是否开启自动上传
				    }).on('fileuploadprocessalways',function (e, data) {
				    	var nodeImg=$(this).parent().parent().find(".file_ipt_img");
				    	$.each(data.files, function (index, file) {
				    		nodeImg.empty();
				       		$(file.preview).appendTo(nodeImg);//显示预览图
				    	});
				   
					});

				}
				//右侧
				else{
					var codeAdd="<tr class='tg_row_rgt tg_row_disable'>"+$("#tg_rgt_disable").html()+"</tr>";
					$(codeAdd).insertAfter(nodeAdd);
				}
				
				nodeAdd.removeClass("tg_row_disable");
			}
			
		});
		//输入框获取焦点时，重置所有样式
		$(".tag_add_sec").on("focus",".tag_input",function(){
			$(this).removeClass("error");
			$(this).nextAll().remove();
		});	
	},
	//标签行删除功能
	initTagBtnDel:function(){
		$("tbody").on("click",".tb_half_txt_btn",function(){
			//如果是最后一行则不可删除
			var node=$(this).parent().parent();
			var noDelete=node.hasClass("tg_row_disable");
			if(!noDelete){
				//var r=confirm("是否删除标签?");
				//if(r==true){node.remove();}
				var idx=$(this).attr("openPopId");
				$("#"+idx).addClass("show");
				$("#confirmYes").on("click",function(){
					node.remove();
					$("#"+idx).removeClass("show");
				});
				$("#confirCancel").on("click",function(){
					$("#"+idx).removeClass("show");
				});
			}
		});
	},
	//初始化图片上传
	initImgUpload:function(){
		var url='plugins/jqfile/server/php/index.php';
		$(".file_loader").fileupload({
			dropZone:null,//拖拽上传区域
	        url: url,
	        dataType: 'json',
	        previewMaxWidth: 108,//预览图片宽
	        previewMaxHeight: 108,//预览图片高
	        previewCrop: true,
	        autoUpload:false//是否开启自动上传
	    }).on('fileuploadprocessalways',function (e, data) {
	    	var nodeImg=$(this).parent().parent().find(".file_ipt_img");
	    	$.each(data.files, function (index, file) {
	    		nodeImg.empty();
	       		$(file.preview).appendTo(nodeImg);//显示预览图
	    	});
	   
		});
	}
};
