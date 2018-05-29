$(function () {
    $(":input[class=rd_1]").change(wallpapername_chang);

    $(".select_box>ul>li").click(wallpapertarg_chang);
    
    $(".btn_s2").click(wallpaper_del);

    function wallpaper_del(){
        var wall_id = $(this).attr("valueKey");
         $(".pop_wraper").removeClass("show");
         var data = "wall_id="+wall_id+"&token="+token;
         var url = "/Admin/wallpapermy/delwallpaper";
         $.post(url,data,function(d){
             if(!d)alert("fliask");
         });
    }


    function wallpapername_chang() {
        var name = $(this).val();
        var wall_id = $(this).next().val();
        var url = "/Admin/wallpapermy/updatesName";
        var data = "name=" + name + "&wall_id=" + wall_id + "&token=" + token;
        $.post(url, data, function () {
        });
    }


    function wallpapertarg_chang(){
        var targid = $(this).attr("value");
        var wall_id =$(this).parent().parent().parent().prev().children("input.wp_name").val();
        var url = "/Admin/wallpapermy/addpapertag";
        var data = "targid=" + targid + "&wall_id=" + wall_id + "&token=" + token;
        $.post(url, data, function () {
        });
    }
});