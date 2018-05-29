package com.gionee.game.search.server.bean.paramBean;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

/**
 *基本搜索参数类
 *
 */
public class SchParam implements Cloneable {
	private String keyword = ""; // 搜索关键词
	private String searchId = ""; // 搜索ID
	private int pageNum = 1; // 页码
	private int pageSize = 30; // 页大小(每页显示条数)
	private int channelId = 0; // 频道号
	private int subChannelId = 0; // 子频道号
	private int searchType = 0; // 搜索类型
	private int sort = 0; // 排序方式
	private boolean highLight = false; // 是否要高亮命中词
	
	/** ******* 记录日志 ********* */
	private boolean log = true; // 是否记录搜索日志
	private int forbid = SchParamConstants.FORBID_NO; // 禁词标识(0:非禁词,1:禁词)	
	private int searchAction = SchParamConstants.SEARCH_ACTION_UNKNOWN; // 搜索动作
	private int searchFrom = SchParamConstants.SEARCH_FROM_UNKNOWN; // 搜索来源渠道
	private String ip = "";//用户ip
	private String ua = "";//客户端ua
	private String uid = "";//用户id(用户唯一标识)
	private String account = "";//用户账号

	// 是否为调试状态(在调试状态下，返回的搜索结果会带有一些调试信息，在线上运行时应设为"false")
	private boolean debug = false;

	public String getKeyword() {
		return keyword;
	}

	public void setKeyword(String keyword) {
		this.keyword = keyword == null ? "" : keyword.trim();
	}

	public String getSearchId() {
		return searchId;
	}

	public void setSearchId(String searchId) {
		this.searchId = searchId;
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

	public int getChannelId() {
		return channelId;
	}

	public void setChannelId(int channelId) {
		this.channelId = channelId;
	}

	public int getSubChannelId() {
		return subChannelId;
	}

	public void setSubChannelId(int subChannelId) {
		this.subChannelId = subChannelId;
	}

	public boolean isLog() {
		return log;
	}

	public void setLog(boolean log) {
		this.log = log;
	}

	public int getSearchAction() {
		return searchAction;
	}

	public void setSearchAction(int searchAction) {
		this.searchAction = searchAction;
	}

	public int getForbid() {
		return forbid;
	}

	public void setForbid(int forbid) {
		this.forbid = forbid;
	}
	

	public boolean isHighLight() {
		return highLight;
	}

	public void setHighLight(boolean highLight) {
		this.highLight = highLight;
	}

	public int getSort() {
		return sort;
	}

	public void setSort(int sort) {
		this.sort = sort;
	}

	public boolean isDebug() {
		return debug;
	}

	public void setDebug(boolean debug) {
		this.debug = debug;
	}

	public int getSearchType() {
		return searchType;
	}

	public void setSearchType(int searchType) {
		this.searchType = searchType;
	}
	
	public String getAccount() {
		return account;
	}

	public void setAccount(String account) {
		this.account = account;
	}

	public String toString() {
		return ToStringBuilder.reflectionToString(this,
				ToStringStyle.SHORT_PREFIX_STYLE);
	}
	
	@Override
	public SchParam clone() throws CloneNotSupportedException {
		return (SchParam) super.clone();
	}
}
