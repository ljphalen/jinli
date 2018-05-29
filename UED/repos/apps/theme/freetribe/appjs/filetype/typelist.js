$(function () {

    //一级标签的保存;
    $(".targ_save").click(function () {
        var name = $(this).parent().attr("vname");
        var tid = $(this).parent().attr("vid");
        var sort = $(this).parent().attr("sort");
        var urlImage = $(this).parent().attr("vurl");
        if (name == undefined)
            name = 0;
        if (tid == undefined)
            tid = 0;
        if (sort == undefined)
            sort = 0;
        if (urlImage == undefined)
            urlImage = 0;
        var url = "/Admin/filetype/seaveTarg";
        var data = "urlImage=" + urlImage + "&token=" + token + "&name=" 
                + name + "&tid=" + tid+"&sort="+sort;
        $("#fixTipView").removeClass("show");
        $.post(url, data, function (d) {
            if (d.opt == "insert"){
                window.location.href = "/Admin/filetype/index";
            }
        }, "json");
    });

    $("#saveconfirmYes").click(function () {
        $(".pop_wraper").removeClass("show");
    });

    //添加名字;
    $(".tag_input").change(function () {
        var name = $(this).val();
        $(this).parent().nextAll().last().attr("vname", name);

    });
    
     $(".addsort").change(function () {
        var name = $(this).val();
        $(this).parent().nextAll().last().attr("sort", name);

    });

    $(".targ_del").click(function () {
        var tid = $(this).parent().attr("vid");
        $("#confirmYes").attr('valueKey', tid);
    })

    $("#confirmYes").click(function () {
        var tid = $(this).attr("ValueKey");
        var url = "/Admin/filetype/delete";
        var data = "id=" + tid + "&token=" + token;
        $.post(url, data, function () {

        });
    });

    //----------------------------------------------------------------//


    $(".addtarg2").change(function () {
        var name = $(this).val();
        var id = $(this).prev().val();
        var data = "name=" + name + "&id=" + id + "&token=" + token;
        var url = "/Admin/Filetype/setThemeSubTarg";
        $.post(url, data, function (d) {
            if (d.opt == "insert")
                window.location.href = "/Admin/filetype/index";
        },"json");
    });
    $(".addsort2").change(function(){
          var id = $(this).parent().prev().find(".subtarg_id").val();
          var sort = $(this).val();
        var data = "sort=" + sort + "&id=" + id + "&token=" + token;
        var url = "/Admin/Filetype/setThemeSubTarg";
          $.post(url, data, function () {});
    })
    //主题二级标签处理;
    $(".deltarg2").click(function () {
        var tid = $(this).attr("valid");
        var url = "/Admin/Filetype/delTargsub";
        var data = "tid=" + tid + "&token=" + token;
        
      
        $.post(url, data, function (d) {
            if (d){
                window.location.href = "/Admin/filetype/index";
            }
        });

    })




})