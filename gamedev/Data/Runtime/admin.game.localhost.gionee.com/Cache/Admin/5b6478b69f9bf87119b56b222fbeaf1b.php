<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title><?php echo (C("sitename")); ?></title>
<link href="<?php echo cdn('PUBLIC');?>/app/dwz/themes/default/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo cdn('PUBLIC');?>/app/dwz/themes/css/core.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="<?php echo cdn('PUBLIC');?>/app/dwz/themes/css/ieHack.css" rel="stylesheet" type="text/css" />
<![endif]-->
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/speedup.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/jquery-1.7.2.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/jquery.cookie.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/jquery.validate.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/jquery.bgiframe.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/jquery.tablesorter.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/xheditor/xheditor-1.1.8-zh-cn.min.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/uploadify/scripts/swfobject.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/uploadify/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/dwz.min.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/dwz.regional.zh.js" type="text/javascript"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/chart/raphael.js"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/chart/g.raphael.js"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/chart/g.bar.js"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/chart/g.line.js"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/chart/g.pie.js"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/dwz/js/chart/g.dot.js"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/FusionCharts/FusionCharts.js"></script> 

<script>var PUBLIC_URL = '/Public/<?php echo C('DEFAULT_THEME');?>/';</script>

<script type="text/javascript">
function fleshVerify(){
	$('#verifyImg').attr("src", '__APP__/Public/verify/'+new Date().getTime());
}
function dialogAjaxMenu(json){
	dialogAjaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok){
		$("#sidebar").loadUrl("__APP__/Public/menu");
	}
}
function navTabAjaxMenu(json){
	navTabAjaxDone(json);
}
$(function(){
	DWZ.init("__APP__/Data/Config/dwz.frag.xml", {
		loginTitle:"操作超时，重新登录",
		loginUrl:"__APP__/Public/logindialog",
		statusCode:{ok:1, error:0, timeout:301},
		debug:false,
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"<?php echo cdn('PUBLIC');?>/app/dwz/themes"});
		}
	});
});
if ($.browser.msie) {
	window.setInterval("CollectGarbage();", 10000);
}

</script>

<!-- <link href="<?php echo cdn('PUBLIC');?>/common/css/admin.css" rel="stylesheet" type="text/css" /> -->
<script src="<?php echo cdn('PUBLIC');?>/app/artdialog/js/artDialog.source.js?skin=blue" type="text/javascript"></script>	
</head>

<body scroll="no"<?php if(!APP_DEBUG): ?>onbeforeunload="return '关闭该页面会造成所有未保存数据会丢失，确认吗？';"<?php endif; ?>>
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<a class="logo" href="<?php echo (C("bbsurl")); ?>">Logo</a>
				<ul class="nav">
					<li><a href="#"><?php echo ($userDetail["Agent"]["name"]); ?> <?php echo (session('loginUserName')); ?> 欢迎您！</a></li>
					<li><a href="http://<?php echo C('SITE_DEV_DOMAIN');?>" target="_blank">网站首页</a></li>
					<?php if(($userDetail["id"]) == "1"): ?><li><a href="<?php echo U('System/Tools/rebuildCache');?>" target="ajaxTodo">更新缓存</a></li><?php endif; ?>
					<li><a href="__APP__/Public/main" target="dialog" width="580" height="360" rel="sysInfo">系统消息</a></li>
					<li><a href="__APP__/Public/password/" target="dialog" mask="true">修改密码</a></li>
					<li><a href="__APP__/Public/profile/" target="dialog" mask="true">修改资料</a></li>
					<li><a href="__APP__/Public/logout/">退出</a></li>
				</ul>
				<ul class="themeList" id="themeList">
					<li theme="default"><div class="selected">蓝色</div></li>
					<li theme="green"><div>绿色</div></li>
					<li theme="purple"><div>紫色</div></li>
					<li theme="silver"><div>银色</div></li>
					<li theme="azure"><div>天蓝</div></li>
				</ul>
			</div>
		</div>
		
		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse"><h2>操作面板</h2><div>收缩</div></div>
				<div class="accordion" fillSpace="sideBar">
					<?php if(is_array($menu)): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i; if(!empty($group['menu'])): ?><div class="accordionHeader">
								<h2><span>Folder</span><?php echo ($group["name"]); ?></h2>
							</div>
				
						    <div class="accordionContent">
						        <ul class="tree treeFolder">
						            <?php if(is_array($group["menu"])): $i = 0; $__LIST__ = $group["menu"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i; if((strtolower($item['name'])) != "public"): if((strtolower($item['name'])) != "index"): if(($item['access']) == "1"): ?><li><a href="<?php echo ($item['url']); ?>" target="navTab" rel="<?php echo ($item['name']); if(($item["action"]) != "index"): echo (strtoupper($item['action'])); endif; ?>"><?php echo ($item['title']); ?></a></li><?php endif; endif; endif; endforeach; endif; else: echo "" ;endif; ?>
						        </ul>
						    </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
		</div>

		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent">
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:void(0)"><span><span class="home_icon">欢迎界面</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div>
					<div class="tabsRight tabsRightDisabled">right</div>
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:void(0)">欢迎界面</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
					<div>
	
						<style>
						.style_ls li{ height:30px; line-height:30px; float:left; padding:0 20px;}
						</style>
						<div class="searchBar">
						<ul class="style_ls">
						
						<li>今日新增游戏（实时数据） ：<?php echo ($game_add[0]); ?></li>
						<li>累计注册帐号数量： <?php echo ($user_count); ?> </li>
						<li>昨日新增游戏： <?php echo ($game_add[1]); ?> </li>
						<!--  <li>累计审核通过游戏：<?php echo ($check_count); ?> </li>-->
						<li>昨日审核通过：<?php echo ($game_check[1]); ?> </li>
						</ul>
						<br clear="all">
						</div>
						
						<div class="pageFormContent" layoutH="80">
						
							<script type="text/javascript">
							var options = {
								axis: "0 0 1 1", // Where to put the labels (trbl)
								axisxstep: 11,
								axisxlables: [<?php echo implode(',',$date_arr);?>],
								axisystep: 20,
								shade:false, // true, false
								smooth:false, //曲线
								symbol:"circle"
							};
							
							$(function () {
								
								// Make the raphael object
								var r = Raphael("chartHolder"); 
								
								var lines = r.linechart(
									20, // X start in pixels
									10, // Y start in pixels
									600, // Width of chart in pixels
									400, // Height of chart in pixels
									[0,1,2,3,4,5,6,7,8,9,10,11], // Array of x coordinates equal in length to ycoords
									[[<?php echo implode(',',$game_add);?>], [<?php echo implode(',',$game_update);?>], [<?php echo implode(',',$game_check);?>]], // Array of y coordinates equal in length to xcoords
									options // opts object
								).hoverColumn(function () {
							        this.tags = r.set();
							
							        for (var i = 0, ii = this.y.length; i < ii; i++) {
							            this.tags.push(r.tag(this.x, this.y[i], this.values[i], 160, 10).insertBefore(this).attr([{ fill: "#fff" }, { fill: this.symbols[i].attr("fill") }]));
							        }
							    }, function () {
							        this.tags && this.tags.remove();
							    });
							
							    lines.symbols.attr({ r: 6 });
							});
							</script>
							<style>
							.style_my > div{ margin-bottom:10px; line-height:30px; }
							</style>
							
							
							

							
							
							
							
							
							
							<div id="chartHolder" style="width: 100%;height: 450px; min-width:650px; position:relative;">
							
								<div  style="width:90px; position:absolute; right:20%; top:20px;" class="style_my">
									<div style="width:30px; height:30px; background-color:#2f69bf; cursor:pointer;float:left"></div><div style="width:60px; height:30px;  cursor:pointer;float:left;text-align:center;">游戏增加数</div>
									<div style="width:30px; height:30px; background-color:#a2bf2f; cursor:pointer;float:left"></div><div style="width:60px; height:30px;  cursor:pointer;float:left;text-align:center;">游戏更新数</div>
									<div style="width:30px; height:30px; background-color:#bf5a2f; cursor:pointer;float:left"></div><div style="width:60px; height:30px;  cursor:pointer;float:left;text-align:center;">审核通过数</div>
								</div>
							</div>
							
							<br clear="all">
							<table class="table" width="100%" layoutH="138">
								<thead>
									<tr>
										<th>日期</th>
										<th>新增注册帐号</th>
										<th>审核通过帐号</th>
										<th>日审核量</th>
										<th>游戏增加数</th>
										<th>游戏更新数</th>
										<th>游戏审核通过</th>
										<th>游戏通过率</th>
									</tr>
								</thead>
								<tbody>
									<?php if(is_array($date_arr)): $key = 0; $__LIST__ = $date_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><tr target="sid_user" rel="1">
										<td><?php echo ($vo); ?></td>
										<td><?php echo ($reg_count[$key-1]); ?></td>
										<td><?php echo ($check_count[$key-1]); ?></td>
										<td><?php echo ($accounts_pass_rate[$key-1]); ?>%</td>
										<td><?php echo ($game_add[$key-1]); ?></td>
										<td><?php echo ($game_update[$key-1]); ?></td>
										<td><?php echo ($game_check[$key-1]); ?></td>
										<td><?php echo ($pass_rate[$key-1]); ?>%</td>
									</tr><?php endforeach; endif; else: echo "" ;endif; ?>
									</tbody>
								</table>





						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<div id="footer">Copyright &copy; 2005 - 2015 <a href="<?php echo (C("siteurl")); ?>" target="_blank"><?php echo (C("copyright")); ?></a></div>
</body>
</html>