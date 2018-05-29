package com.gionee.game.search.action.searchAction;

import java.io.PrintWriter;

import javax.servlet.http.HttpServletResponse;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.apache.struts2.StrutsStatics;

import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.paramBean.SchParamConstants;
import com.opensymphony.xwork2.ActionContext;
import com.opensymphony.xwork2.ActionSupport;

/**
 * 搜索Action基类
 * 
 */
@SuppressWarnings("serial")
public abstract class SearchActionBase extends ActionSupport {
	protected static final Log logger = LogFactory
			.getLog(SearchActionBase.class);
	
	// 关键词
	protected String keyword = "";

	// 页码
	protected int pageNum = 1;

	// 页大小
	protected int pageSize = 10;
	
	// 是否高亮搜索词
	protected int highLight = SchParamConstants.HIGHLIGHT_NO; 
	
	// 排序方式
	protected int sort = 0;
	
	// 是否记录搜索日志
	protected int log = SchParamConstants.LOG_YES;
	
	// 禁词标识
	protected int forbid = SchParamConstants.FORBID_NO;
	
	// 搜索行为
	protected int searchAction = SchParamConstants.SEARCH_ACTION_UNKNOWN;
	
	// 搜索来源渠道
	protected int searchFrom = SchParamConstants.SEARCH_FROM_UNKNOWN; 

	// 用户ip
	protected String ip = "";

	// 用户代理（浏览器访问为User-Agent的值）
	protected String ua = "";
	
	// 用户id(用户唯一标识)
	protected String uid = "";
	
	// 用户账号
	protected String account = "";

	public String getKeyword() {
		return keyword;
	}

	public void setKeyword(String keyword) {
		this.keyword = keyword;
	}

	public int getPageNum() {
		return pageNum;
	}

	public void setPageNum(int pageNum) {
		this.pageNum = pageNum;
	}

	public int getPageSize() {
		return pageSize;
	}

	public void setPageSize(int pageSize) {
		this.pageSize = pageSize;
	}

	public int getHighLight() {
		return highLight;
	}

	public void setHighLight(int highLight) {
		this.highLight = highLight;
	}

	public String getIp() {
		return ip;
	}

	public void setIp(String ip) {
		this.ip = ip;
	}

	public String getUa() {
		return ua;
	}

	public void setUa(String ua) {
		this.ua = ua;
	}

	public int getSort() {
		return sort;
	}

	public void setSort(int sort) {
		this.sort = sort;
	}

	public int getLog() {
		return log;
	}

	public void setLog(int log) {
		this.log = log;
	}

	public int getForbid() {
		return forbid;
	}

	public void setForbid(int forbid) {
		this.forbid = forbid;
	}

	public int getSearchAction() {
		return searchAction;
	}

	public void setSearchAction(int searchAction) {
		this.searchAction = searchAction;
	}

	public int getSearchFrom() {
		return searchFrom;
	}

	public void setSearchFrom(int searchFrom) {
		this.searchFrom = searchFrom;
	}

	public String getUid() {
		return uid;
	}

	public void setUid(String uid) {
		this.uid = uid;
	}

	public String getAccount() {
		return account;
	}

	public void setAccount(String account) {
		this.account = account;
	}

	/**
	 * 搜索方法
	 * 
	 */
	public abstract void search();
	
	/**
	 * 设置基本搜索参数
	 * 
	 * @param schParam
	 */
	protected void setSchParam(SchParam schParam) {
		schParam.setKeyword(keyword);
		schParam.setPageNum(pageNum);
		schParam.setPageSize(pageSize);
		schParam.setHighLight(highLight == SchParamConstants.HIGHLIGHT_YES);
		schParam.setSort(sort);
		schParam.setLog(log == SchParamConstants.LOG_YES);
		schParam.setForbid(forbid);
		schParam.setSearchAction(searchAction);
		schParam.setSearchFrom(searchFrom);
		schParam.setIp(ip);
		schParam.setUa(ua);
		schParam.setUid(uid);
		schParam.setAccount(account);
	}

	protected void writeJsonOut(String strjson) {
		try {
			HttpServletResponse response = (HttpServletResponse) ActionContext
					.getContext().get(StrutsStatics.HTTP_RESPONSE);
			response.setContentType("application/json;charset=utf-8");
			response.setCharacterEncoding("utf-8");
			PrintWriter pw = response.getWriter();
			pw.write(strjson);
			pw.flush();
			pw.close();
		} catch (Exception e) {
			logger.error(e, e);
		}
	}
}
