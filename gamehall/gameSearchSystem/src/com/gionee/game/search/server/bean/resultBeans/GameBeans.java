package com.gionee.game.search.server.bean.resultBeans;


/**
 * 游戏搜索结果列表
 *
 */
public class GameBeans extends BaseBeans {
	private static final long serialVersionUID = 5104356242655643675L;
	
	/*****回传的搜索参数-开始************************************/
	// 页码
	private int pageNum;
	// 页大小
	private int pageSize;
	/*****回传的搜索参数-结束************************************/
	
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
}
