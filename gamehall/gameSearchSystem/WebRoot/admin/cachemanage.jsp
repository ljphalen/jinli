<%@page contentType="text/html; charset=UTF-8"%>

<%@page import="com.gionee.game.search.server.util.Constants"%>

<%
   String cache = (null == request.getParameter("cache") ? "0" : request.getParameter("cache"));
%>

<html>
	<head>
		<title>搜索缓存管理</title>
	</head>
	<body>
		<form>
			<table>
				<tr>
					<td>
						缓存:
						<select name="cache">
						    <option value="0" <%=cache.equals("0") ? "selected='selected'" : ""%>>
								请选择
							</option>	
							<option value="1" <%=cache.equals("1") ? "selected='selected'" : ""%>>
								游戏-资源搜索_缓存
							</option>
							<option value="2" <%=cache.equals("2") ? "selected='selected'" : ""%>>
								游戏-联想词搜索_缓存
							</option>							
						</select>
						<input type="submit" name="commit" value="提交">
					</td>
				</tr>
				<tr>
					<td>
						<%
							if (cache.equals("1")) { // 游戏-资源搜索_缓存
							    Constants.jcsAdminBean.clearRegion("gameCache"); 
							    out.println("<br/> <script type=\"text/javascript\"> alert(\"缓存清除成功！\") </script> <br/>");
						    }
						    else if (cache.equals("2")) { // 游戏-联想词搜索_缓存
						        Constants.jcsAdminBean.clearRegion("gameSuggestionCache"); 
						        out.println("<br/> <script type=\"text/javascript\"> alert(\"缓存清除成功！\") </script> <br/>");
						    }
						%>
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>

