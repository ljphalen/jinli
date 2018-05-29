<?php /* Smarty version Smarty-3.0.6, created on 2013-08-10 15:11:02
         compiled from "/work/website/agent.com/template/default/index_band_email.html" */ ?>
<?php /*%%SmartyHeaderCode:5740279285205e786488367-29717175%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3173b3738414a7e518f9f678e4a30943b42b2843' => 
    array (
      0 => '/work/website/agent.com/template/default/index_band_email.html',
      1 => 1375838015,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5740279285205e786488367-29717175',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

 <!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>名游渠道商管理后台</title>
<link rel="stylesheet" type="text/css" href="/template/default/css/general.css" />
<link rel="stylesheet" type="text/css" href="/template/default/css/main.css" />
<link rel="stylesheet" type="text/css" href="/template/default/css/validate.css" />
<script type="text/javascript" src="/template/default/js/jquery.js"></script>
<script type="text/javascript" src="/template/default/js/jquery.validate.js"></script>
<script type="text/javascript" src="/template/default/js/validate_exist.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$("#theForm").validate({
		rules: {
			userpass: "required",
			email: {
				required:true,
				email:true
			}
		},
		messages: {
			userpass: "请输入密码",
			email: {
				required: "请输入Email地址",
				email: "请输入正确的email地址"
			}
		}
	});
});
</script>
</head>

<body>
<h1><span class="action-span"></span>
<span class="action-span1">个人信息</span><span id="search_id"
	class="action-span1"> - 邮箱绑定</span>
<div style="clear: both"></div>
</h1>

<div class="main-div">
<form action="/index.php?ac=band_email" method="post"
	enctype="multipart/form-data" name="theForm" id="theForm">
<table width="100%">
		<tr>
    <td colspan="3">
    <div class="main-div">
        <h1>
            <div class="section_title">
                邮箱绑定
            </div>
        </h1>
        <table id="list-table__" width="100%">
            <tbody>
                <tr>
                    <td class="label_2" width="327">当前登录用户邮箱：</td>
                    <td width="742"><font color="#666666"><?php echo $_smarty_tpl->getVariable('personMail')->value;?>
</font></td>
                </tr>
                <tr>
                    <td class="label_2" width="327">当前登录用户密码：</td>
                    <td><input id="userpass" type="password" name="userpass" /><label></label><label><span>*</span></label></td>
                </tr>
                <tr>
                    <td class="label_2" width="327">请输入新邮箱地址：</td>
                    <td>
                        <input id="email" type="text" name="email" />
                        <label id="loading4"></label>
                        <label><span>*</span></label>
                    </td>
                </tr>
            </tbody>
        </table>
        <script type="text/javascript">
            (function(){
                $("#email").focus(function(){
                    $("#loading4").hide();
                });
                
                var time;
                $("#email").blur(function(){
                    $("#smt").attr('disabled',true);
                    var that=this;
                    clearTimeout(time);
                    
                    time=setTimeout(function(){
                        var nextLabel=$(that).next().get(0);
                        
                        if(nextLabel.className!="error" || nextLabel.style.display=="none"){
                            //do....    
                            var fieldval = that.value;
                            if(fieldval){
                                $("#loading4").show();
                                $("#loading4").html('<img src="/template/default/img/loading.gif" />');
                                ck_isset('email',fieldval,function(c){
                                    ck_loder('loading4','邮箱',c,function(){
                                                $("#smt").disabled = true;
                                            },function(){
                                                $("#smt").attr('disabled',false);                                                              
                                            },function(){
                                            });
                                });
                            }   
                        }
                    },500);
                    
                    
                });
            })();

        </script>
    </div>
    </td>
    </tr>
    <tr>
        <td colspan="2" align="center"><input type="submit" name="smt" disabled=true id="smt" value="下一步" class="button" /></td>
    </tr>
  </tbody>
</table>
</form>
</div>

</body>
</html>
<style type="text/css">
tr.over td {
	background: #bcd4ec; /*这个将是鼠标高亮行的背景色*/
	cursor: pointer;
}
</style>
<script type="text/javascript">
//$(document).ready(function(){
	 $("#list-table tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");})
//});
</script>
<script src="/template/default/js/common.js" type="text/javascript"></script>