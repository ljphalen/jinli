$(function () {
    //一级标签的保存;
    $(".targ_save").click(function () {
        var name = $(this).parent().attr("vname");
        var tid = $(this).parent().attr("vid");
        var urlImage = $(this).parent().attr("vurl");
        var sort =$(this).parent().attr("sort");
        if (name == undefined)
            name = 0;
        if (tid == undefined)
            tid = 0;
        if (urlImage == undefined)
            urlImage = 0;
          if (sort == undefined)
            sort = 0;
        var url = "/Admin/wallpaperadmin/savetarg";
        var data = "urlImage=" + urlImage +"&sort="+sort+ "&token=" + token + "&name=" + name + "&tid=" + tid;
       
        $("#fixTipView").removeClass("show");
        $.post(url, data, function (d) {
            if (d.opt == "insert")
                window.location.href = "/Admin/Wallpaperadmin/typelist";
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

    $(".targ_del").click(function () {
        var tid = $(this).parent().attr("vid");
        $("#confirmYes").attr('valueKey', tid);
    });
    
    $(".addsort").change(function () {
        var name = $(this).val();
        $(this).parent().nextAll().last().attr("sort", name);

    });

    $("#confirmYes").click(function () {
        var tid = $(this).attr("ValueKey");
        var url = "/Admin/wallpaperadmin/delTarg";
        var data = "tid=" + tid + "&token=" + token;
        $.post(url, data, function () {

        });
    });

    //----------------------------------------------------------------//


    $(".addtarg2").change(function () {
        var name = $(this).val();
        var id = $(this).prev().val();
        var data = "name=" + name + "&id=" + id + "&token=" + token;
        var url = "/Admin/wallpaperadmin/optsubstarg";

        $.post(url, data, function (d) {
            if (d.opt == "insert")
                window.location.href = "/Admin/Wallpaperadmin/typelist";
        },"json");
    })

    $(".deltarg2").click(function () {
        var tid = $(this).attr("valid");
        var url = "/Admin/wallpaperadmin/delTargsub";
        var data = "tid=" + tid + "&token=" + token;
        $.post(url, data, function (d) {
            if (d)
                window.location.href = "/Admin/Wallpaperadmin/typelist";
        });
    })




})