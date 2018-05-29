$(function () {
    //----------------------------------------------------------------//
    $(".addtarg2").change(function () {
        var name = $(this).val();
        var id = $(this).prev().val();
      
        var data = "name=" + name + "&id=" + id + "&token=" + token;
        var url = '';
        
        if(id ==undefined){
              url = "/Admin/Searchwords/addWords";
        }else{
              url = "/Admin/Searchwords/editWords";
        }
        $.post(url, data, function (d) {
            if (d.opt == "insert"){
               history.go(0);
          }
        },"json");
    });
    $(".addsort2").change(function(){
          var id = $(this).parent().prev().find(".subtarg_id").val();
          var sort = $(this).val();
        var data = "sort=" + sort + "&id=" + id + "&token=" + token;
        

        var url = "/Admin/Searchwords/editSort";
          $.post(url, data, function () {});
    })
    //主题二级标签处理;
    $(".deltarg2").click(function () {
        var tid = $(this).attr("valid");
        var url = "/Admin/searchwords/delWords";
        var data = "id=" + tid + "&token=" + token;
        
        if(confirm("你确定要删除吗?")){
        $.post(url, data, function (d) {
            if (d){
               history.go(0);
            }
        });
        }
    })




})