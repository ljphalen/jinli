<?php $isLast = preg_match('/^2/', $page)>0? true:false;?>
<header class="hd">
	<div class="line-top"></div>
	<div class="menu">
		<ul>
			<li <?php if(!$isLast){?>class="selected"<?php }?>>
				<span class="top-arrow"><i></i></span>
				<a href="index.php">金立世界</a>
			</li>
			<li <?php if($isLast){?>class="selected"<?php }?>>
				<span class="top-arrow"><i></i></span>
				<a href="myhome.php">我的乐园</a>
				<!--<a data-brief="true" href="login.php">(登陆)</a>-->
			</li>
		</ul>
	</div>
	<div class="sub-menu">
		<?php if($isLast){?>
		<ul>
			<li><a <?php if($page=='2-1'){?>class="selected"<?php }?> href="myhome.php">新闻资讯</a><i></i></li>
			<li><a <?php if($page=='2-2'){?>class="selected"<?php }?> href="chartlet.php">美图在线</a><i></i></li>
			<li><a <?php if($page=='2-3'){?>class="selected"<?php }?> href="lottery.php">彩票天地</a><i></i></li>
			<li><a <?php if($page=='2-4'){?>class="selected"<?php }?> href="signin.php">每日签到</a></li>
		</ul>
		<?php }else{?>
		<ul>
			<li><a <?php if($page=='1-1'){?>class="selected"<?php }?> href="index.php">金立手机</a><i></i></li>
			<li><a <?php if($page=='1-2'){?>class="selected"<?php }?> href="download.php">资源下载</a><i></i></li>
			<li><a <?php if($page=='1-3'){?>class="selected"<?php }?> href="mbparts.php">手机配件</a><i></i></li>
			<li><a <?php if($page=='1-4'){?>class="selected"<?php }?> href="aftersale.php">售后服务</a></li>
		</ul>
		<?php }?>
	</div>
</header>