/*
 * @Author : hoff
 * 2012-04-05
 * 
 */

/*--------------------------------------------------------------
 * 验证表单值是否已经存在
 --------------------------------------------------------------*/

    //处理loding显示
    function ck_loder(loader,msg,result,error_callback,right_callback,callback)
    {
        
        
        /*成功:
            1.显示可以注册  loaderid.html = ''
            2.提交按钮可用
        */
        if('yes'==result){
            $('#'+loader).html('<font color="red">'+msg+'已存在</font>');
            error_callback();
            
        }else if('no'==result){
        //alert($('#'+loader).html);
            $('#'+loader).html('<font color="green">'+msg+'可以使用</font>');
           right_callback();
        }else{
            $('#'+loader).html('<font color="red">'+msg+'验证失败</font>');
             error_callback();
        }
        
        callback();
        /*失败：
            1.显示不可注册
            2.提交按钮不可用
        */
    
    }

    //检测用户名是否已存在
    function ck_isset(action,condition,callback){
        var url='';
        var data='';
        
        switch(action){
            case 'username':
                url = '/index.php?ac=check';
                break;
            case 'name':
                url = '/index.php?ac=check';
                break;
            case 'clientid':
                url = '/index.php?ac=check';
                break;
            case 'clientids':
                url = '/index.php?ac=check';
                break;
            case 'email':
                url = '/index.php?ac=check';
                break;
        }
        data = 'value='+condition+'&fieldname='+action;
        $.ajax({
           type: "POST",
           url: url,
           data: data,
           success: function(msg){
                callback && callback(msg);
           }
        }); 
    };


