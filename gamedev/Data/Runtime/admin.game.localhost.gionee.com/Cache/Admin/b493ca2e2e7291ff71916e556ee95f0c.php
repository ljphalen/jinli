<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="__ACTION__" method="post">
	<input type="hidden" name="pageNum" value="<?php echo ($_REQUEST["pageNum"]); ?>"/>
	<input type="hidden" name="numPerPage" value="<?php echo ($_REQUEST["numPerPage"]); ?>" />
	<input type="hidden" name="orderField" value="<?php echo ($_REQUEST["orderField"]); ?>" />
	<input type="hidden" name="orderDirection" value="<?php echo ($_REQUEST["orderDirection"]); ?>" />
	<input type="hidden" name="method" value="<?php echo ($_REQUEST["method"]); ?>"/>
	<?php if(is_array($_REQUEST["_search"])): $name = 0; $__LIST__ = $_REQUEST["_search"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($name % 2 );++$name;?><input type="hidden" name="_search[<?php echo ($key); ?>]" value="<?php echo (safe($field)); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
</form>

<div class="page">

	<!--查询开始-->
	<div class="pageHeader">
		<form onsubmit="return navTabSearch(this);" action="__URL__" method="post" name="myform" id = 'myform'>
		<input type="hidden" name="pageNum" value="<?php echo ($_REQUEST["pageNum"]); ?>"/>
        <input type="hidden" name="method" value="<?php echo ($_REQUEST["method"]); ?>"/>
		<div class="searchBar">
			<ul class="searchContent" style=" overflow:inherit;">
				<li>
					<label>开发者：</label>
					<input type="text" name="_search[author]" value="<?php echo ($_REQUEST["_search"]["author"]); ?>"/>
				</li>
				<li>
					<label>应用名称：</label>
					<input type="text" name="_search[app_name]" value="<?php echo ($_REQUEST["_search"]["app_name"]); ?>"/>
				</li>
				<li>
					<label>应用包名：</label>
					<input type="text" name="_search[package]" value="<?php echo ($_REQUEST["_search"]["package"]); ?>"/>
				</li>
				<li>
					<label>审核人：</label>
					<input type="text" name="_search[account]" value="<?php echo ($_REQUEST["_search"]["account"]); ?>"/>
				</li>
                <li>
					<label><?php echo ($_REQUEST['method']=='passed'?"审核":($_REQUEST['method']=='online'?"上线":"下线")); ?>日期：</label>
					<input type="text" name="_search[timeStart]" value="<?php echo ($_REQUEST["_search"]["timeStart"]); ?>" class="date" yearstart="-10" yearend="0" pattern="yyyy-MM-dd" readonly/>
					到<input type="text" name="_search[timeEnd]" value="<?php echo ($_REQUEST["_search"]["timeEnd"]); ?>" class="date" yearstart="-10" yearend="0" pattern="yyyy-MM-dd" readonly/>
				</li>
                
                <div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div>
			</ul>

		</div>
		</form>
	</div>
	<!--查询结束-->
	
	
	<div class="pageContent">
	
		<!-- 列表开始 -->
		<table class="list" width="100%" layoutH="138">
			<thead>
			<tr>
				<th orderField="id" class="<?php echo orderField('id','desc');?>">ApkID</th>
				<th orderField="app_id" class="<?php echo orderField('app_id','desc');?>">AppID</th>
				<th>提交日期</th>
				<th>审核日期</th>
				<?php if($_REQUEST['method']!='passed'){ if($_REQUEST['method']=='online'){ echo '<th>上线日期</th>'; }else{ echo '<th>下线日期</th>'; } } ?>
				<th>开发者帐号</th>
				<th>应用名称</th>
				<th>包名</th>
				<th>版本名称</th>
				<th>应用状态</th>
				<th>操作</th>
				<th>测试记录</th>
				<th>历史记录</th>
			</tr>
			</thead>
			<tbody>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; $con['package'] = $vo['package']; $con['version_code'] = $vo['version_code']; $testlog_info = D("Testlog")->where($con)->order('created_at desc')->find(); $test_admin = D("Admin")->find($testlog_info['admin_id']); $admin_info = D("Admin")->find($vo['admin_id']); ?>
			
				<tr target="sid_user" rel="<?php echo ($vo['id']); ?>">
					<td><?php echo ($vo['id']); ?></td>
					<td><?php echo ($vo['app_id']); ?></td>
					<td width="130"><?php echo (date("Y-m-d H:i:s",$vo['created_at'])); ?></td>
					<td width="130"><?php echo (date("Y-m-d H:i:s",$vo['checked_at'])); ?></td>
					
					<?php if($_REQUEST['method']!='passed'){ echo '<td width="130">'; if($vo['status']==3 && !empty($vo['onlined_at'])){ echo date("Y-m-d H:i:s", $vo['onlined_at']); } elseif(intval($vo['status']) < -1 && !empty($vo['offlined_at'])) { echo date("Y-m-d H:i:s", $vo['offlined_at']); }else{ echo ''; } echo '</td>'; } ?>
					
					<td><?php echo AccountsModel::listTxt($vo['author_id']);?></td>
					<td><?php echo ($vo['app_name']); ?></td>
					<td><?php echo ($vo['package']); ?></td>
					<td><?php echo ($vo['version_name']); ?></td>
					<td>
					 <?php if($vo['status']==2){ echo "审核通过"; } elseif($vo['status']==3){ echo "已上线"; } elseif($vo['status'] == -2){ echo "已下线"; } elseif($vo['status'] == -3){ echo "已认领下线"; } elseif($vo['status'] == -4){ echo "已封号下线"; } ?>
					</td>
					<td width="100">
					    <?php if($vo['status'] == '2' ): ?><a height="450" width="600" target="dialog" href="__URL__/edit/id/<?php echo ($vo['id']); ?>">上线</a> |
					    <a height="450" width="600" target="ajaxTodo" href="__URL__/syncdata/opt/online/id/<?php echo ($vo['id']); ?>">同步</a>
					    <?php elseif($vo['status'] == '3'): ?>
					    <a height="450" width="600" target="dialog" href="__URL__/offedit/id/<?php echo ($vo['id']); ?>">下线</a> |
					    <a height="450" width="600" target="ajaxTodo" href="__URL__/syncdata/opt/offline/id/<?php echo ($vo['id']); ?>">同步</a>
					    <?php elseif($vo['status'] == '-2'): ?>
					    <a height="450" width="600" target="dialog" href="__URL__/edit/id/<?php echo ($vo['id']); ?>">上线</a> |
					    <a height="450" width="600" target="ajaxTodo" href="__URL__/syncdata/opt/online/id/<?php echo ($vo['id']); ?>">同步</a>
					    <?php else: endif; ?>
					
					</td>
					<td>
					<a href="<?php echo U("@admin");?>/testlog/index/apk_id/<?php echo ($vo['id']); ?>" target="navTab" mask="true">查看</a>
					</td>
					<td>
					<a href="<?php echo U("@admin");?>optlog/index/apk_id/<?php echo ($vo['id']); ?>" target="navTab" mask="true">查看</a>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
		</table>
		<!-- 列表结束 -->
		
		<!-- 分页开始 -->
		<div class="panelBar">
			<div class="pages">
				<span>显示</span>
				<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
					<option value="10" <?php if(($numPerPage) == "10"): ?>selected<?php endif; ?>>10</option>
					<option value="20" <?php if(($numPerPage) == "20"): ?>selected<?php endif; ?>>20</option>
					<option value="50" <?php if(($numPerPage) == "50"): ?>selected<?php endif; ?>>50</option>
					<option value="100" <?php if(($numPerPage) == "100"): ?>selected<?php endif; ?>>100</option>
					<option value="200" <?php if(($numPerPage) == "200"): ?>selected<?php endif; ?>>200</option>
				</select>
				<span>共<?php echo ($totalCount); ?>条</span>
			</div>
			<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($currentPage); ?>"></div>
		</div>
		<!-- 分页结束 -->
		
	</div>
	
</div>