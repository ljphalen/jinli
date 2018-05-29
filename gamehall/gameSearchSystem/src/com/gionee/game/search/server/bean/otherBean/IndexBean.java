package com.gionee.game.search.server.bean.otherBean;

import com.gionee.game.search.server.bean.resultBean.BaseBean;

public class IndexBean extends BaseBean {
	private static final long serialVersionUID = -8983139027797318353L;

	// 索引路径
	private String indexPath;
	
	// 可搜索文档数
	private int searchDocNum;
	
	// 有效索引文档数
	private int numDocs;
	
	// 索引中删除的文档数
	private int numDeletedDocs;
	
	// 最大索引文档号
	private int maxDoc;
	
	// 最大记录更新时间
	private String maxUpdateTime;

	public String getIndexPath() {
		return indexPath;
	}

	public void setIndexPath(String indexPath) {
		this.indexPath = indexPath;
	}

	public String getMaxUpdateTime() {
		return maxUpdateTime;
	}

	public void setMaxUpdateTime(String maxUpdateTime) {
		this.maxUpdateTime = maxUpdateTime;
	}

	public int getNumDeletedDocs() {
		return numDeletedDocs;
	}

	public void setNumDeletedDocs(int numDeletedDocs) {
		this.numDeletedDocs = numDeletedDocs;
	}

	public int getNumDocs() {
		return numDocs;
	}

	public void setNumDocs(int numDocs) {
		this.numDocs = numDocs;
	}

	public int getMaxDoc() {
		return maxDoc;
	}

	public void setMaxDoc(int maxDoc) {
		this.maxDoc = maxDoc;
	}

	public int getSearchDocNum() {
		return searchDocNum;
	}

	public void setSearchDocNum(int searchDocNum) {
		this.searchDocNum = searchDocNum;
	}
}
