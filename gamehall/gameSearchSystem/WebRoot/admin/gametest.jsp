<%@page contentType="text/html; charset=UTF-8"%>
<%@page import="java.util.Date"%>
<%@page import="java.util.List"%>
<%@page import="java.util.ArrayList"%>
<%@page import="java.util.Map"%>
<%@page import="java.text.DateFormat"%>
<%@page import="java.text.SimpleDateFormat"%>
<%@page import="net.sf.json.JSONObject"%>
<%@page import="org.apache.commons.lang.builder.ToStringBuilder"%>
<%@page import="org.apache.commons.lang.builder.ToStringStyle"%>
<%@page import="com.gionee.game.search.server.bean.paramBean.SchParam"%>
<%@page import="com.gionee.game.search.server.bean.paramBean.SchParamConstants"%>
<%@page import="com.gionee.game.search.server.util.SpringContextUtil"%>
<%@page import="com.gionee.game.search.server.bean.paramBean.GameSchParam"%>
<%@page import="com.gionee.game.search.server.service.SearchService"%>
<%@page import="com.gionee.game.search.server.service.GameSearchService"%>
<%@page import="com.gionee.game.search.server.service.GameSuggestionSearchService"%>
<%@page import="com.gionee.game.search.server.util.SpringContextUtil"%>
<%@page import="com.gionee.game.search.server.bean.resultBeans.BaseBeans"%>
<%@page import="com.gionee.game.search.server.bean.resultBeans.GameBeans"%>
<%@page import="com.gionee.game.search.server.bean.resultBean.GameBean"%>
<%@page import="com.gionee.game.search.server.bean.resultBean.BaseBean"%>
<%@page import="com.gionee.game.search.server.bean.resultBeans.GameSuggestionBeans"%>
<%@page import="com.gionee.game.search.server.bean.resultBean.GameSuggestionBean"%>


<%
	String keyword = (null == request.getParameter("keyword") ? "" : request.getParameter("keyword"));
	String searchCase = (null == request.getParameter("searchCase") ? "0" : request.getParameter("searchCase"));
	String sort = (null == request.getParameter("sort") ? "0" : request.getParameter("sort"));
	int pageNum = Integer
	.parseInt((request.getParameter("pageNum") == null || request.getParameter("pageNum").length() == 0) ? "1"
			: request.getParameter("pageNum"));
	int pageSize = Integer
	.parseInt((request.getParameter("pageSize") == null || request.getParameter("pageSize").length() == 0) ? "100"
			: request.getParameter("pageSize"));
%>

<html>
	<head>
		<title>游戏搜索接口测试</title>
	</head>
	<body>
		<form>
			<table>
				<tr>
					<td>
						关键词：
						<input type="text" name="keyword" value="<%=keyword%>" size="40">
						<select name="searchCase">
							<option value="1" <%=searchCase.equals("1") ? "selected='selected'" : ""%>>
								游戏搜索
							</option>
							<option value="2" <%=searchCase.equals("2") ? "selected='selected'" : ""%>>
								联想词搜索
							</option>							
						</select>
						<input type="submit" name="sch" value="搜索">
						<br/>
						页码
						<input type="text" name="pageNum" value="<%=pageNum%>" size="4">
						页大小
						<input type="text" name="pageSize" value="<%=pageSize%>" size="4">
						<br/>
						排序方式:
						<select name="sort">
						    <option value="0" <%=sort.equals("0") ? "selected='selected'" : ""%>>
								按相关度
							</option>	
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<%
							if (searchCase.equals("1")) { // 游戏搜索
											    GameSchParam param = new GameSchParam();
											    param.setLog(false);
											    param.setDebug(true);//设置为调试状态
											    param.setHighLight(true);
											    param.setKeyword(keyword.trim());
											    param.setPageNum(pageNum);
											    param.setPageSize(pageSize);
											    if (sort != null && sort.length() > 0) {
													param.setSort(Integer.parseInt(sort));
												}
											
												GameSearchService searchService = (GameSearchService) SpringContextUtil.getBean("gameSearchService");
												
                                                // 搜索
												GameBeans beans = (GameBeans) searchService.search(param);
												
												out.println("<br/>");
												out.println("indexPath==" + searchService.getIndexPath() + "<br/>");
												out.println("查询：" + beans.getReport() + "<br/>");
												out.println("<br/>");
												out.println("搜索结果总数：" + beans.getTotalCount() + " 条<br/>");
												out.println("搜索耗时：" + beans.getSearchCostTime() + " 毫秒<br/>");
												out.println("是否命中缓存：" + beans.isCached() + "<br/>");
												out
														.println("###############################################################################################################################################################################<br/>");

												DateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
												List<BaseBean> beanList = beans.getBeanList();
												for (int i = 0; i < beanList.size(); i++) {
													GameBean bean = (GameBean) beanList.get(i);
													out.println("结果:" + (i + 1) + "<br/>");
													out.println("<b>id:</b> " + bean.getId() + "<br/>");
													out.println("<b>名称:</b> " + bean.getName() + "<br/>");
													out.println("<b>简介:</b> " + bean.getResume() + "<br/>");
													out.println("<b>标签:</b> " + bean.getLabel() + "<br/>");
													out.println("<b>月下载量:</b> " + bean.getMonthDownloads() + "<br/>");
													out.println("<b>下载量:</b> " + bean.getDownloads() + "<br/>");
													out.println("<b>创建时间:</b> " + sdf.format(new Date(Long.parseLong(bean.getCreateTime()) * 1000)) + "<br/>");
													out.println("<b>上线时间:</b> " + sdf.format(new Date(Long.parseLong(bean.getOnlineTime()) * 1000)) + "<br/>");
													
													if (bean.getExplain() != null && bean.getExplain().length() > 0) {
														out.println("<font color='green'><b>Explain: </b>"
																+ bean.getExplain() + "</font>" + "<br/>");
													}
													out.println("===============================================================================================================================================================================<br/>");
												}
									}
									else if (searchCase.equals("2")) { // 联想词搜索
											    SchParam param = new SchParam();
											    param.setDebug(true);//设置为调试状态
											    param.setHighLight(true);
											    param.setKeyword(keyword.trim());
											    param.setPageSize(pageSize);
											
											    // 搜索
												GameSuggestionSearchService searchService = (GameSuggestionSearchService) SpringContextUtil.getBean("gameSuggestionSearchService");
												GameSuggestionBeans beans = (GameSuggestionBeans) searchService.search(param);
												
												out.println("<br/>");
												out.println("indexPath==" + searchService.getIndexPath() + "<br/>");
												out.println("查询：" + beans.getReport() + "<br/>");
												//out.println("<br/>");
												out.println("搜索结果总数：" + beans.getTotalCount() + " 条<br/>");
												out.println("搜索耗时：" + beans.getSearchCostTime() + " 毫秒<br/>");
												out.println("是否命中缓存：" + beans.isCached() + "<br/>");
												out.println("<br/>");

												List<BaseBean> beanList = beans.getBeanList();
												for (int i = 0; i < beanList.size(); i++) {
													GameSuggestionBean bean = (GameSuggestionBean) beanList.get(i);
													out.println((i + 1) + "、" + "<font color='blue'>" + bean.getName() + "</font>" + "（" + bean.getNum() + "," + "id=" + bean.getId() + "）" + "<br/>");
												}									     
									}
						%>
						<!-- font color='red'>注意：本测试页不写搜索日志</font-->
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>

