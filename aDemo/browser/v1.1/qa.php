<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立世界-常见问题</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $title = '常见问题'; include '_sheader.php';?>
		
		<article class="ac qa">
			<div class="item-list">
				<ul class="J_itemWrap" data-ajaxUrl="jsonQu.php">
				<li id="curInfo" class="hidden" curpage="1" hasnext="true"></li>
										<li>
						<a href="http://3gtest.gionee.com:88/services/question_detail?qid=28" class="wrap">
							<dl>
								<dt>问题</dt>
								<dd>金立浏览器机型具体信息查询</dd>
							</dl>
						</a>
					</li>
										<li>
						<a href="http://3gtest.gionee.com:88/services/question_detail?qid=9" class="wrap">
							<dl>
								<dt>问题</dt>
								<dd>工作</dd>
							</dl>
						</a>
					</li>
										<li>
						<a href="http://3gtest.gionee.com:88/services/question_detail?qid=10" class="wrap">
							<dl>
								<dt>问题</dt>
								<dd>大巧若拙</dd>
							</dl>
						</a>
					</li>
					<li>
						<a href="http://3gtest.gionee.com:88/services/question_detail?qid=11" class="wrap">
							<dl>
								<dt>问题</dt>
								<dd>在此基础上</dd>
							</dl>
						</a>
					</li>
										<li>
						<a href="http://3gtest.gionee.com:88/services/question_detail?qid=12" class="wrap">
							<dl>
								<dt>问题</dt>
								<dd>地方工业</dd>
							</dl>
						</a>
					</li>
										<li>
						<a href="http://3gtest.gionee.com:88/services/question_detail?qid=14" class="wrap">
							<dl>
								<dt>问题</dt>
								<dd>地方工业 </dd>
							</dl>
						</a>
					</li>
										<li>
						<a href="http://3gtest.gionee.com:88/services/question_detail?qid=15" class="wrap">
							<dl>
								<dt>问题</dt>
								<dd>一些单位</dd>
							</dl>
						</a>
					</li>
										<li>
						<a href="http://3gtest.gionee.com:88/services/question_detail?qid=8" class="wrap">
							<dl>
								<dt>问题</dt>
								<dd>问题2</dd>
							</dl>
						</a>
					</li>
				</ul>
			</div>
		</article>
	</div>
	
	<script id="J_itemView" type="template">
		<li id="curInfo" class="hidden" curpage="{data.curpage}" hasnext="{data.hasnext}"></li>
		{each data.list}
		<li>
			<a href="{$value.href}" class="wrap">
				<dl>
					<dt>问题{$value.id}：</dt>
					<dd>{$value.text}</dd>
				</dl>
			</a>
		</li>
		{/each}
	</script>
</body>
</html>