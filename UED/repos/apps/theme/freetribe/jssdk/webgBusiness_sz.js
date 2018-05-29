/**
 * 时钟 各页面的业务逻辑，和UI分开来写 init开头的方法会被自动执行
 */
var sz_operation_szsubject_add01 = {
    funName: "sz_operation_szsubject_add01",
    pageCount: 4,
    recordCount: 5,
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
        this.dataFetch(1);
    },
    //点击分页获取数据
    initPagerDataFetch: function () {
        //初始化分页控件
      // webUI.requireJs(jsroot+'/jssdk/jquery.paginate.js');
        $("#pager").paginate({
            count: sz_operation_szsubject_add01.pageCount, //有多少页
            start: 1, //起始页面
            display: 10, //显示多少个分页数字
            images: false,
            mouse: 'press',
            onChange: function (page) {             
                sz_operation_szsubject_add01.dataFetch(page);
            }
        });
        //输入页面号码 点击确定按钮去到对应页面 原控件不带此功能 需自己写
        $("#pageOk").click(function () {
           
            var goPageNum = $("#goPageNum").val() - 1;
            $("#pager .jPag-pages li:eq(" + goPageNum + ")").trigger("click");
        });
    },
    dataFetch: function (pageNum) {
        //ajax 请求数据
        $.ajax({
            url: "/Admin/Clockres/getClocks",
            type: "post",
            async: false,
            dataType: "json",
            data: {"pageNum": pageNum,"token":token},
            success: function (data) {
                $("#viewTbody").find("tr").empty();//清空原有数据
                //储存分页汇总数据
                sz_operation_szsubject_add01.pageCount = data.pageCount;
                sz_operation_szsubject_add01.recordCount = data.recordCount;
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
                        copyObj.find("td:eq(2)").text(ele.uploadDate);
                        copyObj.find("td:eq(3)").text(ele.times);
                        copyObj.find("td:eq(4)").text(ele.status);
                        //渲染新数据至页面
                        $(copyObj).appendTo($("#viewTbody"));
                    });
                }
                //在页面更新分页汇总数据
                $("#recordCount").text(sz_operation_szsubject_add01.recordCount);
                $("#pageCount").text(sz_operation_szsubject_add01.pageCount);

            },
            error: function () {
                //TODO 错误的处理
                alert("AJAX请求出错！请修改webgBusiness_sz.js->sz_operation_szsubject_add01");
            }
        });

    },
    //下一步
    initNext: function () {
       $("#taoTuPack01Next").on("click", function () {
            window.location.href = "/Admin/Clocksubject/addsubjecttwo";
        });
    },
    //TODO web本地存储 清空待选壁纸数据
    initClearImg: function () {
        webUI.delData("PICKED_IMG");
    },
    //TODO web本地存储 添加待选壁纸数据
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
                alert("请选择壁纸");
            }



        });
    },

    initViewModeSelect: function () {
        $(".btn_switch").on("click", function () {
            //选中的模式保持批量管理功能
            var openId = $(this).attr("openId");
            $("#" + openId).addClass("admin_mode");
        });
    },
};

var sz_operation_szsubject_add02 = {
    funName: "sz_operation_szsubject_add02",
    initSeminarPopBtnGoList: function () {
        /*
         * 
        $("#szsubjectAdd02GoList").on("click", function () {
            window.location.href = "/Admin/Clocksubject/index";
        });
         
        */
        
    },
    initSeminarPopBtnAddMore: function () {
        $("#szsubjectAdd02AddMore").on("click", function () {
            window.location.href = "/Admin/Clocksubject/addsubject";
        });
    },
    initSeminarBtnPrev: function () {
        $("#szsubjectAdd02Prev").on("click", function () {
            window.location.href = "/Admin/Clocksubject/addsubject";
        });
    },
    //壁纸专题/广告专题 切换效果
    initShowAdvertisment: function () {
        $("#szsubjectType .radio").on("click", function () {
            
           
            if ($(this).text() == "广告专题") {
                $("#szsubjectAdvertisment").removeClass("none");
                $(".sz_block_wrap").addClass("none");
            }else {
                $("#szsubjectAdvertisment").addClass("none");
                $(".sz_block_wrap").removeClass("none");
            }

        });

    },
    initImgUpload: function () {
        var url = '/Admin/Clocksubject/uploadimg?token='+token;

        $('#fileLoader').fileupload({
            dropZone: $("#cell_drag1"), //拖拽上传区域
            url: url,
            dataType: 'json',
            previewMaxWidth: 200, //预览图片宽
            previewMaxHeight: 200, //预览图片高
            previewCrop: true,
            autoUpload: true//是否开启自动上传
        }).on('fileuploadprocessalways', function (e, data) {

            $.each(data.files, function (index, file) {

                $(file.preview).appendTo("#cell_img_canvas1");//显示预览图

            });

        }).on('fileuploaddone', function (e, data) {
            //绑定相关事件
            var imgurl = data.result.url;
            $("#loadurl").val(imgurl);
            var src = $(".fixed_elem img").attr("src");
             $(".fixed_elem img").attr("src",src+"/"+imgurl);
            
        });

        $('#fileLoader2').fileupload({
            dropZone: $("#cell_drag2"), //拖拽上传区域
            url: url,
            dataType: 'json',
            previewMaxWidth: 200, //预览图片宽
            previewMaxHeight: 200, //预览图片高
            previewCrop: true,
            autoUpload: true//是否开启自动上传
        }).on('fileuploadprocessalways', function (e, data) {

            $.each(data.files, function (index, file) {
                $(file.preview).appendTo("#cell_img_canvas2");//显示预览图
            });
        }).on('fileuploaddone', function (e, data) {
            //绑定相关事件
            var imgurl = data.result.url;
             $(".upload_adv").val(imgurl);

        });

    },
    //壁纸名称拖拽排序效果
    initDragSort: function () {
        this.refactorImgDragSort();
    },
    initBzListSort: function () {
        this.refactorImgBzListSort();
    },
    //添加专题 图片基本交互效果进阶-拖拽交互效果
    refactorImgDragSort: function () {
       // webUI.requireJs('plugins/dragsort/jquery.dragsort-0.5.2.min.js');
        $("#dragSortTxtPack,#dragSortTxtPack2").dragsort({
            dragSelector: "li",
            dragBetween: true,
            dragEnd: function () {
                console.log(this);
                //如果待选壁纸被移动到了已选壁纸区域中
                if (this.attr("role") == "txtPack" & this.parent().attr("id") == "dragSortTxtPack2") {
                    //设定最新角色为已选壁纸
                    this.removeClass("on");
                    this.attr("role", "txtPack2");
                    //通过targetImg值来找对应缩略图进行增/删操作
                    var targetImg = this.attr("targetImg");
                    //缩略图中新增图片 demo代码
                    var dataUrl = $(this).attr("data-url");
                    $("<li targetImg=" + targetImg + " class='img_list'><img src='" + dataUrl + "'></li>").appendTo($("#dragSortImgPack"));
                    webUI.imgSimpleRearrange2();
                }
                //如果已选壁纸被移动到了待选壁纸中
                if (this.attr("role") == "txtPack2" & this.parent().attr("id") == "dragSortTxtPack") {
                    //设定最新角色为待选壁纸
                    this.removeClass("on");
                    this.attr("role", "txtPack");
                    var targetImg = this.attr("targetImg");
                    //缩略图中移除对应图片
                    $("#dragSortImgPack").find("[targetImg=" + targetImg + "]").remove();
                    webUI.imgSimpleRearrange2();
                }
            }
        });

        $("#dragSortImgPack").dragsort({
            dragSelector: ".img_list",
            dragBetween: true,
            dragEnd: function () {
                webUI.imgSimpleRearrange2();
            }
        });
    },
    //添加专题 图片基本交互效果
    refactorImgBzListSort: function () {
        $(".drag_sort_list").on("click", "li", function () {
            $(this).siblings().removeClass("on");
            $(this).toggleClass("on");
        });
        //按钮添加功能
        $("#packBzAdd").on("click", function () {
            if ($("#dragSortTxtPack").find(".on").length == 0) {
                alert("请先选择待选时钟");
            }
            else {
                $("#dragSortTxtPack").find(".on").each(function () {
                    //设定最新角色为已选壁纸
                    $(this).removeClass("on");
                    $(this).attr("role", "txtPack2");
                    var targetImg = $(this).attr("targetImg");
                    $($(this).get(0).outerHTML).appendTo("#dragSortTxtPack2");
                    //缩略图中新增图片 demo代码
                    var dataUrl = $(this).attr("data-url");
                    $("<li targetImg=" + targetImg + " class='img_list'><img src='" + dataUrl + "'></li>").appendTo($("#dragSortImgPack"));
                    $(this).remove();
                    webUI.imgSimpleRearrange2();
                });
            }
        });
        //按钮删除功能
        $("#packBzDel").on("click", function () {
            if ($("#dragSortTxtPack2").find(".on").length == 0) {
                alert("请先选择已选时钟");
            }
            else {
                $("#dragSortTxtPack2").find(".on").each(function () {
                    //设定最新角色为待选壁纸
                    $(this).removeClass("on");
                    $(this).attr("role", "txtPack");
                    var targetImg = $(this).attr("targetImg");
                    $($(this).get(0).outerHTML).appendTo("#dragSortTxtPack");
                    //缩略图中移除对应图片
                    $("#dragSortImgPack").find("[targetImg=" + targetImg + "]").remove();
                    $(this).remove();
                    webUI.imgSimpleRearrange2();
                });
            }
        });

    },  
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
};



