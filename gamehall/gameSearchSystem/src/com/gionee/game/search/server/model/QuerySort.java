package com.gionee.game.search.server.model;

import org.apache.lucene.search.Query;
import org.apache.lucene.search.Sort;


/**
 * 查询和排序对象
 * 
 */
public class QuerySort {
	private Query totalQuery; // 总查询
	private Query keywordQuery; // 关键词查询
	private Query highLightQuery; // 高亮查询
	private Sort sort; // 排序对象
	public Query getTotalQuery() {
		return totalQuery;
	}
	public void setTotalQuery(Query totalQuery) {
		this.totalQuery = totalQuery;
	}
	public Query getKeywordQuery() {
		return keywordQuery;
	}
	public void setKeywordQuery(Query keywordQuery) {
		this.keywordQuery = keywordQuery;
	}
	public Query getHighLightQuery() {
		return highLightQuery;
	}
	public void setHighLightQuery(Query highLightQuery) {
		this.highLightQuery = highLightQuery;
	}
	public Sort getSort() {
		return sort;
	}
	public void setSort(Sort sort) {
		this.sort = sort;
	}
}
