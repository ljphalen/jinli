package com.gionee.game.search.server.bean.resultBeans;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

import com.gionee.game.search.server.bean.resultBean.BaseBean;

/**
 * 搜索结果集基础类
 *
 */
public class BaseBeans implements Serializable {
	private static final long serialVersionUID = -6070543507603834157L;

	// 查询报告
	private String report;

	// 搜索结果总数
	private int totalCount;

	// 搜索耗时(单位：毫秒(ms))
	private long searchCostTime;

	// 是否命中缓存
	private boolean cached;

	// 当前页搜索结果集
	private List<BaseBean> beanList = new ArrayList<BaseBean>();

	public String getReport() {
		return report;
	}

	public void setReport(String report) {
		this.report = report;
	}

	public int getTotalCount() {
		return totalCount;
	}

	public void setTotalCount(int totalCount) {
		this.totalCount = totalCount;
	}

	public List<BaseBean> getBeanList() {
		return beanList;
	}

	public void setBeanList(List<BaseBean> beanList) {
		this.beanList = beanList;
	}

	public void addBean(BaseBean baseBean) {
		beanList.add(baseBean);
	}

	public long getSearchCostTime() {
		return searchCostTime;
	}

	public void setSearchCostTime(long searchCostTime) {
		this.searchCostTime = searchCostTime;
	}

	public boolean isCached() {
		return cached;
	}

	public void setCached(boolean cached) {
		this.cached = cached;
	}
}
