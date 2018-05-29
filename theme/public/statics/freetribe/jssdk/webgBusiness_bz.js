/**
 * 壁纸 各页面的业务逻辑，和UI分开来写 init开头的方法会被自动执行
 */
//该处变量=对应的html文件内的id名,表示属于该页面下的各种业务逻辑
var bz_admin_wp02 = {
    /**
     * funName值和外层变量名一致，用于webUI中去重，防止一个页面被多次注册；让路由只加载当前正在使用的页面的业务逻辑。
     */
    funName: "bz_admin_wp02",
    //取消编辑
    initBtnCancel: function () {
        $("#bzAdminWp02Cancel").on("click", function () {
            window.history.back(-1);
        });
    },
    initApprove: function () {
        $("#wp02Approve").find("li").on("click", function () {
            var res = $(this).text();
            if (res != "通过") {
                $("#wp02Editor").removeClass("none");
            }
            else {
                $("#wp02Editor").addClass("none");
            }
        });
    }

};

var bz_admin_taotu_list02 = {
    funName: "bz_admin_taotu_list02",
    initImg: function () {
        webUI.imageRearrange();
    },
    //壁纸名称拖拽排序效果
    initDragSort: function () {
        
       
       // webUI.requireJs('plugins/dragsort/jquery.dragsort-0.5.2.min.js');
        $("#dragSortTxt1").dragsort({
            dragSelector: "li",
            dragBetween: true,
            dragEnd: function () {
                //拖拽结束后的代码
            }
        });
        $("#dragSortImgPack").dragsort({
            dragSelector: "li",
            dragBetween: true,
            dragEnd: function () {
                webUI.imageRearrange();
            }
        });

    },
    initPopCancel: function () {
        $("#bzAdminTaoTuL2Cancel").on("click", function () {
            window.history.back(-1);
        });
    }
};

var bz_admin_taotu_pack01 = {
    funName: "bz_admin_taotu_pack01",
    pageCount: 10,
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
        bz_admin_taotu_pack01.dataFetch(1,0);
    },
    //点击分页获取数据
    initPagerDataFetch: function () {
        //初始化分页控件
      //webUI.requireJs(jsroot+'/jssdk/jquery.paginate.js');
        $("#pager").paginate({
            count:  bz_admin_taotu_pack01.pageCount, //有多少页
          
            start: 1, //起始页面
            display: 10, //显示多少个分页数字
            images: false,
            mouse: 'press',
            onChange: function (page) {
                var tid = $(".tag_one").find(".on").attr("tid");
                bz_admin_taotu_pack01.dataFetch(page,tid);
            }
        });
        //输入页面号码 点击确定按钮去到对应页面 原控件不带此功能 需自己写
        $("#pageOk").click(function () {
            var goPageNum = $("#goPageNum").val() - 1;
            $("#pager .jPag-pages li:eq(" + goPageNum + ")").trigger("click");
        });
    },
    dataFetch: function (pageNum,tid) {
        //ajax 请求数据
        $.ajax({
            url: "/Admin/wallpaperadmin/getWallpaper",
            type: "post",
            async: false,
            dataType: "json",
            data: {"pageNum": pageNum,"token":token,"tid":tid},
            success: function (data) {
              
             // console.log(data);
                $("#viewTbody").find("tr").empty();//清空原有数据
                //储存分页汇总数据
                bz_admin_taotu_pack01.pageCount = data.pageCount;
                bz_admin_taotu_pack01.recordCount = data.recordCount;
                //遍历后台json数据 不同页面此处的内容很可能不一致 根据具体业务来
                if (data.datas.length > 0) {
                    $(data.datas).each(function (idx, ele) {
                        //获取数据格式，去掉隐藏class
                        $(".btn_check_all").removeClass("on");
                        var copyObj = $("#demoTr").clone();
                        copyObj.removeAttr("class");
                        copyObj.removeAttr("id");
                        
                        copyObj.find(".btn_check").addClass("btn_check_list");
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
                $("#recordCount").text(bz_admin_taotu_pack01.recordCount);
                $("#pageCount").text(bz_admin_taotu_pack01.pageCount);

            },
            error: function () {
                //TODO 错误的处理
                alert("AJAX请求出错！请修改webgBusiness_bz.js->bz_admin_taotu_pack01");
            }
        });

    },
    //下一步
    initNext: function () {
        $("#taoTuPack01Next").on("click", function () {
            window.location.href = "/Admin/wallpaperadmin/adminsetTwo";
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
    //验证 web本地存储中的 PICKED_IMG 是否符合规则
    checkPickedImg: function () {

    }

};

var bz_admin_taotu_pack02 = {
    funName: "bz_admin_taotu_pack02",
    //TODO web本地存储 初始化待选壁纸数据
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
    //壁纸选择进阶-拖拽交互效果
    initDragSort: function () {
        webUI.imgDragSort();

    },
    //壁纸选择基本交互效果
    initBzListSort: function () {
        webUI.imgBzListSort();
    },
    initPopCancel: function () {
        $("#bzAdminTaoTuP2Cancel").on("click", function () {
            window.history.back(-1);
        });
    }

};

var bz_operation_daily_list02 = {
    funName: "bz_operation_daily_list02",
    initDragSort: function () {
        //webUI.requireJs('plugins/dragsort/jquery.dragsort-0.5.2.min.js');
        webUI.imageRearrange();
        $("#dragSortImgPack").dragsort({
            dragSelector: "li",
            dragBetween: true,
            dragEnd: function () {
                webUI.imageRearrange();
            }
        });
    },
};
var bz_operation_seminar_list02 = {
    funName: "bz_operation_seminar_list02",
    initDragSort: function () {
        this.refactorImgDragSort();
    },
    initBzListSort: function () {
        this.refactorImgBzListSort();
    },
    //动态把输入框内的文字内容写入缩略图中
    initAutoTxt: function () {
        $("#ztTxt").on("keyup", function () {
            $("#autoGetTxt").text($(this).val());
        });
    },
    //添加专题 图片基本交互效果进阶-拖拽交互效果
    refactorImgDragSort: function () {
       // webUI.requireJs('plugins/dragsort/jquery.dragsort-0.5.2.min.js');
        $("#dragSortTxtPack,#dragSortTxtPack2").dragsort({
            dragSelector: "li",
            dragBetween: true,
            dragEnd: function () {
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
            
            //$(this).siblings().removeClass("on");
            $(this).toggleClass("on");
        });
        //按钮添加功能
        $("#packBzAdd").on("click", function () {
            if ($("#dragSortTxtPack").find(".on").length == 0) {
                alert("请先选择待选壁纸");
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
                alert("请先选择已选壁纸");
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

    }
};

var bz_usr_upload02 = {
    funName: "bz_usr_upload02",
    initPopViewList: function () {
        $("#upload02ViewList").on("click", function () {
            var data = "wallpaper_up_status=1&token=" + token;
            var url = "/Admin/wallpapermy/up_status";
            $.post(url, data, function (e) {
                if (e) {
                    window.location.href = "/Admin/wallpapermy/index"
                } else {
                    alert("重新操作");
                    return 0;
                }
            });

        });

    },
    initPopUploadMore: function () {
        $("#upload02UploadMore").on("click", function () {
            window.location.href = "/Admin/wallpapermyupload/index";
        });
    }
};

var bz_operation_daily_add01 = {
    funName: "bz_operation_daily_add01",
    initBtnAddNext: function () {
        $("#bzDailyAddNext").on("click", function () {
            window.location.href = "/Admin/wallpapersets/pubdailytwo";
        });

    }
};

var bz_operation_daily_add02 = {
    funName: "bz_operation_daily_add02",
    //上一步
    initOperationBtnAddPrev: function () {
        $("#bzDailyAddPrev").on("click", function () {
            window.location.href = "/Admin/wallpapersets/pubdaily";
        });
    },
    //继续添加
    initPopBtnAdd: function () {
        $("#dailyPopBtnAdd").on("click", function () {
            window.location.href = "bz_operation_daily_add01.html";
        });
    },
    //返回每日精选列表
    initPopBtnReturn: function () {
        $("#dailyPopBtnReturn").on("click", function () {
            window.location.href = "bz_operation_daily_list01.html";
        });
    },
    initDragSort: function () {
       // webUI.requireJs('plugins/dragsort/jquery.dragsort-0.5.2.min.js');
        webUI.imageRearrange();
        $("#dragSortImgPack").dragsort({
            dragSelector: "li",
            dragBetween: true,
            dragEnd: function () {
                webUI.imageRearrange();
            }
        });
    }
};

var bz_operation_seminar_add01 = {
    funName: "bz_operation_seminar_add01",
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
        this.dataFetch(1,0);
    },
    //点击分页获取数据
    initPagerDataFetch: function () {
        //初始化分页控件
      // webUI.requireJs(jsroot+'/jssdk/jquery.paginate.js');
        $("#pager").paginate({
            count: bz_operation_seminar_add01.pageCount, //有多少页
            start: 1, //起始页面
            display: 10, //显示多少个分页数字
            images: false,
            mouse: 'press',
            onChange: function (page) {
                var tid = $(".tag_one").find(".on").attr("tid");
                bz_operation_seminar_add01.dataFetch(page,tid);
            }
        });
        //输入页面号码 点击确定按钮去到对应页面 原控件不带此功能 需自己写
        $("#pageOk").click(function () {
           
            var goPageNum = $("#goPageNum").val() - 1;
            $("#pager .jPag-pages li:eq(" + goPageNum + ")").trigger("click");
        });
    },
    dataFetch: function (pageNum,tid) {
        //ajax 请求数据
        $.ajax({
            url: "/Admin/wallpaperadmin/getWallpaper",
            type: "post",
            async: false,
            dataType: "json",
            data: {"pageNum": pageNum,"token":token,"tid":tid},
            success: function (data) {
                $("#viewTbody").find("tr").empty();//清空原有数据
                //储存分页汇总数据
                bz_operation_seminar_add01.pageCount = data.pageCount;
                bz_operation_seminar_add01.recordCount = data.recordCount;
                //遍历后台json数据 不同页面此处的内容很可能不一致 根据具体业务来
                if (data.datas.length > 0) {
                    $(data.datas).each(function (idx, ele) {
                        //获取数据格式，去掉隐藏class
                        $(".btn_check_all").removeClass("on");
                        var copyObj = $("#demoTr").clone();
                        copyObj.removeAttr("class");
                        copyObj.removeAttr("id");
                       
                        copyObj.find(".btn_check").addClass("btn_check_list");
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
                $("#recordCount").text(bz_operation_seminar_add01.recordCount);
                $("#pageCount").text(bz_operation_seminar_add01.pageCount);

            },
            error: function () {
                //TODO 错误的处理
                alert("AJAX请求出错！请修改webgBusiness_bz.js->bz_operation_seminar_add01");
            }
        });

    },
    //下一步
   /* initNext: function () {
       $("#taoTuPack01Next").on("click", function () {
            window.location.href = "/Admin/wallpaperadminsubject/addsubjecttwo";
        });
    },*/
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
    
   /* initOperationBtnAddNext: function () {
        $("#bzSeminarAddNext").on("click", function () {
            window.location.href = "/Admin/wallpaperadsubject/addsubjecttwo";
        });
    },*/
    initViewModeSelect: function () {
        $(".btn_switch").on("click", function () {
            //选中的模式保持批量管理功能
            var openId = $(this).attr("openId");
            $("#" + openId).addClass("admin_mode");
        });
    },
};

//动态壁纸专题
var bz_bzlwpsubject_bzlwpsubjectadd = {
    funName: "bz_bzlwpsubject_bzlwpsubjectadd",
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
        bz_bzlwpsubject_bzlwpsubjectadd.dataFetch(1,0);
    },
    //点击分页获取数据
    initPagerDataFetch: function () {
        console.log(bz_bzlwpsubject_bzlwpsubjectadd);
        //初始化分页控件
    
        $("#pager").paginate({
            count: this.pageCount, //有多少页
            start: 1, //起始页面
            display: 10, //显示多少个分页数字
            images: false,
            mouse: 'press',
            onChange: function (page) {
                var tid = $(".tag_one").find(".on").attr("tid");
                bz_bzlwpsubject_bzlwpsubjectadd.dataFetch(page,tid);
            }
        });
        //输入页面号码 点击确定按钮去到对应页面 原控件不带此功能 需自己写
        $("#pageOk").click(function () {
           
            var goPageNum = $("#goPageNum").val() - 1;
            $("#pager .jPag-pages li:eq(" + goPageNum + ")").trigger("click");
        });
    },
    dataFetch: function (pageNum,tid) {
        //ajax 请求数据
        $.ajax({
            url: "/Admin/Livewallpapersubject/getlivewallpaper",
            type: "post",
            async: false,
            dataType: "json",
            data: {"pageNum": pageNum,"token":token,"tid":tid},
            success: function (data) {
                $("#viewTbody").find("tr").empty();//清空原有数据
                //储存分页汇总数据
                bz_bzlwpsubject_bzlwpsubjectadd.pageCount = data.pageCount;
                bz_bzlwpsubject_bzlwpsubjectadd.recordCount = data.recordCount;
                //遍历后台json数据 不同页面此处的内容很可能不一致 根据具体业务来
                if (data.datas.length > 0) {
                    $(data.datas).each(function (idx, ele) {
                        //获取数据格式，去掉隐藏class
                        $(".btn_check_all").removeClass("on");
                        var copyObj = $("#demoTr").clone();
                        copyObj.removeAttr("class");
                        copyObj.removeAttr("id");
                       
                        copyObj.find(".btn_check").addClass("btn_check_list");
                        copyObj.find(".btn_check").attr("data-img-src", ele.url);
                        copyObj.find(".btn_check").attr("data-img-name", ele.wallpaperlive_name);
                        copyObj.find(".btn_check").attr("mid", ele.wallpaperlive_id);
                        copyObj.find("img").attr("src", ele.url);
                        
                        copyObj.find("td:eq(1)").text(ele.wallpaperlive_name);
                        copyObj.find("td:eq(2)").text(ele.wallpaperlive_onlinetime);
                        copyObj.find("td:eq(3)").text(ele.wallpaperlive_uploadtime);
                        copyObj.find("td:eq(4)").text(ele.wallpaperlive_down);
                        copyObj.find("td:eq(6)").text(ele.wallpaperlive_like);
                        copyObj.find("td:eq(7)").text(ele.wallpaperlive_status);
                        //渲染新数据至页面
                        $(copyObj).appendTo($("#viewTbody"));
                    });
                }
                //在页面更新分页汇总数据
                $("#recordCount").text(bz_bzlwpsubject_bzlwpsubjectadd.recordCount);
                $("#pageCount").text(bz_bzlwpsubject_bzlwpsubjectadd.pageCount);

            },
            error: function () {
                //TODO 错误的处理
                alert("AJAX请求出错！请修改webgBusiness_bz.js->bz_operation_seminar_add01");
            }
        });

    },
    //下一步
   /* initNext: function () {
       $("#taoTuPack01Next").on("click", function () {
            window.location.href = "/Admin/wallpaperadminsubject/addsubjecttwo";
        });
    },*/
    //TODO web本地存储 清空待选壁纸数据
    initClearImg: function () {
        webUI.delData("PICKED_LWP_IMG");
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
                var imgData = webUI.getData("PICKED_LWP_IMG");
                if (imgData != null) {
                    tempImgArr = tempImgArr + "#" + imgData;
                }
                webUI.setData("PICKED_LWP_IMG", tempImgArr);
                //更新添加按钮上的 已选壁纸数量
                $(".icon_num").text(webUI.getData("PICKED_LWP_IMG").split("#").length);
            }
            else {
                alert("请选择壁纸");
            }
        });
    },
    
   /* initOperationBtnAddNext: function () {
        $("#bzSeminarAddNext").on("click", function () {
            window.location.href = "/Admin/wallpaperadsubject/addsubjecttwo";
        });
    },*/
    initViewModeSelect: function () {
        $(".btn_switch").on("click", function () {
            //选中的模式保持批量管理功能
            var openId = $(this).attr("openId");
            $("#" + openId).addClass("admin_mode");
        });
    },
};

//添加动态壁纸专题第二步
var bz_bzlwpsubject_bzlwpsubjectadd02 = {
    funName: "bz_bzlwpsubject_bzlwpsubjectadd02",
    initSeminarPopBtnGoList: function () {
        /*
         * 
         $("#bzSeminarAdd02GoList").on("click", function () {
            window.location.href = "/Admin/wallpaperadminsubject/index";
        });
        */
    },
    initSeminarPopBtnAddMore: function () {
        $("#bzSeminarAdd02AddMore").on("click", function () {
            window.location.href = "/Admin/livewallpapersubject/add";
        });
    },
    initSeminarBtnPrev: function () {
        $("#bzSeminarAdd02Prev").on("click", function () {
            window.location.href = "/Admin/livewallpapersubject/add";
        });
    },
    //壁纸专题/广告专题 切换效果
    initShowAdvertisment: function () {
        $("#seminarType .radio").on("click", function () {
            
           
            if ($(this).text() == "广告专题") {
                $("#seminarAdvertisment").removeClass("none");
                $(".bz_block_wrap").addClass("none");
            }
            else {
                $("#seminarAdvertisment").addClass("none");
                $(".bz_block_wrap").removeClass("none");
            }

        });

    },
    initImgUpload: function () {
        var url = '/Admin/livewallpapersubject/uploadimg?token='+token;

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
                $("#cell_img_canvas1").html(file.preview);//显示预览图   
            });

        }).on('fileuploaddone', function (e, data) {
            var imgurl = data.result.url;
            var src = image_url+imgurl;
            $("#loadurl").val(imgurl);
            $("#cover_box").html("<img src='"+src+"' />");     
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
                $("#cell_img_canvas2").html(file.preview);//显示预览图
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
    //添加 图片基本交互效果进阶-拖拽交互效果
    refactorImgDragSort: function () {
       // webUI.requireJs('plugins/dragsort/jquery.dragsort-0.5.2.min.js');
        $("#dragSortTxtPack,#dragSortTxtPack2").dragsort({
            dragSelector: "li",
            dragBetween: true,
            dragEnd: function () {
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
           // $(this).siblings().removeClass("on");
            $(this).toggleClass("on");
        });
        //按钮添加功能
        $("#packBzAdd").on("click", function () {
            if ($("#dragSortTxtPack").find(".on").length == 0) {
                alert("请先选择待选壁纸");
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
                alert("请先选择已选壁纸");
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
        var imgData = webUI.getData("PICKED_LWP_IMG");
        
        if (imgData != null) {
            var dataRecord = imgData.split("#");
            //console.log(dataRecord);
            $(dataRecord).each(function (idx, ele) {
                var recordDeatil = ele.split("*");
                var str = "<li role='txtPack' targetImg=" + recordDeatil[2] + " data-url='" + recordDeatil[0] + "'>" + recordDeatil[1] + "</li>";
                $(str).appendTo($("#dragSortTxtPack"));
            });
        }
    },
};


var bz_operation_seminar_add02 = {
    funName: "bz_operation_seminar_add02",
    initSeminarPopBtnGoList: function () {
        /*
         * 
         $("#bzSeminarAdd02GoList").on("click", function () {
            window.location.href = "/Admin/wallpaperadminsubject/index";
        });
        */
    },
    initSeminarPopBtnAddMore: function () {
        $("#bzSeminarAdd02AddMore").on("click", function () {
            window.location.href = "/Admin/wallpaperadminsubject/addsubject";
        });
    },
    initSeminarBtnPrev: function () {
        $("#bzSeminarAdd02Prev").on("click", function () {
            window.location.href = "/Admin/wallpaperadminsubject/addsubject";
        });
    },
    //壁纸专题/广告专题 切换效果
    initShowAdvertisment: function () {
        $("#seminarType .radio").on("click", function () {
            
           
            if ($(this).text() == "广告专题") {
                $("#seminarAdvertisment").removeClass("none");
                $(".bz_block_wrap").addClass("none");
            }
            else {
                $("#seminarAdvertisment").addClass("none");
                $(".bz_block_wrap").removeClass("none");
            }

        });

    },
    initImgUpload: function () {
        var url = '/Admin/wallpaperadminsubject/uploadimg?token='+token;

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
    //添加 图片基本交互效果进阶-拖拽交互效果
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
           // $(this).siblings().removeClass("on");
            $(this).toggleClass("on");
        });
        //按钮添加功能
        $("#packBzAdd").on("click", function () {
            if ($("#dragSortTxtPack").find(".on").length == 0) {
                alert("请先选择待选壁纸");
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
                alert("请先选择已选壁纸");
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

var bz_admin_tag = {
    funName: "bz_admin_tag",
    //用户修改输入框内容的交互效果
    initTagInput: function () {
        $(".tag_add_sec").on("change", ".tag_input", function () {
            var repeat = -1;//判断重名计数器
            var thisTxt = $(this).val();
            $(".tag_input").each(function () {
                ($(this).val() == thisTxt) ? repeat++ : "";
            });
            //如果重名，保存按钮不能点击，同时出现错误提示
            if (repeat > 0) {
                var erro = "<p class='erro_mes'>该名称已被使用，请使用其他名称！</p>";
                $(this).addClass("error");
                $(erro).insertAfter($(this));
                $("#tagSave").addClass("disable");
                $("#tagSave").removeAttr("openPopId");
            }
            //如果无重名，保存按钮可点击
            else {
                $("#tagSave").removeClass("disable");
                $("#tagSave").attr("openPopId", "fixTipView");
            }

            //如果是最后一行，在它下方添加一行新记录
            var nodeAdd = $(this).parent().parent();
            if (nodeAdd.hasClass("tg_row_disable")) {
                //左侧
                if (nodeAdd.hasClass("tg_row_lft")) {
                    var url = '/Admin/wallpaperadmin/addtargImg?token=' + token;
                    var codeAdd = $("<tr class='tg_row_lft tg_row_disable'></tr>");
                    var htmlCode = $("#tg_lft_disable").html();
                    $(htmlCode).appendTo(codeAdd);
                    $(codeAdd).insertAfter(nodeAdd);
                    //动态添加的元素，需重新绑定上传功能
                    codeAdd.find(".file_loader").fileupload({
                        dropZone: null, //禁止拖拽上传
                        url: url,
                        dataType: 'json',
                        previewMaxWidth: 108, //预览图片宽
                        previewMaxHeight: 108, //预览图片高
                        previewCrop: true,
                        autoUpload: true//是否开启自动上传
                    }).on('fileuploadprocessalways', function (e, data) {
                        var nodeImg = $(this).parent().parent().find(".file_ipt_img");
                        $.each(data.files, function (index, file) {
                            nodeImg.empty();
                            $(file.preview).appendTo(nodeImg);//显示预览图
                        });

                    });
                }
                //右侧
                else {
                    var codeAdd = "<tr class='tg_row_rgt tg_row_disable'>" + $("#tg_rgt_disable").html() + "</tr>";
                    $(codeAdd).insertAfter(nodeAdd);
                }

                nodeAdd.removeClass("tg_row_disable");
            }

        });
        //输入框获取焦点时，重置所有样式
        $(".tag_add_sec").on("focus", ".tag_input", function () {
            $(this).removeClass("error");
            $(this).nextAll().remove();
        });
    },
    //标签行删除功能
    initTagBtnDel: function () {
        $("tbody").on("click", ".tb_half_txt_btn", function () {
            //如果是最后一行则不可删除
            var node = $(this).parent().parent();
            var noDelete = node.hasClass("tg_row_disable");
            if (!noDelete) {
                //var r=confirm("是否删除标签?");
                //if(r==true){node.remove();}
                var idx = $(this).attr("openPopId");
                $("#" + idx).addClass("show");
                $("#confirmYes").on("click", function () {
                    node.remove();
                    $("#" + idx).removeClass("show");
                });
                $("#confirCancel").on("click", function () {
                    $("#" + idx).removeClass("show");
                });
            }
        });
    },
    //初始化图片上传
    initImgUpload: function () {
        var url = '/Admin/wallpaperadmin/addtargImg?token=' + token;

        $(".file_loader").fileupload({
            dropZone: null, //拖拽上传区域
            url: url,
            dataType: 'json',
            previewMaxWidth: 108, //预览图片宽
            previewMaxHeight: 108, //预览图片高
            previewCrop: true,
            autoUpload: true, //是否开启自动上传
        }).on('fileuploadprocessalways', function (e, data) {
            var nodeImg = $(this).parent().parent().find(".file_ipt_img");
            $.each(data.files, function (index, file) {
                nodeImg.empty();
                $(file.preview).appendTo(nodeImg);//显示预览图

            });
        }).on('fileuploaddone', function (e, data) {
            //绑定相关事件
            var imgurl = data.result.url;
            $(this).parent().parent().next().attr("vurl",imgurl);

        });
    }
};


