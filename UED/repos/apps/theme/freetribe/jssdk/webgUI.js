//////////////////////////////////////////
//页面的交互效果都写在这 依赖Jquery init命名开头的方法将被自动执行
var webUI = {
  
    registPage: [],
    run: function () {
        //执行webUI内init开头的方法
        this.autoRun(this);
        //执行通过addRegistPage方法已注册，且当前正在使用的页面 的 业务逻辑
        this.runRegistPage();
    },
    //加载JS公用方法
    requireJs: function (url) {
        var getUrl = "<script language='javascript' src='" + url + "'></script>";
        $("body").append(getUrl);
    },
    //加载css公用方法
    requireCss: function (url) {
        var getUrl = "<link rel='stylesheet' href='" + url + "'></link>";
        $("head").append(getUrl);
    },
    //遍历对象方法，自动执行init开头的方法
    autoRun: function (obj) {
        //console.log("autoRun:"+obj);
        for (var f in obj) {
            var isAutoRunAble;
            (f.substr(0, 4) == "init") ? isAutoRunAble = 1 : isAutoRunAble = 0;
            if (typeof (obj[f] == "function") && isAutoRunAble) {
                obj[f]();
            }
        }
    },
    //注册各页面的业务逻辑
    addRegistPage: function () {
        for (var x in arguments) {
            this.registPage.push(arguments[x]);
        }
        this.registPage = this.uniqueArray(this.registPage);
        //console.log(this.registPage);
    },
    //数组去重
    uniqueArray: function (ary) {
        ary.sort();
        var tmp = [ary[0]];
        for (var i = 1; i < ary.length; i++) {
            if (ary[i] !== tmp[tmp.length - 1]) {
                tmp.push(ary[i]);
            }
            else {
                console.log("webUI.registPage检查到重复的方法:" + ary[i].funName);
            }
        }
        return tmp;
    },
    //只执行通过addRegistPage方法已注册，且当前正在使用的页面 的 业务逻辑
    runRegistPage: function () {
        //console.log(webUI.registPage);
        for (var x in webUI.registPage) {
            var pageName = webUI.registPage[x].funName;
            var isCurrentPageActive = $("#" + pageName).length;
            if (isCurrentPageActive) {
                webUI.autoRun(webUI.registPage[x]);
            }

        }
    },
    //ajax加载头部和左侧导航栏
    initNavigator: function () {
     //   var hasNavigator = $(".navigator").length;
      //  var turl = 'http://assets.theme.gionee.com/apps/theme/freetribe/header/navigator.html';
      //  if (hasNavigator) {
        //    $(".navigator").load(turl, function () {
                webUI.initTopNav();
                webUI.initLeftNav();
                //ajax加载导航后，自动高亮当前页面的菜单栏，如不需自动高亮，注释下面两行代码
                var menuName = $(".navigator").attr("menuOn");//导航栏需要高亮的菜单栏
                console.log(menuName);
                webUI.highLightMenu(menuName);
         //   });

       // }
    },
    //设置导航栏需要高亮的菜单栏  eg:zt_taotu_taotuPack  分别高亮id=zt id=taotu id=taotuPack
    highLightMenu: function (menuOn) {
        //切割字符串，找到对应的id名称 高亮 二级导航还需额外加一个class=default
        var menuList = menuOn.split("_");
        //console.log(menuList[0]);
        for (var i = 0; i < menuList.length; i++) {
            $("#" + menuList[i]).addClass("on");
            //判断是主题还是壁纸，显示对应的二级菜单
            if (i == 0) {
                $("#" + menuList[i] + "Son").show();
            }
            //高亮的二级菜单如果还有子菜单，需展开并显示所有的三级菜单
            if (i == 1) {
                $("#" + menuList[i]).parent().addClass("default");
            }
        }

    },
    //头部导航交互效果
    initTopNav: function () {
        var hasNavTop = $(".nav_top").length;
        if (hasNavTop) {
            var navTopList = $(".nav_top").find("li");
            navTopList.on("click", function () {
                navTopList.removeClass("on");
                $(this).addClass("on");
                $(".left_menu_son").hide();
                var menuSonOn = $(this).attr("id") + "Son";
                $("#" + menuSonOn).show();
            });
        }

    },
    //左侧导航交互效果
    initLeftNav: function () {
        var hasNavLeft = $(".menu_left_label").length;
        if (hasNavLeft) {
            //主菜单交互
            $(".menu_left_label").on("click", function () {
                $(".menu_left_label").removeClass("on");
                $(this).addClass("on");
                $(".menu_lft_son").hide();
                $(this).parent().find(".menu_lft_son").show();

            });
            //子菜单交互
            $(".menu_lft_son").find("li").on("click", function () {
                $(".menu_lft_son").find("li").removeClass("on");
                $(this).addClass("on");
            });

        }
    },
    //模拟Select下拉框的效果
    initSelectBox: function () {
        var hasSelectBox = $(".select_val").length;
        if (hasSelectBox) {
            //点击区域非模拟下拉框时 关闭下拉框
            $("body").on("click", function (event) {
                var isSelectBox = $(event.target).parents(".select_box").length;
                if (isSelectBox == 0) {
                    $(".select_option").removeClass("show");
                    $(".select_val").removeClass("on");
                }
            });
            $(".select_val").on("click", function () {
                $(this).toggleClass("on");

                $(".select_option").hide();
                $(this).parent().find(".select_option").toggleClass("show");
            });
            $(".select_option").find("li").on("click", function () {
                var optionVal = $(this).html();
                
                var optionid = $(this).attr("val");
             
                $(this).parent().parent().find(".select_val").html(optionVal).attr("val",optionid);
                $(this).parent().removeClass("show");
                $(this).parent().siblings(".select_val").removeClass("on");
            });
        }
    },
    //颜色选择器交互效果
    initColorPicker: function () {
        var hasColorPicker = $('#taotuColorPicker').length;
        if (hasColorPicker) {
           // this.requireCss('css/farbtastic.css');
          //  this.requireJs('js/farbtastic.js');
            $('#taotuColorPicker').farbtastic('#taotuPickedColor');
        }
    },
    //标签选择器交互效果
    initTagList: function () {
        var hasTagList = $(".tag_list").length;
        if (hasTagList) {
            $(".tag_list").find("li").on("click", function () {
                //如果是一级标签则只能选中一个
                if ($(this).parent().hasClass("tag_one")) {
                    $(this).siblings().removeClass("on");
                }
                $(this).toggleClass("on");
            });

        }
    },
    //内容风格切换
    initSwitchBtns: function () {
        var hasSwitchBtns = $(".btn_switch").length;
        if (hasSwitchBtns) {

            $(".btn_switch").on("click", function () {
                //按钮 宫格 列表 只能亮一个
                $(this).siblings(".btn_switch").removeClass("on");
                $(this).addClass("on");
                //显示指定位置的内容
                var openId = $(this).attr("openId");
                $(".view").removeClass("show");
                $("#" + openId).addClass("show");
                //充值批量管理
                $(".txt_switch").removeClass("on");
                $(".view").removeClass("admin_mode");

                /*一直需要显示的就给view 添加calss=viewons admin_mode*/
                var tagViewon = $(".view").hasClass("viewons");
                if (tagViewon) {
                    $(".view").addClass("admin_mode");
                }
                ;
            });
            //文字 批量管理
            $(".txt_switch").on("click", function () {
                $(this).toggleClass("on");
            });
        }
    },
    //9宫格，鼠标移动上去显示操作按钮
    initViewPicsBtns: function () {
        var hasViewPicsBtns = $(".view_pics_btns").length;
        if (hasViewPicsBtns) {
            $(".pic_cont").on("mouseenter", function () {
                $(this).find(".view_pics_btns").show();
            });
            $(".pic_cont").on("mouseleave", function () {
                $(this).find(".view_pics_btns").hide();
            });

        }
    },
    //9宫格批量管理按钮交互，显示批量按钮和各子项目按钮
    initViewPackAdmin: function () {
        var hasViewPicsPackAdmin = $(".view").length;
        if (hasViewPicsPackAdmin) {
            $(".txt_switch").on("click", function () {
                $(".view.show").toggleClass("admin_mode");

            });
        }

    },
    //check 按钮选中/取消 效果
    initBtnCheck: function () {
        //var hasBtnCheck=$(".btn_check").length;
        var hasBtnCheck = 1;
        if (hasBtnCheck) {
            $("body").on("click", ".btn_check", function () {
                $(this).toggleClass("on");
            });
        }


    },
    //check 全选按钮 选中/取消 效果
    initCheckAll: function () {
        var hasBtnCheckAll = $(".btn_check_all").length;
        if (hasBtnCheckAll) {
            $(".btn_check_all").on("click", function () {
                var checkClass = $(this).attr("checkClass");
                $("." + checkClass).toggleClass("on");
            });
           
        }

    },
    //模拟radio选中效果
    initRadio: function () {
        var hasBtnRadio = $(".radio_wrap").length;
        if (hasBtnRadio) {
            $(".radio").on("click", function () {
                $(this).addClass("on");
                $(this).siblings().removeClass("on");
            });
        }
    },
    //标签删除按钮交互
    initTagDel: function () {

        var hasTagDel = $(".btn_tag").length;
        if (hasTagDel) {
            $(".tag_add_wrap").on("mouseenter", ".btn_tag", function () {
                $(this).find(".tag_del").show();
            });
            $(".tag_add_wrap").on("mouseleave", ".btn_tag", function () {
                $(this).find(".tag_del").hide();
            });
            //点击删除当前标签
            $(".tag_add_wrap").on("click", ".tag_del", function () {
                $(this).parent().remove();
            });
        }
    },
    //添加标签
    initTagAdd: function () {
        var hasTagAdd = $(".btn_tag_add").length;
        
    
        if (hasTagAdd) {
            $(".btn_tag_add_confirm").on("click", function () {
                var tagList = $(this).parents(".pop_cont_tags").find(".btn_tag.on");
                if (tagList.length > 0) {
                    tagList.each(function () {
                        $("<li class='btn_tag' subid="+$(this).attr("subid")+"><span>" + $(this).text() + "</span><i class='tag_del'></i></li>").insertBefore(".btn_tag_add");
                    });
                }
                $(".tag_add_wrap").find(".btn_tag").removeClass("on");
                $(this).parents(".pop_wraper").removeClass("show");

            });
            $(".btn_tag_add_cancel").on("click", function () {
                $(this).parents(".pop_wraper").removeClass("show");
                $(".tag_add_wrap").find(".btn_tag").removeClass("on");
            });

            $(".pop_cont_tags").on("click", ".pop_close", function () {
                $(".tag_add_wrap").find(".btn_tag").removeClass("on");
            });
        }
    },
    //富文本编辑器tinymec 暂时不用
    noinitTxtEditor: function () {
        var hasTxtEditor = $(".txt_editor").length;
        if (hasTxtEditor) {
            this.requireJs('plugins/tinymce/tinymce.min.js');
            //var editorUrl="<script language='javascript' src='js/tinymce/tinymce.min.js'></script>";
            //$("body").append(editorUrl);
            tinymce.init({
                selector: ".txt_editor",
                language: 'zh_CN',
                //toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons", 

            });
        }
    },
    //弹窗
    initPopUp: function () {
        //var hasPopTriger=$(".pop_btn").length;
        var hasPopTriger = 1;
        if (hasPopTriger) {
            $(".pop_wraper").unbind();
            $("body").on("click", ".pop_btn", function () {
             
                var valueKey = $(this).attr("valueKey");
              
              
                
                var openPopId = $(this).attr("openPopId");
                $(".btn_s2").attr("valueKey",valueKey);
                $(".pop_wraper").removeClass("show");
                $("#" + openPopId).addClass("show");
                webUI.enAblePopClickClose();
            });
            //var hasPopClose=$(".pop_close").length;
            var hasPopClose = 1;
            if (hasPopClose) {
                $("body").on("click", ".pop_close", function () {
                    $(".pop_wraper").removeClass("show");
                });

            }
            ;

            /*取消 添加class=btnCancel*/
            //var hasBtnCancel=$(".btnCancel").length;
            var hasBtnCancel = 1;
            if (hasBtnCancel) {
                $("body").on("click", ".btnCancel", function () {
                    $(".pop_wraper").removeClass("show");
                });

            }
            ;

        }

    },
    //弹窗 点击任意位置关闭
    enAblePopClickClose: function () {
        var isImgPop = $(".pop_style_img.show").length;
        if (isImgPop) {
            $(".pop_style_img.show").on("click", function () {
                $(this).removeClass("show");
            });

        }
    },
    //列表 根据颜色值显示颜色样例
    initColorPan: function () {
        var hasColorPan = $(".color_pan").length;
        if (hasColorPan) {
            $(".color_val").each(function () {
                var color = $(this).text();
                $(this).siblings(".color_pan").css("background", color);
            });

        }
    },
    //日期控件
    initDatePicker: function () {
        var hasDatePicker = $(".datepicker").length;
        
        if (hasDatePicker) {
           // this.requireCss('plugins/datepicker/jquery.datetimepicker.css');
           // this.requireJs('plugins/datepicker/jquery.datetimepicker.js');
            $('.datepicker').datetimepicker({
               
                value: packtime,
                lang: 'ch',
                format: 'Y-m-d H:i:s',
                formatDate: 'Y-m-d H:i:s',
            });
        }
    },
    //分页
    initPager: function () {
        $(".pageList a").on("click", function () {
              
        });

    },
    //缩略图重排 所有图片33.3%宽度
    imgSimpleRearrange: function () {
        $("#dragSortImgPack .img_list").removeAttr("style");
        $("#dragSortImgPack .img_list").find("img").css("width", "100%");
        $("#dragSortImgPack .img_list").css({
            "width": "calc(33.33% - 2px)",
            "float": "left",
            "margin-top": "2px",
            "margin-left": "2px"

        });
    },
    //缩略图重排 所有图片50%宽度
    imgSimpleRearrange2: function () {
        $("#dragSortImgPack .img_list").removeAttr("style");
        $("#dragSortImgPack .img_list").find("img").css("width", "100%");
        $("#dragSortImgPack .img_list").css({
            "width": "calc(50% - 2px)",
            "float": "left",
            "margin-top": "2px",
            "margin-left": "2px"

        });
    },
    //缩略图重排 6种排列方式
    imageRearrange: function () {
        var numPics = $("#dragSortImgPack .img_list").length;
        $("#dragSortImgPack .img_list").removeAttr("style");
        $("#dragSortImgPack .img_list").find("img").css("width", "100%");
        switch (numPics) {
            case 1:
                $("#dragSortImgPack .img_list:first").css("width", "100%");
                break;
            case 2:
                $("#dragSortImgPack .img_list:first").css({
                    "width": "calc(50% - 2px)",
                    "float": "left"
                });
                $("#dragSortImgPack .img_list:eq(1)").css({
                    "width": "calc(50% - 2px)",
                    "float": "right"
                });
                break;
            case 3:
                $("#dragSortImgPack .img_list:first").css("width", "100%");
                $("#dragSortImgPack .img_list:eq(1)").css({
                    "width": "calc(50% - 2px)",
                    "float": "left",
                    "margin-top": "4px"
                });
                $("#dragSortImgPack .img_list:eq(2)").css({
                    "width": "calc(50% - 2px)",
                    "float": "right",
                    "margin-top": "4px"
                });
                break;
            case 4:
                $("#dragSortImgPack .img_list:first").css("width", "100%");
                $("#dragSortImgPack .img_list:eq(1)").css({
                    "width": "calc(33.33% - 2px)",
                    "float": "left",
                    "margin-top": "4px"
                });
                $("#dragSortImgPack .img_list:eq(2)").css({
                    "width": "calc(33.33% - 2px)",
                    "float": "left",
                    "margin-left": "3px",
                    "margin-top": "4px"
                });
                $("#dragSortImgPack .img_list:eq(3)").css({
                    "width": "calc(33.33% - 2px)",
                    "float": "right",
                    "margin-top": "5px"
                });
                break;
            case 5:
                $("#dragSortImgPack .img_list:first").css("width", "calc(50% - 2px)");
                $("#dragSortImgPack .img_list:eq(1)").css({
                    "width": "calc(50% - 2px)",
                    "float": "right",
                    "margin-top": "0"
                });
                $("#dragSortImgPack .img_list:eq(2)").css({
                    "width": "calc(33.33% - 2px)",
                    "float": "left",
                    "margin-top": "4px",
                    "margin-left": "0",
                });
                $("#dragSortImgPack .img_list:eq(3)").css({
                    "width": "calc(33.33% - 2px)",
                    "float": "left",
                    "margin-left": "3px",
                    "margin-top": "4px"
                });
                $("#dragSortImgPack .img_list:eq(4)").css({
                    "width": "calc(33.33% - 2px)",
                    "float": "right",
                    "margin-top": "4px"
                });
                break;
            case 6:
                $("#dragSortImgPack .img_list:first").css("width", "100%");
                $("#dragSortImgPack .img_list:eq(1)").css({
                    "width": "calc(50% - 2px)",
                    "float": "left",
                    "margin-top": "4px"
                });
                $("#dragSortImgPack .img_list:eq(2)").css({
                    "width": "calc(50% - 2px)",
                    "float": "right",
                    "margin-top": "4px"
                });
                $("#dragSortImgPack .img_list:eq(3)").css({
                    "width": "calc(33.33% - 2px)",
                    "float": "left",
                    "margin-left": "0",
                    "margin-top": "4px"
                });
                $("#dragSortImgPack .img_list:eq(4)").css({
                    "width": "calc(33.33% - 2px)",
                    "float": "left",
                    "margin-left": "3px",
                    "margin-top": "4px"
                });
                $("#dragSortImgPack .img_list:eq(5)").css({
                    "width": "calc(33.33% - 2px)",
                    "float": "right",
                    "margin-top": "4px"
                });
                break;

        }
    },
    //壁纸选择进阶-拖拽交互效果
    imgDragSort: function () {
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
                    webUI.imageRearrange();
                }
                //如果已选壁纸被移动到了待选壁纸中
                if (this.attr("role") == "txtPack2" & this.parent().attr("id") == "dragSortTxtPack") {
                    //设定最新角色为待选壁纸
                    this.removeClass("on");
                    this.attr("role", "txtPack");
                    var targetImg = this.attr("targetImg");
                    //缩略图中移除对应图片
                    $("#dragSortImgPack").find("[targetImg=" + targetImg + "]").remove();
                    webUI.imageRearrange();
                }
            }
        });

        $("#dragSortImgPack").dragsort({
            dragSelector: ".img_list",
            dragBetween: true,
            dragEnd: function () {
                webUI.imageRearrange();
            }
        });
    },
    //壁纸选择基本交互效果
    imgBzListSort: function () {
        $(".drag_sort_list").on("click", "li", function () {
           // $(this).siblings().removeClass("on");
            $(this).toggleClass("on");
        });
        //按钮添加功能
        $("#packBzAdd").on("click", function () {
            if ($("#dragSortTxtPack").find(".on").length == 0) {
                alert("请先选择待选资源");
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
                    webUI.imageRearrange();
                });
            }
        });
        //按钮删除功能
        $("#packBzDel").on("click", function () {
            if ($("#dragSortTxtPack2").find(".on").length == 0) {
                alert("请先选择已选资源");
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
                    webUI.imageRearrange();
                });
            }
        });

    },
    //表单状态那列的点击值变成输入框修改
    initStatusEdit: function () {
        $(".view_list_tb").on("click", ".status_edit_able", function () {
            var objSpan = $(this);
            var status = $(this).text();
            var objInpt = $("<input type='text' class='rd_3'>");
            objInpt.val(status);
            $(this).replaceWith(objInpt);
            objInpt.focus();
            //输入框失去焦点时
            objInpt.on("blur", function () {
                objSpan.text($(this).val());
                $(this).replaceWith(objSpan);
            });
        });
    },
    //写入web存储数据
    setData: function (key, val) {
        window.localStorage.setItem(key, val);
    },
    //获取web存储数据
    getData: function (key) {
        return window.localStorage.getItem(key);
    },
    //删除web存储数据
    delData: function (key) {
        window.localStorage.removeItem(key);
    }

};

//加载主题的各页面交互效果
//document.write("<script language='javascript' src='<?php echo $staticPath ?>/jssdk/webgBusiness_zt.js'></script>");
//加载壁纸的各页面交互效果
//document.write("<script language='javascript' src='<?php echo $staticPath ?>/js/webgBusiness_bz.js'></script>");

$(function () {
    //注册主题各页面的业务逻辑
    webUI.addRegistPage(
            zt_usr_upload02,
            zt_usr_02,
            zt_admin_edit,
            zt_operation_03,
            zt_operation_04,
            zt_operation_05,
            zt_operation_07,
            zt_admin_tag
            );
    //注册壁纸各页面的业务逻辑
    webUI.addRegistPage(
            bz_admin_wp02,
            bz_admin_taotu_list02,
            bz_admin_taotu_pack01,
            bz_admin_taotu_pack02,
            bz_usr_upload02,
            bz_operation_daily_add01,
            bz_operation_daily_add02,
            bz_operation_seminar_add01,
            bz_operation_seminar_add02,
            bz_admin_tag,
            bz_operation_daily_list02,
            bz_operation_seminar_list02
            );
    
     //注册时钟各页面的业务逻辑
    webUI.addRegistPage(
            sz_operation_szsubject_add01,
            sz_operation_szsubject_add02
            );

    webUI.run();

});

