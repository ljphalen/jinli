$(function () {

    $(".edit_ok").click(function () {
        $(".pop_wraper").removeClass("show");
        var name = $("input").val();
        var wall_id = $("#wallid").attr("wallid");
        var url = "/Admin/wallpapermy/updatesName";
        var data = "name=" + name + "&wall_id=" + wall_id + "&token=" + token;
        $.post(url, data, function (d) {
            if (d) {
                window.location.href = "/Admin/wallpapermy/index";
            } else {
                alert("提交失败！请重新操作");
                return 0;
            }
        });

    });
//////////////////////////
     var wall_id = $(this).attr("valueKey");
        $(".pop_wraper").removeClass("show");
        var data = "wall_id=" + wall_id + "&token=" + token;
        var url = "/Admin/wallpapermy/delwallpaper";
        $.post(url, data, function (d) {
            if (!d)
                alert("fliask");
        });
    


    $(".select_option >li").click(function () {

        var wallId = $(this).parent().attr("wallId");
        var targid = $(this).val();
        var data = "wall_id=" + wallId + "&targid=" + targid + "&token=" + token;
        var url = "/Admin/wallpapermy/addpapertag";
        $.post(url, data, function () {
        });
    })


})

