<?php /* Smarty version Smarty-3.0.6, created on 2013-07-12 17:17:06
         compiled from "D:\www\trunk\agent.com/template/default/index_welcome.html" */ ?>
<?php /*%%SmartyHeaderCode:320445190ae0879b304-48195648%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '92aef4088277c31500a8b205fa66c729780cc95f' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_welcome.html',
      1 => 1368005392,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '320445190ae0879b304-48195648',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php  $_config = new Smarty_Internal_Config("site.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars(null, 'local'); ?> <?php $_template = new Smarty_Internal_Template("public/pageHead.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<h1>
<span class="action-span1">系统首页</span><span id="search_id"
	class="action-span1"> </span>
<div style="clear: both"></div>
</h1>
<div class="main-div">
    <div class="main-div">
        <h1>
            <div class="section_title">
                收益统计概览
            </div>
        </h1>
        <table width="100%" border="0" cellpadding="5">
          <tr>
            <td width="9%">&nbsp;</td>
            <td width="36%">截至昨天您的   总收入：<span class="red"><?php echo (($tmp = @$_smarty_tpl->getVariable('totals')->value['consumemoney'])===null||$tmp==='' ? 0 : $tmp);?>
</span> 元
            <br /></td>
            <td width="27%" align="left">本月收入：<span class="red"><?php echo (($tmp = @$_smarty_tpl->getVariable('totals')->value['monthmoney'])===null||$tmp==='' ? 0 : $tmp);?>
</span> 元</td>
            <td width="28%" align="left">昨日收入：<span class="red"><?php echo (($tmp = @$_smarty_tpl->getVariable('totals')->value['lastmoney'])===null||$tmp==='' ? 0 : $tmp);?>
</span> 元</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>
          <?php if ('200'==$_smarty_tpl->getVariable('user')->value['level']){?>  
            您当前有二级用户 ： <span class="red"><?php echo (($tmp = @$_smarty_tpl->getVariable('user')->value['twouser'])===null||$tmp==='' ? 0 : $tmp);?>
</span>  个
            <?php }else{ ?>
            昨日总注册人数：<span class="red"><?php echo (($tmp = @$_smarty_tpl->getVariable('totals')->value['lastusers'])===null||$tmp==='' ? 0 : $tmp);?>
</span> 人
            <?php }?>
            </td>
            <td align="left"><?php if (($_smarty_tpl->getVariable('user')->value['level']=='200'||$_smarty_tpl->getVariable('user')->value['level']=='30')){?>三级用户：<span class="red"><?php echo (($tmp = @$_smarty_tpl->getVariable('user')->value['threeuser'])===null||$tmp==='' ? 0 : $tmp);?>
</span>个<?php }?></td>
            <td align="left">玩家注册人数：<span class="red"><?php echo (($tmp = @$_smarty_tpl->getVariable('totals')->value['registerusers'])===null||$tmp==='' ? 0 : $tmp);?>
</span> 人</td>
          </tr>
          
          <tr>
            <td>&nbsp;</td>
            <td><?php if ('200'==$_smarty_tpl->getVariable('user')->value['level']){?>昨日总注册人数：<span class="red"><?php echo (($tmp = @$_smarty_tpl->getVariable('totals')->value['lastusers'])===null||$tmp==='' ? 0 : $tmp);?>
</span> 人<?php }?></td>
            <td align="left">&nbsp;</td>
            <td align="center"><a href="/index.php?ac=report">查看详情>></a></td>
          </tr>
            
        </table>
    </div>
    
    <div class="main-div">
        <h1>
            <div class="section_title">
                个人状态
            </div>
        </h1>
        <table width="100%" border="0" cellpadding="5">
          <tr>
            <td style="background-color:#FFF;"><p>登录者:  <?php echo $_smarty_tpl->getVariable('user')->value['username'];?>
，所属角色:  <?php echo $_smarty_tpl->getVariable('user')->value['rolename'];?>
  </p>
              <p><!--这是您第 <?php echo $_smarty_tpl->getVariable('user')->value['loginNum'];?>
 次登录，-->上次登录时间： <?php echo $_smarty_tpl->getVariable('user')->value['dateline'];?>
 ，登录IP：<?php echo $_smarty_tpl->getVariable('user')->value['logonip'];?>
</p></td>
          </tr>
        </table>
    </div>
    
    <div class="main-div">
        <h1>
            <div class="section_title">
                快捷菜单
            </div>
        </h1>
  
  <table>
  <tr>
  	<td style="background-color:#FFF;">
    
    	<table width="100" border="0" cellpadding="8" style="width:auto;background-color:#FFF;">
        	<?php if ('200'==$_smarty_tpl->getVariable('user')->value['level']){?>
            <tr>
              <td valign="top" nowrap><strong>查询统计报表：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=report&channeltype=1">自主渠道商</a></td>
              <td valign="top" nowrap><a href="index.php?ac=report&channeltype=2">移动渠道商</a></td>
              <td valign="top" nowrap><a href="index.php?ac=report_excel">导出对帐单</a></td>
            </tr>
            <tr>
              <td valign="top" nowrap><strong>用户管理：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=user_list&channeltype=1">自主渠管理员列表</a></td>
              <td valign="top" nowrap><a href="index.php?ac=user_list&channeltype=2">移动渠道管理员列表</a></td>
              <td valign="top" nowrap><a href="index.php?ac=user_add">添加用户</a></td>
            </tr>
            <tr>
              <td valign="top" nowrap><strong>渠道商管理：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=company_list&channeltype=1">自主渠道商</a></td>
              <td valign="top" nowrap><a href="index.php?ac=company_list&channeltype=2">移动渠道商</a></td>
            </tr>
            <tr>
              <td valign="top" nowrap><strong>个人信息：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=set_person">个人设置</a></td>
              <td valign="top" nowrap><a href="index.php?ac=reset_Pwd">修改密码</a></td>
              <td valign="top" nowrap><a href="index.php?ac=band_email">邮箱绑定</a></td>
            </tr>
            <tr>
              <td valign="top" nowrap><strong>系统设置：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=graph">图形参数设置</a></td>
              <td valign="top" nowrap><a href="index.php?ac=setting_payway">第三方支付方式配置</a></td>
              <td valign="top" nowrap><a href="index.php?ac=setting_payconfig">我方他方支付方式配置</a></td>
              <td valign="top" nowrap><a href="index.php?ac=setting_yd_rate">移动平台分成比率配置 </a></td>
            </tr>
			<tr>
              <td valign="top" nowrap></td>
              <td valign="top" nowrap><a href="index.php?ac=setting_ipay_rate">爱贝渠道分成比率配置</a></td>
			  <td valign="top" nowrap><a href="index.php?ac=setting_yd_channel">移动自有渠道费率配置</a></td>
			  <td valign="top" nowrap><a href="index.php?ac=setting_base">对帐单基本信息配置 </a></td>
            </tr>
			
            <?php }?>
			
			<?php if ('30'==$_smarty_tpl->getVariable('user')->value['level']){?>
            <tr>
              <td valign="top" nowrap><strong>查询统计报表：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=report">查询统计报表</a></td>
            </tr>
            <tr>
              <td valign="top" nowrap><strong>用户管理：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=user_list">管理员列表</a></td>
              <td valign="top" nowrap><a href="index.php?ac=user_add">添加用户</a></td>
            </tr>
            <tr>
              <td valign="top" nowrap><strong>渠道商管理：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=company_list">渠道商列表</a></td>
            </tr>
            <tr>
              <td valign="top" nowrap><strong>个人信息：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=set_person">个人设置</a></td>
              <td valign="top" nowrap><a href="index.php?ac=reset_Pwd">修改密码</a></td>
              <td valign="top" nowrap><a href="index.php?ac=band_email">邮箱绑定</a></td>
            </tr>
            <?php }?>
			
			<?php if ('10'==$_smarty_tpl->getVariable('user')->value['level']){?>
            <tr>
              <td valign="top" nowrap><strong>查询统计报表：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=report">查询统计报表</a></td>
            </tr>
            <tr>
              <td valign="top" nowrap><strong>渠道商管理：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=mychannel">我的渠道</a></td>
            </tr>
            <tr>
              <td valign="top" nowrap><strong>个人信息：</strong></td>
              <td valign="top" nowrap><a href="index.php?ac=set_person">个人设置</a></td>
              <td valign="top" nowrap><a href="index.php?ac=reset_Pwd">修改密码</a></td>
              <td valign="top" nowrap><a href="index.php?ac=band_email">邮箱绑定</a></td>
            </tr>
            <?php }?>
        </table>
    
    </td>
  </tr>
  </table>
        
           
    </div>
</div>
<?php $_template = new Smarty_Internal_Template("public/pageFoot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<script src="/public/js/common.js" type="text/javascript"
	language="javascript"></script>