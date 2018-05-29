$(function(){
   $(".ch_status >li").click(function(){
       var status_id = $(this).attr("val");
       var sid = $(this).parent().parent().parent().attr("subjectid");
       var screemid = $(this).parent().parent().parent().attr("srceem");
       var data = "screemid="+screemid+"&status="+status_id+"&sid="+sid+"&token="+token;
       var url = "/Admin/subject/updatestatus";
       if(confirm("确定修改状态?")){
       $.post(url,data,function(d){
           if(d){
              history.go(0);
           }
       });
   }
       
   })
      $(".ch_status_select >li").click(function(){
       var status_id = $(this).attr("val");
       window.location.href="/Admin/Subject/index?status="+status_id;
      
   })
   
   //del_subject
   
   $(".delBtn").click(function(){
       var subjectid = $(this).attr("sid");
       $(".del_subject").click(function(e){
           var data = "id="+subjectid+"&token="+token;
           var url = "/Admin/Subject/delete";
           $(".pop_wraper").removeClass("show");
           
           $.post(url,data,function(d){
                 history.go(0);
           })
            stopPropagation(e)
       })
   })
   
   $(".preimg").click(function(){
       var url = $(this).parent().attr("img");
       $(".pop_img_src img").attr("src",url);
   })
    
    
})
