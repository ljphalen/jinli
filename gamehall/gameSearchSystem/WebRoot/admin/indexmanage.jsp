<%@page contentType="text/html; charset=UTF-8"%>

<%@page import="com.gionee.game.search.server.util.SpringContextUtil"%>
<%@page import="com.gionee.game.search.server.service.GameSearchService"%>
<%@page import="com.gionee.game.search.server.service.SearchService"%>
<%@page import="com.gionee.game.search.server.bean.otherBean.IndexBean"%>

<%
   String index = (null == request.getParameter("index") ? "0" : request.getParameter("index"));
   String opType = (null == request.getParameter("opType") ? "0" : request.getParameter("opType"));
%>

<html>
	<head>
		<title>索引管理</title>
	</head>
	<body>
		<form>
			<table>
				<tr>
					<td>
						索引:
						<select name="index">
						    <option value="0" <%=index.equals("0") ? "selected='selected'" : ""%>>
								请选择
							</option>	
							<option value="1" <%=index.equals("1") ? "selected='selected'" : ""%>>
								游戏-资源索引
							</option>
						</select>
						操作类型:
						<select name="opType">
						    <option value="0" <%=opType.equals("0") ? "selected='selected'" : ""%>>
								请选择
							</option>	
							<option value="1" <%=opType.equals("1") ? "selected='selected'" : ""%>>
								查看索引
							</option>
							<option value="2" <%=opType.equals("2") ? "selected='selected'" : ""%>>
								同步索引(与数据库同步)
							</option>	
							<!-- 
							<option value="3" <%=opType.equals("3") ? "selected='selected'" : ""%>>
								重建索引
							</option>
							 -->
						</select>						
						<input type="submit" name="commit" value="提交">
					</td>
				</tr>
				<tr>
					<td>
						<%
						    // 索引-搜索服务
						    SearchService searchService = null;
							if (index.equals("1")) { // 游戏-资源索引
								 searchService = (SearchService) SpringContextUtil.getBean("gameSearchService");
						    }
						    
						    
						    if (searchService != null) {
						       // 操作类型
						       if (opType.equals("1")) { // 查看索引
						           IndexBean indexBean = searchService.getIndexBean(); 
						           if (indexBean != null) {
						              out.println("索引路径：" + indexBean.getIndexPath() + "<br/>");
						              out.println("可搜索文档数：" + indexBean.getSearchDocNum() + "<br/>");
						              out.println("有效索引文档数：" + indexBean.getNumDocs() + "<br/>");
						              out.println("索引中删除的文档数：" + indexBean.getNumDeletedDocs() + "<br/>");
						              out.println("最大索引文档号：" + indexBean.getMaxDoc() + "<br/>");
						              out.println("最大记录更新时间：" + indexBean.getMaxUpdateTime() + "<br/>");
						           } else {
						              out.println("indexBean=" + indexBean + "<br/>");
						           }	
						       }
						       else if (opType.equals("2")) { // 同步索引
						           searchService.syncIndex(); 
								   out.println("<br/> <script type=\"text/javascript\"> alert(\"请求提交成功，系统正在同步索引，0-10分钟后，请到线上检查是否生效！\") </script> <br/>");
						       }
						       /* else if (opType.equals("3")) { // 重建索引
						           searchService.recreateIndex();
						           out.println("<br/> <script type=\"text/javascript\"> alert(\"请求提交成功，系统正在重建索引，0-10分钟后，请到线上检查是否生效！\") </script> <br/>");
						       }
						       */						    
						    }
						%>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>

