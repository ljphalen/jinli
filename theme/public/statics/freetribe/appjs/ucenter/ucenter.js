$(function(){
    //点击个人或者企业设计师，显示其相应的内容
    $('#designer_type li').on('click',function(){
           var $this = $(this);
           var typeNum = $this.attr('data-type');
           if(typeNum ==1){
                $('.info_cont').removeClass('none');
                $('.companyInfo').addClass('none');
           }else
           if(typeNum ==0){
                $('.info_cont').addClass('none');
                $('.companyInfo').removeClass('none');
           }
    })

    //id: 上传id, up_url： 上传url, filename: 文件存储路径
    function init_upload(id, up_url, filename){  
        $("#"+id+'_btn').on('click', function(event) {  
            $("#"+id+"_input").replaceWith('<input id="'+id+'_input" style="display: none" type="file" name="'+id+'_input">');
            $('#'+id+"_input").click(); 
            event.preventDefault();
        }); 

        //选择文件之后执行上传
        $('body').delegate('#'+id+"_input",'change', function() {  
            var allowImgageType = ['jpg', 'jpeg', 'png', 'gif'];
            var file = $("#"+id+"_input").val();
            //获取大小
            var byteSize = $("#"+id+"_input")[0].files[0].size;
            byteSize = Math.ceil(byteSize / 1024) //KB
            //获取后缀
            if (file.length > 0) {
                if(byteSize > 4096) {
                    alert("上传的附件文件不能超过4M");
                    return;
                }
                var pos = file.lastIndexOf(".");
                //截取点之后的字符串
                var ext = file.substring(pos + 1).toLowerCase();
                //console.log(ext);
                if($.inArray(ext, allowImgageType) != -1) {
                    createUpload();
                }else {
                    alert("请选择jpg,jpeg,png,gif类型的图片");
                }
            }
            else {
                alert("请选择jpg,jpeg,png,gif类型的图片");
            }
        });  

         function createUpload(){
            $.ajaxFileUpload({
                url: up_url,  
                secureuri:true,  
                fileElementId:id+"_input",//file标签的id  
                dataType: 'json',//返回数据的类型  
                data: {'token': token},//一同上传的数据  
                success: function (data, status) {  
                    var src = icon_url + '/'+filename+'/' +  data.path+'?t='+(new Date().getTime());
                    $("input[name='"+id+"']").val(data.path);
                    $("#"+id+"_demo").find('a').attr('href', src);
                    $("#"+id+"_demo").show();
                }, 
                error: function (data, status, e) {  
                    alert(e);  
                }  
            });  
        }
    }

    //点击打开文件选择器, 上传身份证照
    init_upload('uploadIdPic', upload_id_url, 'idcardIcon');
    //上传营业执照
    init_upload('uploadLicensePic', upload_license_url, 'licenseIcon');
    //上传税务登记照
    init_upload('uploadTaxPic', upload_tax_url, 'taxIcon');
    
    //点击打开文件选择器  
    $("#uploadBtn").on('click', function() {  
        $("#fileToUpload").replaceWith('<input id="fileToUpload" style="display: none" type="file" name="upfile">');
        $('#fileToUpload').click();  
    });  
      
    //选择文件之后执行上传
    $('body').delegate('#fileToUpload','change', function() {  
        var allowImgageType = ['jpg', 'jpeg', 'png', 'gif'];
        var file = $("#fileToUpload").val();
        //获取大小
        var byteSize = $("#fileToUpload")[0].files[0].size;
        byteSize = Math.ceil(byteSize / 1024) //KB
        //获取后缀
        if (file.length > 0) {
            if(byteSize > 10240) {
                alert("上传的附件文件不能超过10M");
                return;
            }
            var pos = file.lastIndexOf(".");
            //截取点之后的字符串
            var ext = file.substring(pos + 1).toLowerCase();
            //console.log(ext);
            if($.inArray(ext, allowImgageType) != -1) {
                createUpload();
            }else {
                alert("请选择jpg,jpeg,png,gif类型的图片");
            }
        }
        else {
            alert("请选择jpg,jpeg,png,gif类型的图片");
        }
        event.preventDefault();
    });  

    //头像裁剪
    var jcrop_api, boundx, boundy;

    function createUpload(){
        $.ajaxFileUpload({
            url: upload_icon_url,  
            secureuri:true,  
            fileElementId:'fileToUpload',//file标签的id  
            dataType: 'json',//返回数据的类型  
            data: {'token': token},//一同上传的数据  
            success: function (data, status) {  
                var src = icon_url + '/userIcon/' +  data.path+'?t='+(new Date().getTime());
        
                var uploadImageCallback = function (){
                    if(img.width<100 || img.height<100){
                        alert("头像宽高要大于100像素");
                        return false;
                    }
                    $("#select_img").val( data.path );
                    $("#selectImg").attr("src", src);
                    //$("#selectImg").attr("width", 240);
                    //$("#selectImg").attr("height", 360);
                    $("#selectImg").css("width", img.width + "px");
                    $("#selectImg").css("height", img.height + "px");

                    $("#showSelectIconBtn").click();
                    if(jcrop_api){
                        jcrop_api.destroy();
                    }
                    $('#selectImg').Jcrop({
                        minSize: [100,100],
                        bgOpacity: 0.5,
                        bgColor: 'white',
                        canDelete: false,
                        onSelect: updateCoords,
                        allowSelect: false,
                        aspectRatio: 1
                    },
                    function(){
                        jcrop_api = this;
                        var x1 = img.width/2-70;
                        var y1 = img.height/2-70;
                        var x2 = img.width/2+70;
                        var y2 = img.height/2+70;
                        //console.log(x1, y1, x2, y2);
                        jcrop_api.setSelect([x1, y1, x2, y2]);
                        var bounds = this.getBounds();
                        boundx = bounds[0];
                        boundy = bounds[1];
                    }); 
                };

                var img = new Image();
                img.src = src;
                if(img.complete){
                    uploadImageCallback();
                    return ;
                } else{
                    img.onload = function(){
                        uploadImageCallback();
                        img.onload = null;
                    };
                    img.onerror = function(){
                    };
                }
                return false;
            }, 
            error: function (data, status, e) {  
                alert(e);  
            }  
        });  
    }
        
    function updateCoords(c)
    {
        $('#select_x').val(c.x);
        $('#select_y').val(c.y);
        $('#select_w').val(c.w);
        $('#select_h').val(c.h);
    };
    function checkCoords()
    {
        if (parseInt($('#select_w').val())) return true;
        alert('请选择图片上合适的区域');
        return false;
    };
    function updatePreview(c){
        if (parseInt(c.w) > 0){
            var rx = 112 / c.w;
            var ry = 112 / c.h;
            $('#preview').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
        {
            var rx = 130 / c.w;
            var ry = 130 / c.h;
            $('#preview2').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
        {
            var rx = 200 / c.w;
            var ry = 200 / c.h;
            $('#preview3').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
    };
    
    $("#avatarSubmitBtn").click(function(){
        var img = $("#select_img").val();
        var x = $("#select_x").val();
        var y = $("#select_y").val();
        var w = $("#select_w").val();
        var h = $("#select_h").val();
        if( checkCoords() ){
            $.ajax({
                type: "POST",
                url: resize_icon_url,
                data: {"img":img,"x":x,"y":y,"w":w,"h":h,"token": token},
                dataType: "json",
                success: function(msg){
                    if( msg.result_code == 1 ){
                        var  src = icon_url + '/userIcon/' + msg.result_des+'?t='+(new Date().getTime());
                        $('#iconDemo').find("img").attr('src', src);
                        $('#iconDemo').find("img").attr('uri', msg.result_des);
                        $('#iconDemo').show();
                        $(".btnCancel").click();
                    } else {
                        alert("失败");
                    }
                }
            });
        }
    });
    
    //输入银行卡号
    $("input[name='card_number']").keyup(function(){
        var num = $(this).val().replace(/\s/g,'').replace(/(\d{4})(?=\d)/g,"$1 ");
        $(this).val(num);
    });

    $("input[name='check_card_number']").keyup(function(){
        var num = $(this).val().replace(/\s/g,'').replace(/(\d{4})(?=\d)/g,"$1 ");
        $(this).val(num);
    });

    $("input[name='company_card_number']").keyup(function(){
        var num = $(this).val().replace(/\s/g,'').replace(/(\d{4})(?=\d)/g,"$1 ");
        $(this).val(num);
    });

    $("input[name='company_check_card_number']").keyup(function(){
        var num = $(this).val().replace(/\s/g,'').replace(/(\d{4})(?=\d)/g,"$1 ");
        $(this).val(num);
    });
   
    jQuery.validator.addMethod('equalToCardNumber', function(value, element,param){ 
        var ori_card_number = $("input[name='ori_card_number']").val();
        var card_number = $("input[name='card_number']").val();
        var check_card_number = $("input[name='check_card_number']").val();
        
        if(ori_card_number != card_number){
            return check_card_number == card_number;
        } else {
            if(check_card_number != ''){
                return check_card_number == card_number;
            } else {
                return true;
            }
        }
    }, '两次输入的银行卡号不一致');

    jQuery.validator.addMethod('required', function(value, element){ 
        if(undefined === $(element).attr('holder') && value == '') {
            return false;
        } else if(value == $(element).attr('holder')){
            return false;
        } else {
            return true;
        }
    }, 'required');

    var user_validator = $("#userInfo").validate({
        rules: {
            nick_name: {
                required: true,
                rangeLengthCN: [2,14],
                remote: {    
                    url: check_nickname_url,  
                    type: "post",              
                    dataType: "json",    
                    data: {       
                        nick_name: function() {    
                            return $("input[name='nick_name']").val();  
                        }, 
                        token: token  
                    }    
                }    
            },
            sex: {
                radioCheck: true
            }
        },
        messages: {
            nick_name: {
                required: "昵称不能为空",
                rangeLengthCN: "昵称长度为2-14个字",
                remote: "昵称已经使用，请重新填写您的昵称"
            },
            sex: {
                radioCheck: "请选择性别"
            },
        }
    });
    
    var personal_validator = $("#personalInfo").validate({
        rules: {
            real_name:{
                required: true,
                rangeLengthCN: [2,30]
            },
            id_number:{
                required: true,
                idnumberCheck: true 
            },
            email:{
                required: true,
                email: true 
            },
            tel:{
                required: true
            },
            bank:{
                selectCheck:true
            },
            account_name:{
                required: true,
                rangeLengthCN: [2,30]
            },
            branch:{
                required: true,
                rangeLengthCN: [2,100]
            },
            card_number:{
                required: true
            },
            check_card_number:{
                required: true,
                equalTo: "#card_number"
                //equalToCardNumber: "#card_number"
            },
            id_url:{
                required: true
            },
            bank_company:{
                selectCheck: true,
            },
            accout_company:{
                required: true,
            },
            provice_company:{
                required:true,
            },
            city_company:{
                required:true,
            },
            branch_company:{
                required:true,
            },

        },
        messages: {
            real_name:{
                required: "请输入您的真实姓名",
                rangeLengthCN: "长度为2-30字"
            },
            id_number:{
                required: "请输入您的身份证号码",
                idnumberCheck: "身份证号码不正确，请重新输入" 
            },
            email:{
                required: "请输入您的邮箱地址",
                email: "请输入正确格式的电子邮件"
            },
            tel:{
                required: "请输入您的手机号码"
            },
            bank:{
                selectCheck: "请选择开户行名称"
            },
            account_name:{
                required: "请输入您的开户名称",
                rangeLengthCN: "长度为2-30字"
            },
            branch:{
                required: "请输入您的开户支行",
                rangeLengthCN: "长度为2-100字"
            },
            card_number:{
                required: "请输入您的银行卡号"
            },
            check_card_number:{
                required: "请确认您的银行卡呈号",
                equalTo: "两次输入的银行卡号不一致"
                //equalToCardNumber: "两次输入的银行卡号不一致"
            },
            id_url:{
                required: "请上传您的身份证照"
            }
        }
    });
    

    var company_validator = $("#companyInfo").validate({
        rules: {
            company_real_name:{
                required: true,
                rangeLengthCN: [2,30]
            },
            company_tel:{
                required: true
            },
            company_email:{
                required: true,
                email: true 
            },
            company_qq:{
                required: true
            },
            company_name:{
                required: true,
                rangeLengthCN: [2,32]
            },
            license:{
                required: true
            },
            license_pic:{
                required: true
            },
            tax_number: {
                required: true
            },
            tax_pic:{
                required: true
            },
            company_addr:{
                required: true
            },
            company_bank:{
                selectCheck:true
            },
            company_account_name:{
                required: true,
                rangeLengthCN: [2,30]
            },
            company_bank_province:{
                required: true
            }, 
            company_bank_city: {
                required: true
            },
            company_branch:{
                required: true,
                rangeLengthCN: [2,100]
            },
            company_card_number:{
                required: true
            },
            company_check_card_number:{
                required: true,
                equalTo: "#company_card_number"
            },
        },
        messages: {
            company_real_name:{
                required: "请输入您的真实姓名",
                rangeLengthCN: "长度为2-30字"
            },
            company_tel:{
                required: "请输入您的手机号码"
            },
            company_email:{
                required: "请输入您的邮箱地址",
                email: "请输入正确格式的电子邮件"
            },
            company_qq:{
                required: "请输入您的QQ号码"
            },
            company_name: {
                required: "请输入公司名",
                rangeLengthCN: "长度为2-32字"
            },
            license:{
                required: "请输入营业执照注册号"
            },
            license_pic:{
                required: "请上传营业执照扫描件"
            },
            tax_number:{
                required: "税务登记证号"
            },
            tax_pic:{
                required: "请上传税务登记扫描件"
            },
            company_addr:{
                required: "请输入公司地址"
            },
            company_bank:{
                selectCheck: "请选择开户行名称"
            },
            company_account_name:{
                required: "请输入您的开户名称",
                rangeLengthCN: "长度为2-30字"
            },
            company_bank_province : {
                required: "请输入您的开户行所在省份"
            }, 
            company_bank_city: {
                required: "请输入您的开户行所在城市"
            },
            company_branch:{
                required: "请输入您的开户支行",
                rangeLengthCN: "长度为2-100字"
            },
            company_card_number:{
                required: "请输入您的银行卡号"
            },
            company_check_card_number:{
                required: "请确认您的银行卡呈号",
                equalTo: "两次输入的银行卡号不一致"
                //equalToCardNumber: "两次输入的银行卡号不一致"
            },
        }
    });
    
    $("#saveBtn").click(function(){
        var designer_type = $("#designer_type").find('li').filter('.on').attr('val');
        var r_user_validator = user_validator.form();
        if(designer_type == 1){
            var r_valid = company_validator.form();
        } else {
            var r_valid = personal_validator.form();
        }
        if(r_user_validator && r_valid){
            $("#hideSaveBtn").click();   
        }
        return false;
    });

    $("#submitBtn").click(function(){
        var designer_type = $("#designer_type").find('li').filter('.on').attr('val');
        //基本信息
        var nick_name = $("input[name='nick_name']").val();
        var sex = $("#sex").find('li').filter('.on').attr('val');
        var userIcon = $("#iconDemo").find("img").attr('uri');
        var userId = $("input[name='userId']").val();

        //支付必备信息
        if(designer_type == 1){
            var real_name = $("input[name='company_real_name']").val();
            var tel = $("input[name='company_tel']").val();
            var email = $("input[name='company_email']").val();
            var qq = $("input[name='company_qq']").val();
            var company_name = $("input[name='company_name']").val();
            var license = $("input[name='license']").val();
            var license_pic = $("input[name='uploadLicensePic']").val();
            var tax_number = $("input[name='tax_number']").val();
            var tax_pic = $("input[name='uploadTaxPic']").val();
            var company_addr = $("input[name='company_addr']").val();
            var bank = $("#company_bank").find(".check_data").attr("val");
            var account_name = $("input[name='company_account_name']").val();
            var bank_province = $("input[name='company_bank_province']").val();
            var bank_city = $("input[name='company_bank_city']").val();
            var branch = $("input[name='company_branch']").val();
            var card_number = $("input[name='company_card_number']").val();
            var check_card_number = $("input[name='company_check_card_number']").val();
            
            var json_data = {
                'nick_name': nick_name, 'sex': sex, 'userIcon': userIcon, 'userId': userId, 'designer_type': designer_type, 
                'real_name': real_name, 'tel': tel, 'email': email, 'qq': qq, 'company_name': company_name,
                'license': license, 'license_pic': license_pic, 'tax_number': tax_number, 'tax_pic': tax_pic,
                'company_addr': company_addr, 'bank': bank, 'account_name': account_name, 
                'bank_province': bank_province, 'bank_city': bank_city,
                'branch': branch, 'card_number': card_number, 'id_url': id_url,
                'token': token
            };
        } else {
            var real_name = $("input[name='real_name']").val();
            var id_number = $("input[name='id_number']").val();
            var email = $("input[name='email']").val();
            var tel = $("input[name='tel']").val();
            var bank = $("#bank").find(".check_data").attr("val");
            var account_name = $("input[name='account_name']").val();
            var branch = $("input[name='branch']").val();
            var card_number = $("input[name='card_number']").val();
            var check_card_number = $("input[name='check_card_number']").val();
            var ori_card_number = $("input[name='ori_card_number']").val();
            var id_url = $("input[name='uploadIdPic']").val();
            
            var json_data = {
                'nick_name': nick_name, 'sex': sex, 'userIcon': userIcon, 'userId': userId, 'designer_type': designer_type, 
                'real_name': real_name, 'id_number': id_number, 'email': email, 'tel': tel,
                'bank': bank, 'account_name': account_name, 'branch': branch, 'card_number': card_number,
                'id_url': id_url,
                'token': token
            };
        }

        $.ajax({
          type: 'POST',
          url: edit_user_url,
          data: json_data,
          success: function(data){
               if(data == 1){
                   window.location.href=theme_index_url;
               } else if(data == -1) {
                   alert("昵称已经使用，请重新填写您的昵称");
               } else {
                   alert("更新失败，请稍后重试");
               }
          },
          error: function(){
              alert("网络繁忙，请稍后重试"); 
          }
        });
    });
 
})

//利用placeholder插件给相对应的属性赋值
$(function(){
    $('#account_name').placeholder({
        word: '开户名称必须和真实公司名称一致'
    });
    $('#branch').placeholder({
        word: '如不清楚请联系银行'
    });
    $('#company_name').placeholder({
        word: '与银行卡账户名一致,提交后不可更改'
    });
    $('#company_addr').placeholder({
        word: '联系地址不超过两百字'
    });
    $('#company_account_name').placeholder({
        word: '开户名称必须和真实公司名称一致'
    });
    $('#bank_province').placeholder({
        word: '如不清楚请联系银行'
    });
    $('#bank_city').placeholder({
        word: '如不清楚请联系银行'   
    });
    $('#company_branch').placeholder({
        word: '如不清楚请联系银行'
    });
});
