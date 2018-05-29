<?php /* Smarty version Smarty-3.0.6, created on 2013-08-20 09:58:53
         compiled from "D:\www\trunk\agent.com/template/default/index_user_add.html" */ ?>
<?php /*%%SmartyHeaderCode:134585212cd5ddfffc6-56998461%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dbfc462498d060af04625eab690157e62994dfdd' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_user_add.html',
      1 => 1376963928,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '134585212cd5ddfffc6-56998461',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include 'D:\www\trunk\agent.com\plugin\smartys\plugins\function.html_options.php';
?><?php  $_config = new Smarty_Internal_Config("site.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars(null, 'local'); ?> <?php $_template = new Smarty_Internal_Template("public/pageHead.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<link href="/template/default/css/validate.css" rel="stylesheet" type="text/css" />
<link href="/template/default/css/autocomplete.css" rel="stylesheet" type="text/css" />
<script src="/template/default/js/jquery-1.4.2-min.js" type="text/javascript" language="javascript"></script>
<script src="/template/default/js/jquery.validate.js" type="text/javascript" language="javascript"></script>
<script src="/template/default/js/validate_exist.js" type="text/javascript" language="javascript"></script>
<script src="/template/default/js/autocomplete.js" type="text/javascript"></script>
<script type="text/javascript">
function len(s) {
  var l = 0;
  var a = s.split("");
   for (var i=0;i<a.length;i++) {
     if (a[i].charCodeAt(0)<299) {
       l++;
     } else {
       l+=2;
     }
   }
  return l;
}
jQuery.validator.addMethod("isLenName", function(value, element){   
	   var length = len(value);
	   return length>=4&&length<=50 ;
   }, "长度为4-50个字符");
jQuery.validator.addMethod("isLenNickname", function(value, element){   
	   var length = len(value);
	   return length>=4&&length<=16 ;
   }, "长度为4-16个字符");
   
$(document).ready(function() {
	
	$("#theForm").validate({
		ignore:".ignore",
		rules: {
			levels: "required",
			username:{
				required:true,
				minlength:5,
				maxlength:10
			},
           <?php if ($_smarty_tpl->getVariable('action')->value!='update'){?>
			userpass:{
				required:true,
				minlength:6,
				maxlength:32
			},
			repass:{
				required:true,
				equalTo:"#userpass"
			},
            <?php }else{ ?>
			userpass:{
				required:false,
				minlength:6,
				maxlength:32
			},
			repass:{
				required:false,
				equalTo:"#userpass"
			},
            <?php }?>
			nickname:{
				required:true,
				//isLenNickname:true,
				minlength:2,
				maxlength:9
			},
			//公司名称
			name:{
				required:true,
				isLenName:true,
				minlength:2,
				maxlength:50
			},
			clientid:{
				required:true,
				digits:true
			},
			clientids:{
				required:true,
				digits:true,
				range:[1,9999]
			},
			intoratio:{
				required:true,
				range:[0,9.99]
			},
            phone:{
                digits:true
            },
            mobile:{
                digits:true
            },
            email:{
                required:false,
                email:true
            },
            postcode:{
                required:false,
                minlength:6,
				maxlength:6,
                digits:true
            }
            
            
		},
		messages: {
			levels: "请选择角色",
			username:{
				required:"请输入帐号",
				minlength:"最小5位",
				maxlength:"最大10位"
			},
            <?php if ($_smarty_tpl->getVariable('action')->value!='update'){?>
			userpass:{
				required:"请输入密码",
				minlength:"最小6位",
				maxlength:"最大32位"
			},
			repass:{
				required:"请输入确认密码",
				equalTo:"确认密码与密码不相同"
			},
            <?php }else{ ?>
			userpass:{
				minlength:"最小6位",
				maxlength:"最大32位"
			},
			repass:{
				equalTo:"确认密码与密码不相同"
			},
            <?php }?>
			nickname:{
				required:"请输入姓名",
				minlength:"最小2个字",
				maxlength:"最大9个字"
			},
			
			name:{
				required:"请输入公司名称",
				minlength:"最小4个字",
				maxlength:"最大50位"
			},
			clientid:{
				required:"请输入父渠道号(单击文本框可选择)"
			},
			clientids:{
				required:"请输入渠道号",
                digits:"必须输入整数",
				range:"1-9999之间的数字"
			},
			intoratio:{
				required:"请输入分成比例",
				range:"0~9.99之间的数字"
			},
            phone:{
                digits:"电话号码请输入数字"
            },
            mobile:{
                digits:"手机号码请输入数字"
            },
            email:{
                email:"请输入正确的email地址"
            },
            postcode:{
                minlength:"请输入正确的邮政编码",
                maxlength:"请输入正确的邮政编码",
                digits:"请输入正确的邮政编码"
            }
            
		}
	});
	
    var set_clientid_label=function(){
        if($("#levels").val()=='10'){
            $("#td_clientids").html("子渠道号：");
        }else{
            $("#td_clientids").html("渠道号：");
        }
    };
	
	$("#levels").change(function(){
		var levels_val=$(this).val();
		
        set_clientid_label();
		//3级代理商
		if(levels_val==10){
			$("#channeltype").removeClass("ignore");
			$("#name").removeClass("ignore");			
			$("#clientid").removeClass("ignore");
			$("#clientids").removeClass("ignore");
			$("#intoratio").removeClass("ignore");				
		}		
		//2级代理商
		else if(levels_val==30){			
			$("#channeltype").removeClass("ignore");
			$("#name").removeClass("ignore");			
			$("#clientid").addClass("ignore");
			$("#clientids").removeClass("ignore");
			$("#intoratio").removeClass("ignore");
			
		}
		//超级管理员，或者 没有选择
		else if(levels_val>30 || !levels_val){
			$("#channeltype").addClass("ignore");
			$("#name").addClass("ignore");			
			$("#clientid").addClass("ignore");
			$("#clientids").addClass("ignore");
			$("#intoratio").addClass("ignore");			
		}
		
	});
	
	//set_clientid_label();
});

function getLength(str) {
    var len = str.length;
    var reLen = 0;
    for (var i = 0; i < len; i++) {       
        if (str.charCodeAt(i) < 27 || str.charCodeAt(i) > 126) {
            // 全角   
            reLen += 2;
        } else {
            reLen++;
        }
    }
    return reLen;   
}
</script>
<h1><span class="action-span">
 <?php if ($_smarty_tpl->getVariable('admin')->value['level']>=200){?>
<a href="/index.php?ac=user_list&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
">管理员列表</a>
 <?php }?>
</span>
<span class="action-span1">用户管理</span><span id="search_id"
	class="action-span1"> - <?php echo $_smarty_tpl->getVariable('actionText')->value;?>
</span>
<div style="clear: both"></div>
</h1>

<div class="main-div">
    <form action="<?php echo $_smarty_tpl->getVariable('url')->value;?>
" method="post" enctype="multipart/form-data" name="theForm" id="theForm">
<input type="hidden" name="HTTP_REFERER" value="<?php echo $_SERVER['HTTP_REFERER'];?>
" />
<?php if ($_smarty_tpl->getVariable('info')->value){?><input type="hidden" name="userid" value="<?php echo $_smarty_tpl->getVariable('info')->value['userid'];?>
" />
            <input type="hidden" name="method" value="<?php echo $_smarty_tpl->getVariable('method')->value;?>
" />
<?php }?>
<table width="100%">
	<?php if ($_smarty_tpl->getVariable('error')->value){?>
	<tr align="center">
		<td colspan="2" style="color: red"><?php echo $_smarty_tpl->getVariable('error')->value;?>
</td>
	</tr>
	<?php }?>
	<tr>
		
	</tr>
    <tr>
    <td colspan="3">
    <?php if ($_smarty_tpl->getVariable('action')->value=='update'){?>
    <div class="main-div">
         <h1>
            <div class="section_title">
            公司渠道基本信息
            </div>
        </h1>
        <table id="list-table__" width="100%">
            <tbody>
                <tr>
                    <th width="150">公司名称</th>
                    <th width="150">渠道号</th>
                    <th width="150">分成比例</th>
                    <th width="150">备注</th>
                </tr>
                <tr>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo $_smarty_tpl->getVariable('info')->value['name'];?>
</td>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo $_smarty_tpl->getVariable('info')->value['clientids'];?>
</td>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo $_smarty_tpl->getVariable('info')->value['intoratio'];?>
</td>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo $_smarty_tpl->getVariable('info')->value['describe'];?>
</td>                    
                </tr>
            </tbody>
        </table>
    </div>
    <?php }else{ ?>
    <?php if ((($tmp = @$_smarty_tpl->getVariable('agentid')->value)===null||$tmp==='' ? 0 : $tmp)==0){?>
    <div class="main-div">
        <h1>
            <div class="section_title">
            公司基本信息
            </div>
        </h1>
        <table id="list-table__" width="100%">
            <tbody>
                <tr>
                    <td class="label_2" width="150">选择角色：</td>
                    <td>
                    <?php if ($_smarty_tpl->getVariable('act')->value!='personal'){?> 
                        <select name="levels" id="levels" autocomplete="off">
                        <option value="">--选择角色--</option>
                        <?php  $_smarty_tpl->tpl_vars['ro'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['idx'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('role')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['ro']->key => $_smarty_tpl->tpl_vars['ro']->value){
 $_smarty_tpl->tpl_vars['idx']->value = $_smarty_tpl->tpl_vars['ro']->key;
?>
                        <?php if ($_smarty_tpl->getVariable('levels')->value>$_smarty_tpl->tpl_vars['idx']->value){?>
                       	<option value="<?php echo $_smarty_tpl->tpl_vars['idx']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['ro']->value;?>
</option>
                        <?php }?>
                        <?php }} ?>
                        </select><label><span>*</span></label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php }?>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2">
                        <div id="list-table-2" style="display:none;">
							<table>
                            <?php if ((($tmp = @$_smarty_tpl->getVariable('levels')->value)===null||$tmp==='' ? 0 : $tmp)>=200){?>
                                    <tr>
                                            <td class="label_2" width="150">所属类型：</td>
                                            <td>
                                            <select  id="channeltype" name="channeltype"  autocomplete="off">
                                                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('channel_types')->value),$_smarty_tpl);?>

                                            </select>
                                            <label><span>*</span></label>
                                            </td>
                                    </tr>
                            <?php }?>
							<tr>
								<td class="label_2" width="150">公司名称：</td>
								<td><input class="ignore" id="name" type="text" name="name" autocomplete="off" /><label id="loading3"></label><label><span>*</span>4~50个字符</label></td>
							</tr>
							<tr id='p_channel' style="display:none">
								<td class="label_2" width="150">渠道号：</td>
								<td>
								<!--<select class="ignore" id="clientid" name="clientid"  autocomplete="off">
								  <option value="" >--选择渠道--</option>
								  <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('comlist')->value),$_smarty_tpl);?>

								</select> -->
                                
                                <?php if ($_smarty_tpl->getVariable('admin')->value['level']>=200){?>
								<input type="text" name="" id="clientid_dummy" autocomplete="off"  value="00"/>	
								<input type="hidden" name="clientid" id="clientid" value="" />	
                                                                <label><font id="clientid_error" color="red"></font>	</label>					 						
								<label><span>*</span></label>
								</td>
								<script>
								$(function(){										
										var $clientid = $("#clientid");
										var $clientid_dummy = $("#clientid_dummy");
										var $clientid_error = $("#clientid_error");
										var arrData = [];
										var isKeyInput = false;
										
										$("#channeltype").change(function(){
											var val = $(this).val();								
											$clientid_dummy.val("");
											$clientid.val("");
													
											$.post("/index.php?ac=getchannel&channeltype="+val,function(_data){
												var data = eval("("+_data+")");												
												if(data["status"] == 0){
													arrData = data["data"];	
													
													$clientid_dummy.bigAutocomplete({width:143,param:"title",data:arrData,callback:function(data){
														$clientid.val(data.clientid);
														$clientid_dummy.val(data.clientid);
														isKeyInput = false;
														$clientid_error.html("");
													}});
												}
											});
										}).change();
										
										$clientid_dummy.click(function(){
											$(this).keyup();
											isKeyInput = true;
										}).blur(function(){
											if(!isKeyInput){ return;}
											$clientid_error.html("");
											var $this = $(this);
											var val = $this.val();
											for(var i = 0, len = arrData.length; i < len; i ++){
												var aData = arrData[i];
												if(val == aData.title || val == aData.clientid){
												   $clientid.val(aData.clientid);
												   return;
												}
											}
											$clientid.val("");
											$clientid_error.html("请输入正确渠道号");
										});
										
								});
								</script>
                                <?php }else{ ?>
                                
                                <?php echo $_smarty_tpl->getVariable('admin')->value['clientid'];?>
<input type="hidden" name="clientid" id="clientid" value="<?php echo $_smarty_tpl->getVariable('admin')->value['clientid'];?>
" />	
                                <?php }?>
							</tr>
							<tr>
								<td class="label_2" width="150" id="td_clientids">子渠道号：</td>
								<td>
                                    <input class="ignore" id="clientids" type="text" name="clientids" />
                                    <label id="loading2" style="display:none"><img src="/template/default/img/loading.gif" /></label>
                                    <label><span>*</span>1~9999之间的数字</label>
                                </td>
							</tr>
							<tr>
								<td class="label_2" width="150">分成比例：</td>
								<td><input class="ignore" id="intoratio" type="text" name="intoratio" /><label><span>*</span>请输入0.01~9.99之间的数字</label></td>
							</tr>
							<tr>
								<td class="label_2" width="150">联系电话：</td>
								<td><input type="text" id="phone" name="phone" /><label></label></td>
							</tr>
							<tr>
								<td class="label_2" width="150">手机号码：</td>
								<td><input type="text" id="mobile" name="mobile" /><label></label></td>
							</tr>
							<tr>
								<td class="label_2" width="150">联系人：</td>
								<td><input type="text" name="linkman" /><label></label></td>
							</tr>
							<tr>
								<td class="label_2" width="150">公司地址：</td>
								<td><input type="text" name="address" /><label></label></td>
							</tr>
							<tr>
								<td class="label_2" width="150">邮政编码：</td>
								<td><input type="text" name="postcode" maxlength="6" /><label></label></td>
							</tr>
							<!--<tr>
								<td class="label_2" width="150">操作员姓名：</td>
								<td><input type="text" name="opname" /><label></label></td>
							</tr>-->
							<tr>
								<td class="label_2" width="150">备注：</td>
								<td><input type="text" name="describe" /><label></label></td>
							</tr>
							</table>
                        
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <script type="text/javascript">
            (function(){ 
                var conf=[10,30];
                //显示父渠道号的参数
                var conf2=[10];
                
                function arrayIndexOf(v,r){
                    for(var i=0;i<r.length;i++){
                        if(r[i]===v){
                            return true;
                        }
                    }
                    
                    return false;
                };
                
                $("#levels").change(function(){
                     if(arrayIndexOf(+this.value,conf)){
                        $("#list-table-2").show();
                     }else{
                        $("#list-table-2").hide();
                     }
                     
                     if(arrayIndexOf(+this.value,conf2)){
                        $("#p_channel").show();
                     }else{
                        $("#p_channel").hide();
                     }
					 if($("#username").val() !='' ){
						 $("#username").focus();
					 }
					 
                });

            })();            
        </script>
    </div>
    <?php }else{ ?>
    <input type="hidden" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('agentid')->value)===null||$tmp==='' ? 0 : $tmp);?>
" name="agentid" />
    <input type="hidden" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('levels')->value)===null||$tmp==='' ? 10 : $tmp);?>
" name="levels" />
    <input type="hidden" value="1" name="is_pointcat" />
    <?php }?>
    <?php }?>
    </td>
    </tr>
    
    <tr>
    <td colspan="3">
    <div class="main-div">
        <h1>
            <div class="section_title">
            管理员基本信息
            </div>
        </h1>
        <table id="list-table__" width="100%">
            <tbody>
                <tr>
                    <td class="label_2" width="150">帐号：</td>
                    <td>
                        <?php if ($_smarty_tpl->getVariable('action')->value!='update'){?>                       
                        <input name="username" type="text" id="username" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['username'])===null||$tmp==='' ? '' : $tmp);?>
" maxlength="10" autocomplete="off"/>
                         <script type="text/javascript">
                            (function (){
                                var name_key=false,
                                    clientids_key=false,
                                    username_key=false;
                                
                                function is_ok(){
                                    //
                                    if(+$("#levels").val() < 200){
                                         if(name_key && clientids_key && username_key){
                                            $('#smt').attr('disabled',false);
                                        }else{
                                            $('#smt').attr('disabled',true);
                                        }
                                    }else{
                                        username_key ? $('#smt').attr('disabled',false) : $('#smt').attr('disabled',true);
                                    }
                                };
                                
                                $("#username").focus(function(){
                                    $("#loading1").hide();
                                });
                                $("#clientids").focus(function(){
                                    $("#loading2").hide();
                                });
                                $("#name").focus(function(){
                                    $("#loading3").hide();
                                });                                
                               /* $("#levels").change(function(){
                                    $('#clientids').val('');
                                    $("#loading2").hide();
                                    $('#smt').removeAttr('disabled');
                                });*/
                                
                                $("#username").blur(function(){
                                    var username = this.value;
                                    if(username.length>4 && 11>username.length){
                                        $("#loading1").show();
                                        $("#loading1").html('<img src="/template/default/img/loading.gif" />');
                                        ck_isset('username',username,function(c){
                                            ck_loder('loading1','用户名',c,function(){
                                                username_key=false;
                                            },function(){
                                                username_key=true;                                                                                           
                                            },function(){
                                                is_ok();
                                            });
                                            
                                        });
                                    }
                                 });
                                 $("#clientids").blur(function(){
									 
									if(this.value.indexOf('.')>0 || parseInt(this.value)!=this.value)return;
									 
                                    var fieldval = parseInt(this.value); // 字段值
                                    var role = parseInt($("#levels").val());  // 角色权限
                                    var fieldname = ''; // 字段名称
                                    var msg_txt ='';    //提示信息
                                    if(fieldval>0 && 9999>=fieldval){
                                        $("#loading2").show();
                                        $("#loading2").html('<img src="/template/default/img/loading.gif" />');
                                        switch(role){
                                            case 10:
                                                fieldname='clientids';
												fieldval=$("#clientid").val()+'-'+fieldval;
                                                msg_txt = '子渠道号';
                                                break;
                                            case 30:
                                                fieldname='clientid';
                                                msg_txt = '渠道号';
                                                break;
                                        }
										
                                        ck_isset(fieldname,fieldval,function(c){
                                            ck_loder('loading2',msg_txt,c,function(){
                                                clientids_key=false;
                                            },function(){
                                                clientids_key=true;
                                            },function(){
                                                is_ok();
                                            });
                                        });
                                    }
                                 });
                                 $("#name").blur(function(){
                                    var fieldval = this.value;
                                    if(getLength(fieldval)>3 && 50>getLength(fieldval)){
                                        $("#loading3").show();
                                        $("#loading3").html('<img src="/template/default/img/loading.gif" />');
                                        ck_isset('name',fieldval,function(c){
                                            ck_loder('loading3','公司名称',c,function(){
                                                name_key=false;
                                            },function(){
                                                name_key=true;
                                            },function(){
                                                is_ok();
                                            });
                                        });
                                    }
                                 });
                            })();
                        </script>
                        <?php }else{ ?>
                        <span><?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['username'])===null||$tmp==='' ? '' : $tmp);?>
</span>
                        <input type="hidden" id="username" name="username" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['username'])===null||$tmp==='' ? '' : $tmp);?>
"/>
                        <?php }?>
                        <label id="loading1" style="display:none"><img src="/template/default/img/loading.gif" /></label>
                        <label><span>*</span>5~10英文字母及数字区分大小写</label>
                  </td>
                </tr>
                <tr>
                    <td class="label_2" width="150">密码：</td>
                    <td><input id="userpass" type="password" name="userpass" autocomplete="off" /><label><span>*</span>6~32字符英文字母、数字或特殊字符任意组合</label></td>
                </tr>
                <tr>
                    <td class="label_2" width="150">确认密码：</td>
                    <td><input id="repass" type="password" name="repass" autocomplete="off" /><label><span>*</span>请再次输入密码</label></td>
                </tr>
                <tr>
                    <td class="label_2" width="150">姓名：</td>
                    <td><input id="nickname" type="text" name="nickname"  value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['nickname'])===null||$tmp==='' ? '' : $tmp);?>
"/><label><span>*</span>2~9个字英文字母、汉字</label></td>
                </tr>
                <tr>
                    <td class="label_2" width="150">邮箱地址：</td>
                    <td><input type="text" name="email" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['email'])===null||$tmp==='' ? '' : $tmp);?>
" /><label><span></span>填写你常用的邮箱方便重置密码</label></td>
                </tr>
            </tbody>
        </table>
        
    </div>
    </td>
    </tr>
    
    
    
    
    
	
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="smt" id="smt" value="提交" class="button" <?php if ('insert'==$_smarty_tpl->getVariable('action')->value){?>disabled<?php }?> /></td>
	</tr>
</table>
</form>
</div>

<?php $_template = new Smarty_Internal_Template("public/pageFoot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<script src="/template/default/js/common.js" type="text/javascript"></script>