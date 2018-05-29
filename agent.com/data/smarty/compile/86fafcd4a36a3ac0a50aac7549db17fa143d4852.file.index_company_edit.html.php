<?php /* Smarty version Smarty-3.0.6, created on 2013-04-26 16:02:49
         compiled from "D:\www\trunk\agent.com/template/default/index_company_edit.html" */ ?>
<?php /*%%SmartyHeaderCode:26802517a34a954af24-15317077%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '86fafcd4a36a3ac0a50aac7549db17fa143d4852' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_company_edit.html',
      1 => 1365991598,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '26802517a34a954af24-15317077',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_date_format')) include 'D:\www\trunk\agent.com\plugin\smartys\plugins\modifier.date_format.php';
if (!is_callable('smarty_function_html_options')) include 'D:\www\trunk\agent.com\plugin\smartys\plugins\function.html_options.php';
?><?php  $_config = new Smarty_Internal_Config("site.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars(null, 'local'); ?> <?php $_template = new Smarty_Internal_Template("public/pageHead.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<link href="/template/default/css/validate.css" rel="stylesheet"
	type="text/css" />
<script src="/template/default/js/jquery.validate.js" type="text/javascript"
	language="javascript"></script>
<script src="/template/default/js/validate_exist.js" type="text/javascript" language="javascript"></script>
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
		rules: {
			//公司名称
			name:{
				required:true,
				isLenName:true,
				minlength:2,
				maxlength:50
			},
            <?php if ((($tmp = @$_smarty_tpl->getVariable('info')->value['clientid'])===null||$tmp==='' ? 0 : $tmp)!=0){?>
            clientid:{
				required:true,
				digits:true,
				range:[1,9999]
			},
            <?php }?>
            <?php if ((($tmp = @$_smarty_tpl->getVariable('info')->value['clientids'])===null||$tmp==='' ? 0 : $tmp)!=0){?>
			clientids:{
				required:true,
				digits:true,
				range:[1,9999]
			},
            <?php }?>
			intoratio:{
				required:true,
				range:[0.01,9.99]
			},
            phone:{
                digits:true
            },
            mobile:{
                digits:true
            },
            postcode:{
                required:false,
                minlength:6,
				maxlength:6,
                digits:true
            }
		},
		messages: {
			name:{
				required:"请输入公司名称",
				minlength:"最小4个字",
				maxlength:"最大50位"
			},
            clientid:{
				required:"请选择渠道号",
                digits:"必须输入整数",
				range:"1~9999之间的数字"
			},
            <?php if ((($tmp = @$_smarty_tpl->getVariable('info')->value['clientids'])===null||$tmp==='' ? 0 : $tmp)!=0){?>
			clientids:{
				required:"请输入渠道号",
                digits:"必须输入整数",
				range:"1~9999之间的数字"
			},
            <?php }?>
			intoratio:{
				required:"请输入分成比例",
				range:"0.01~9.99之间的数字"
			},
            phone:{
                digits:"电话号码请输入数字"
            },
            mobile:{
                digits:"手机号码请输入数字"
            },
            postcode:{
                minlength:"请输入正确的邮政编码",
                maxlength:"请输入正确的邮政编码",
                digits:"请输入正确的邮政编码"
            }
		}
	});

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
<h1>
<?php if ((($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp)>10){?>
<span class="action-span"><a href="/index.php?ac=company_list&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
">渠道商列表</a></span>
<?php }?>
<span class="action-span1">渠道商管理</span><span id="search_id"
	class="action-span1"> - <?php echo $_smarty_tpl->getVariable('actionText')->value;?>
</span>
<div style="clear: both"></div>
</h1>

<div class="main-div">
<form action="/index.php?ac=company_edit" method="post" enctype="multipart/form-data" name="theForm" id="theForm">
<?php if ($_smarty_tpl->getVariable('info')->value){?><input type="hidden" name="id" value="<?php echo $_smarty_tpl->getVariable('info')->value['id'];?>
" /><?php }?>
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
     <td colspan="2">
     <?php if ('update'==$_smarty_tpl->getVariable('action')->value){?>
     <input type="hidden" name="method" value="<?php echo $_smarty_tpl->getVariable('method')->value;?>
" />
     <div class="main-div">
         <h1>
            <div class="section_title">
                管理员信息
                <?php if ((($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp)>10){?>     
                <?php if (!($_smarty_tpl->getVariable('admin')->value['levels']==30&&($_smarty_tpl->getVariable('admin')->value['clientids']==$_smarty_tpl->getVariable('info')->value['clientids']))){?>
                <span class='action-span'><a href="/index.php?ac=user_add&cid=<?php echo $_smarty_tpl->getVariable('info')->value['id'];?>
&clientids=<?php echo $_smarty_tpl->getVariable('info')->value['clientids'];?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
">添加管理员</a></span>
                <?php }?>
                <?php }?>
            </div>
            
        </h1>
        <table id="list-table__" width="100%">
            <tbody>
                <tr>
                    <th>帐号</th>
                    <th>姓名</th>
                    <th>邮箱</th>
                    <th>角色类型</th>
                    <th>最后登录时间</th>
                    <th>最后登录IP</th>
                    <?php if ((($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp)>10){?>
                    <th>操作</th>
                    <?php }?>
                </tr>
                <?php  $_smarty_tpl->tpl_vars['user'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('userlist')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['user']->key => $_smarty_tpl->tpl_vars['user']->value){
?>
                <tr>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['user']->value['username'])===null||$tmp==='' ? '' : $tmp);?>
</td>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['user']->value['nickname'])===null||$tmp==='' ? '' : $tmp);?>
</td>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['user']->value['email'])===null||$tmp==='' ? '' : $tmp);?>
</td>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo $_smarty_tpl->getVariable('role')->value[$_smarty_tpl->tpl_vars['user']->value['levels']];?>
</td>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['user']->value['dateline'],"%Y-%m-%d %H:%M");?>
</td>
                    <td class="label_2" width="150" style="text-align:center;"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['user']->value['logonip'])===null||$tmp==='' ? '' : $tmp);?>
</td>
                    <?php if ((($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp)>10){?>
                    <td class="label_2" width="150" style="text-align:center;">
                        <?php if ($_smarty_tpl->getVariable('admin')->value['userid']!=$_smarty_tpl->tpl_vars['user']->value['userid']){?>
                        <a href="/index.php?ac=user_edit&method=edit&uid=<?php echo $_smarty_tpl->tpl_vars['user']->value['userid'];?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
" title="编辑">
                        <img src="/template/default/img/icon_edit.gif" border="0" />编辑
                        </a>&nbsp;&nbsp;
                        
                        <a href="/index.php?ac=user_edit&method=del&uid=<?php echo $_smarty_tpl->tpl_vars['user']->value['userid'];?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
" title="删除" onclick="return delPrompt()">
                        <img src="/template/default/img//icon_drop.gif" border="0" />删除
                        </a>
                        <?php }else{ ?>
                        登录中...
                        <?php }?>
                    </td>   
                  <?php }?>                    
                </tr>
                <?php }} ?>
            </tbody>
        </table>
    </div>
    <?php }?>
     </td>
    </tr>
    <tr>
    <td colspan="3">
    <div class="main-div">
        <h1>
            <div class="section_title">
            渠道公司信息
            </div>
        </h1>
        <table id="list-table__" width="100%">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div id="list-table-2">
                        <table>
                        <?php if ((($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp)>=200){?>
                        <tr>
                            <td class="label_2" width="150">所属类型：</td>
                            <td>
                            <select  id="channeltype" name="channeltype"  autocomplete="off">
                              <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('channel_types')->value,'selected'=>$_smarty_tpl->getVariable('info')->value['channeltype']),$_smarty_tpl);?>

                            </select>
                            <label><span>*</span></label>
                            </td>
                        </tr>
                        <?php }?>
                        <tr>
                            <td class="label_2" width="150">公司名称：</td>
                            <td>
                            <input id="name" type="text" name="name" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['name'])===null||$tmp==='' ? '' : $tmp);?>
" />
                            <label id="loading3"></label>
                            <label><span>*</span>4~50个字符</label>
                            </td>
                        </tr>
                        
                        <tr id='p_channel' >
                            <td class="label_2" width="150">渠道号：</td>
                            <td>
                            <?php if (2000>(($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp)){?>
                            <span><?php echo $_smarty_tpl->getVariable('info')->value['clientid'];?>
</span>
                            <input type="hidden" name="clientid" id="clientid" value="<?php echo $_smarty_tpl->getVariable('info')->value['clientid'];?>
" readonly />
                            <?php }else{ ?>
                              <select name="clientid" id="">
                              <option value="0" >--选择渠道--</option>
                              <?php if ($_smarty_tpl->getVariable('info')->value['clientid']>0){?>
                              <?php ob_start();?><?php echo $_smarty_tpl->getVariable('info')->value['clientid'];?>
<?php $_tmp1=ob_get_clean();?><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('comlist')->value,'selected'=>$_tmp1),$_smarty_tpl);?>

                              <?php }?>
                            </select>
                            <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label_2" width="150">子渠道号：</td>
                            <td>
                            <?php if (200>(($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp)){?>
                            <span><?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['clientids'])===null||$tmp==='' ? 0 : $tmp);?>
</span>
                            <?php if ((($tmp = @$_smarty_tpl->getVariable('info')->value['clientids'])===null||$tmp==='' ? 0 : $tmp)==0){?><label><!--<font color="#666666">(子渠道号为0，为二级渠道)</font>--></label><?php }?>
                            <input type="hidden" name="clientids" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['clientids'])===null||$tmp==='' ? '' : $tmp);?>
" readonly />
                            <?php }else{ ?>
                             <input id="clientids" type="text" name="clientids" <?php if ($_smarty_tpl->getVariable('info')->value['clientids']==0||$_smarty_tpl->getVariable('info')->value['clientid']==0){?>readonly<?php }?> value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['clientids'])===null||$tmp==='' ? '' : $tmp);?>
"/>
                             <label id="loading1"></label>
                             <label><span>*</span><?php if ((($tmp = @$_smarty_tpl->getVariable('info')->value['clientids'])===null||$tmp==='' ? 0 : $tmp)!=0){?>1~9999之间的数字<?php }?></label>
                            <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label_2" width="150">分成比例：</td>                            
                            <td>
                                <?php if (200>(($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp)){?>
                                <span><?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['intoratio'])===null||$tmp==='' ? 0 : $tmp);?>
</span>
                                <input type="hidden" name="intoratio" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['intoratio'])===null||$tmp==='' ? 0 : $tmp);?>
" readonly />
                                <?php }else{ ?>
                                <input id="intoratio" type="text" name="intoratio" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['intoratio'])===null||$tmp==='' ? 0 : $tmp);?>
"/><label><span>*</span>请输入0.01~9.99之间的数字</label>
                                <?php }?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label_2" width="150">联系电话：</td>
                            <td><input type="text" name="phone" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['phone'])===null||$tmp==='' ? '' : $tmp);?>
"/><label></label></td>
                        </tr>
                        <tr>
                            <td class="label_2" width="150">手机号码：</td>
                            <td><input type="text" name="mobile" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['mobile'])===null||$tmp==='' ? '' : $tmp);?>
"/><label></label></td>
                        </tr>
                        <tr>
                            <td class="label_2" width="150">联系人：</td>
                            <td><input type="text" name="linkman" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['linkman'])===null||$tmp==='' ? '' : $tmp);?>
"/><label></label></td>
                        </tr>
                        <tr>
                            <td class="label_2" width="150">公司地址：</td>
                            <td><input type="text" name="address" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['address'])===null||$tmp==='' ? '' : $tmp);?>
"/><label></label></td>
                        </tr>
                        <tr>
                            <td class="label_2" width="150">邮政编码：</td>
                            <td><input type="text" name="postcode" maxlength="6" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['postcode'])===null||$tmp==='' ? '' : $tmp);?>
"/><label></label></td>
                        </tr>
                        <!--<tr>
                            <td class="label_2" width="150">操作员姓名：</td>
                            <td><input type="text" name="opname" /><label></label></td>
                        </tr>
                        -->
                        <tr>
                            <td class="label_2" width="150">备注：</td>
                            <td><input type="text" name="describe" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['describe'])===null||$tmp==='' ? '' : $tmp);?>
"/><label></label></td>
                        </tr>
                        </table>
                        
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
    </tr>
    
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="smt" id="smt" value="提交"
			class="button" /></td>
	</tr>
</table>
</form>
</div>
<script type="text/javascript">
    (function(){
        var name_key=<?php if ($_smarty_tpl->getVariable('info')->value['id']>0){?>true<?php }else{ ?>false<?php }?>,
            clientids_key=<?php if ($_smarty_tpl->getVariable('info')->value['id']>0){?>true<?php }else{ ?>false<?php }?>;
        
        var company_name="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['name'])===null||$tmp==='' ? '' : $tmp);?>
";
            client_ids="<?php echo (($tmp = @$_smarty_tpl->getVariable('info')->value['clientids'])===null||$tmp==='' ? 0 : $tmp);?>
";
        
        function is_ok(){
            //
            if(<?php echo (($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp);?>
 < 200){
                 name_key ? $('#smt').attr('disabled',false) : $('#smt').attr('disabled',true);
            }else{
                if(name_key && clientids_key){
                    $('#smt').attr('disabled',false);
                }else{
                    $('#smt').attr('disabled',true);
                }
                
            }
        };
        
        $('#name').focus(function(){
            $("#loading3").hide();
        });
        $('#clientids').focus(function(){
            $("#loading1").hide();
        });
        
        
        $('#name').blur(function(){
            var fieldval = this.value;
            if(getLength(fieldval)>3 && 50>getLength(fieldval) && company_name!=fieldval){
                $("#loading3").show();
                $("#loading3").html('<img src="/template/default/img/loading.gif" />');
                ck_isset('name',fieldval,function(c){
                    ck_loder('loading3','公司名称',c,function(){
                       name_key = false;
                    },function(){
                        clientids_key = $('#clientids').val() == client_ids?true:false;
                        name_key = true;
                    },function(){
                        is_ok();
                    });
                });
            }else if(company_name==fieldval){
                name_key = true;
                clientids_key = $('#clientids').val() == client_ids?true:false;
                is_ok();
                
            }
        });
        
        <?php if ((($tmp = @$_smarty_tpl->getVariable('admin')->value['levels'])===null||$tmp==='' ? 0 : $tmp)>=200){?>
        $('#clientids').blur(function(){
									 
			if(this.value.indexOf('.')>0 || parseInt(this.value)!=this.value)return;
			
            var fieldval = this.value;
            if(fieldval>0 && 10000>fieldval && fieldval!=client_ids){
                $("#loading1").show();
                $("#loading1").html('<img src="/template/default/img/loading.gif" />');
				fieldval=$("#clientid").val()+'-'+fieldval;
                ck_isset('clientids',fieldval,function(c){
                    ck_loder('loading1','子渠道号',c,function(){
                        clientids_key = false;
                    },function(){
                        name_key = $('#name').val() == company_name?true:false;                        
                        clientids_key = true;
                    },function(){
                        is_ok();
                    });
                });
            }else if(fieldval==client_ids){
                clientids_key = true;
                name_key = $('#name').val() == company_name?true:false; 
                is_ok();
            }
        });
        <?php }?>
        
    })()
</script>
<?php $_template = new Smarty_Internal_Template("public/pageFoot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<script src="/template/default/js/common.js" type="text/javascript"
	language="javascript"></script>