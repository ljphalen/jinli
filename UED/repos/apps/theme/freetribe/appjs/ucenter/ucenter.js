$(function(){
    /* 
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    var swf = new fullAvatarEditor(
            fullAvatarEditor_swf_url,
            expressInstall_swf_url, 
            "swfContainer", 
            {
                id : 'swf',
                upload_url : fullAvatar_upload_url,	//上传接口
                method : 'post',	//传递到上传接口中的查询参数的提交方式。更改该值时，请注意更改上传接口中的查询参数的接收方式
                src_upload : 0,		//是否上传原图片的选项，有以下值：0-不上传；1-上传；2-显示复选框由用户选择
                avatar_box_border_width : 0,
                avatar_sizes : '140*140',
                avatar_sizes_desc : '140*140像素'
            }, function (msg) {
                switch(msg.code)
                {
                        //case 1 : alert("页面成功加载了组件！");break;
                        case 2 : 
                                //alert("已成功加载图片到编辑面板。");
                                //document.getElementById("upload").style.display = "inline";
                                break;
                        case 3 :
                                if(msg.type == 0)
                                {
                                        //alert("摄像头已准备就绪且用户已允许使用。");
                                }
                                else if(msg.type == 1)
                                {
                                        alert("摄像头已准备就绪但用户未允许使用！");
                                }
                                else
                                {
                                        alert("摄像头被占用！");
                                }
                        break;
                        case 5 : 
                                if(msg.type == 0)
                                {
                                    try{
                                        var uri = msg.content.avatarUrls[0];
                                        $("#iconDemo").find("img").attr('src', user_icon_url+'/'+uri);
                                        $("#iconDemo").find("img").attr('uri', uri);
                                        $("#iconDemo").show();
                                        $(".pop_close").click();
                                    } catch(e){}
                                }
                        break;
                }
        }
    );
    
    $("input[name='nick_name']").focus(function(){
        if($(this).val().length == 0){
            $(".input-tips").show();
        }
    })
    
    $("#saveBtn").click(function(){
        var nick_name = $("input[name='nick_name']").val();
        var sex = $("#sex").find('li').filter('.on').attr('val');
        var userIcon = $("#iconDemo").find("img").attr('uri');
        var userId = $("input[name='userId']").val();
        if(nick_name.length==0){
            alert("昵称不能为空");
            return false;
        }
        if(nick_name.length<2 || nick_name.lenght>14){
            alert("昵称长度应该大于2，小于14");
            return false;
        }
        if(!/^\w+$/.test(nick_name)){
            alert("昵称含有非法字符");
            return false;
        }
        if($("#sex").find('li').filter('.on').size()==0){
            alert("请选择性别");
            return false;
        }
        if(!userIcon){
            alert("请上传头像");
            return false;
        }

        $.ajax({
          type: 'POST',
          url: edit_user_url,
          data: {'nick_name': nick_name, 'sex': sex, 'userIcon': userIcon, 'userId': userId, 'token': token},
          success: function(data){
               if(data == 1){
                   window.location.href=theme_index_url;
               } else {
                   alert("更新失败，请稍后重试");
               }
          },
          error: function(){
              alert("网络繁忙，请稍后重试"); 
          }
        });
      
    });
    
});