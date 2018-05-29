<?php

/*
 *邮件配置
 */
//$mail->Host = $this->mailconfig["host"];  // Specify main and backup SMTP servers
$EMAIL["mailconfig"]["host"]="smtp.idreamsky.com";
$EMAIL["mailconfig"]["username"]="noreply@idreamsky.com";
$EMAIL["mailconfig"]["password"]="noreply123";
$EMAIL["mailconfig"]["from"]="noreply@idreamsky.com";
$EMAIL["mailconfig"]["fromname"]="noreply";
$EMAIL["mailtemplate"]["userpass"]["title"]="您的帐号已通过审批";
$EMAIL["mailtemplate"]["userpass"]["body"]="
您好，您的帐号信息已经审核通过。<br>
证件信息：<br>
证件姓名：%s<br>
证件号码：%s<br>
<br>
为了您的账户安全，以上信息将不能再自行修改，如需修改请联系客服专员，谢谢。
";
$EMAIL["mailtemplate"]["userdenied"]["title"]="您的帐号已被审批驳回";
$EMAIL["mailtemplate"]["userdenied"]["body"]="您好，您的帐号已被审批驳回。
原因如下：<br>
%s<br>
如需帮助，请查看帮助中心，连接如下：<br>
http://www.mobgi.com/help<br>
或与客服专员联系，获取我们的帮助，谢谢。";
$EMAIL["mailtemplate"]["apppass"]["title"]="您的应用已通过审批";
$EMAIL["mailtemplate"]["apppass"]["body"]="您好，您上传的
%s 应用（APPKEY:%s）<br>
已经通过审批。<br>
赶快将您的应用都接入SDK吧！越早接入，收益越多！<br>
<br>
感谢您使用磨基广告平台，希望您在磨基体验愉快！<br>
";
$EMAIL["mailtemplate"]["appdenied"]["title"]="您的应用已被审批驳回";
$EMAIL["mailtemplate"]["appdenied"]["body"]="
您好，您上传的<br>
%s 应用（APPKEY:%s）<br>
已经被审批驳回，原因如下：<br>
%s<br>
赶快检查下您的应用哪里出了问题吧！<br>
<br>
如需帮助，请查看帮助中心，连接如下：<br>
http://www.mobgi.com/help<br>
或与客服专员联系，获取我们的帮助，谢谢。<br>
 <br>
<br>
感谢您使用磨基广告平台，希望您在磨基体验愉快！
";

$EMAIL["lettertemplate"]["usernew"]["title"]="欢迎加入磨基";
$EMAIL["lettertemplate"]["usernew"]["body"]="
感谢您加入磨基移动广告平台，希望您在磨基体验愉快！<br>
如您在使用过程中有任何问题，可以直接和磨基客服专员联系。<br>
 <br>
<br>
客服联系方式：<br>
QQ号：800051212<br>
客服电话：4008-400-188<br>
邮箱：support@mobgi.com<br>
";

$EMAIL["mailtemplate"]["appstatechange"]["title"]="【应用状态变更提醒】 %s 应用被 %s %s";
$EMAIL["mailtemplate"]["appstatechange"]["body"]=" ";
$EMAIL["mailtemplate"]["appstatechange"]['tomailers'] = array('mobgi@idreamsky.com', 'han.song@idreamsky.com',"kiki.nie@idreamsky.com","phil.chen@idreamsky.com","victor.hu@idreamsky.com","soso@idreamsky.com","stephen.feng@idreamsky.com");

$EMAIL["mailtemplate"]["appdelete"]["title"]="【应用删除提醒】 %s 应用被 %s 删除";
$EMAIL["mailtemplate"]["appdelete"]["body"]=" ";
$EMAIL["mailtemplate"]["appdelete"]['tomailers'] = array('mobgi@idreamsky.com', 'han.song@idreamsky.com',"kiki.nie@idreamsky.com","phil.chen@idreamsky.com","victor.hu@idreamsky.com","soso@idreamsky.com","stephen.feng@idreamsky.com");

$EMAIL["mailtemplate"]["appposstatechange"]["title"]="【广告位状态变更提醒】 %s 应用 %s 广告位被 %s %s";
$EMAIL["mailtemplate"]["appposstatechange"]["body"]=" ";
$EMAIL["mailtemplate"]["appposstatechange"]['tomailers'] = array('mobgi@idreamsky.com', 'han.song@idreamsky.com',"kiki.nie@idreamsky.com","phil.chen@idreamsky.com","victor.hu@idreamsky.com","soso@idreamsky.com","stephen.feng@idreamsky.com");

$EMAIL["mailtemplate"]["appposdelete"]["title"]="【广告位删除提醒】 %s 应用 %s 广告位被 %s 删除";
$EMAIL["mailtemplate"]["appposdelete"]["body"]=" ";
$EMAIL["mailtemplate"]["appposdelete"]['tomailers'] = array('mobgi@idreamsky.com', 'han.song@idreamsky.com',"kiki.nie@idreamsky.com","phil.chen@idreamsky.com","victor.hu@idreamsky.com","soso@idreamsky.com","stephen.feng@idreamsky.com");
?>
