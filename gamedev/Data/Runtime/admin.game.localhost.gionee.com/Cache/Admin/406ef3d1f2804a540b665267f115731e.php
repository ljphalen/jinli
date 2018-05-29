<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="__ACTION__" method="post">
	<input type="hidden" name="pageNum" value="<?php echo ($_REQUEST["pageNum"]); ?>"/>
	<input type="hidden" name="numPerPage" value="<?php echo ($_REQUEST["numPerPage"]); ?>" />
	<input type="hidden" name="orderField" value="<?php echo ($_REQUEST["orderField"]); ?>" />
	<input type="hidden" name="orderDirection" value="<?php echo ($_REQUEST["orderDirection"]); ?>" />
	<input type="hidden" name="do" value="<?php echo ($_REQUEST["do"]); ?>">
	<?php if(is_array($_search)): $name = 0; $__LIST__ = $_search;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($name % 2 );++$name;?><input type="hidden" name="_search[<?php echo ($key); ?>]" value="<?php echo (safe($field)); ?>"/><?php endforeach; endif; else: echo "" ;endif; ?>
	<input type="hidden" name="account" value="<?php echo ($_REQUEST["account"]); ?>"/>
</form>
<div class="page">
	<div class="pageHeader">
		<form id="accounts_form" onsubmit="return navTabSearch(this);" action="<?php echo U('Accounts/index');?>" method="post">
		<input type="hidden" name="pageNum" value="<?php echo ($_REQUEST["pageNum"]); ?>"/>
		<input type="hidden" name="do" value="<?php echo ($_REQUEST["do"]); ?>"/>
		<div class="searchBar">
			<ul class="searchContent" style="overflow: inherit; height: auto;">
				<li>
					<label>审核状态：</label>
					<select name="status" id="audited">

                            <?php if(is_array($status_list)): $i = 0; $__LIST__ = $status_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$name): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"
                                <?php if(($_REQUEST["status"]) == $key): ?>selected<?php endif; ?>
                                ><?php echo ($name); ?>
                            </option><?php endforeach; endif; else: echo "" ;endif; ?>
                             <option value=""
                                <?php if(($_REQUEST["status"]) == ""): ?>selected<?php endif; ?>
                                >全部
                            </option>
                    </select>
				
				</li>
				<li>
                    <label>日期：</label>
                    <input type="text"
                        name="created_s" value="<?php echo ($_REQUEST["created_s"]); ?>" class="date textInput readonly" yearstart="-10" yearend="0" pattern="yyyy-MM-dd" readonly="">
                      到 
                       <input type="text"
                        name="created_e" value="<?php echo ($_REQUEST["created_e"]); ?>" class="date textInput readonly" yearstart="-10" yearend="0" pattern="yyyy-MM-dd" readonly="">
                </li>
                <li>
                    <label>审核人：</label>
                    <input type="text" name="audit_admin_name" value="<?php echo ($_REQUEST["audit_admin_name"]); ?>"/>
                </li>
                <li>
                    <label>注册帐号：</label>
                    <input type="text" name="email" value="<?php echo ($_REQUEST["email"]); ?>"/>
                </li>
                 <li>
                    <label>注册公司名：</label>
                    <input type="text" name="company" value="<?php echo ($_REQUEST['company']); ?>"/>
                </li>
                <li>
                    <label>联系人查询：</label>
                    <input type="text" name="contact" value="<?php echo ($_REQUEST['contact']); ?>"/>
                </li>
                 <input type='hidden' name='do_export' value='0' />
                <div class="buttonActive"><div class="buttonContent"><button type="submit" id="b_do_sub">查询</button></div></div>
                <div class="buttonActive"><div class="buttonContent"><button type="submit" id="b_do_export">导出</button></div></div>
			</ul>

		</div>
		</form>
	</div>
	
<script type="text/javascript">
$("#b_do_export").click(function(){
	//$("#accounts_form").attr('action',"<?php echo U('Accounts/index');?>");
	$("#accounts_form").attr('onsubmit','');
	$("input[name='do_export']").val(1);
})

$("#b_do_sub").click(function(){
	$("#accounts_form").attr('onsubmit','return navTabSearch(this);');
	$("input[name='do_export']").val(0);
})
</script>


</script>
	<div class="pageContent">
		<div class="panelBar">
			<ul class="toolBar">
				<li><a class="edit" href="__URL__/details/id/{sid_user}" target="dialog" mask="true" warn="请选择操作对象"><span>编辑</span></a></li>
				<li class="line">line</li>
			</ul>
		</div>
		<table class="list" width="100%" layoutH="138">
			<thead>
			<tr>
				<th >ID</th>
				<th width="120">注册帐号（邮箱）</th>
				<th width="120">注册日期</th>
				<th width="100">公司注册名称</th>
				<th>联系人</th>
				<th width="100">类型</th>
				<th width="80">操作</th>
				<th width="80">审核记录</th>
			</tr>	
			</thead>
			<tbody>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid_user" rel="<?php echo ($vo['id']); ?>">
				
					<td><?php echo ($vo['id']); ?></td>
					<td><?php echo AccountsModel::listTxt($vo);?></td>
					<td><?php echo ($vo['created_at']); ?></td>
					<td><?php echo ($vo['company']); ?></td>
					<td><?php echo ($vo['contact']); ?></td>
					<td>
						<?php if($vo["audit_admin_id"] > 0): ?>修改<?php else: ?> 新增<?php endif; ?>
					</td>
					<td>
						<?php $is_perfect = D('Dev://Accountinfo')->isPerfect($vo['id']); if($vo['status'] == AccountinfoModel::STATUS_INIT && $is_perfect) { ?>
							<a height="600" width="600" target="dialog" href="__URL__/audit/id/<?php echo ($vo['account_id']); ?>">审核</a>
								<?php } ?> 
					</td>
					
				   <td>
						<?php if($vo["audit_admin_id"] > 0): ?><a  rel="newPage" target="navTab" href="<?php echo U('authlog/index',array('account_id' => $vo['account_id']) );?>">审核记录	</a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
		</table>

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
				
				<span style="padding-left:10px;">
				帐号数量：<?php echo ($totalCount); ?> &nbsp;&nbsp;<?php if(is_array($gruop_data)): foreach($gruop_data as $key=>$vo): echo ($status_list[$vo['status']]); ?> : <?php echo ($vo['nums']); ?> &nbsp;&nbsp;<?php endforeach; endif; ?>
				时间段： <?php if($_REQUEST["created_s"] != null): echo ($_REQUEST["created_s"]); else: ?> --<?php endif; ?> 至  <?php if($_REQUEST["created_e"] != null): echo ($_REQUEST["created_e"]); else: ?> --<?php endif; ?>
				</span>
				
			</div>
			
			<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($currentPage); ?>"></div>
		</div>

	</div>
	
</div>