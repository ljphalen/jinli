package com.gionee.game.search.server.service.impl;

import java.io.IOException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.DisjunctionMaxQuery;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.MatchAllDocsQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.Sort;
import org.apache.lucene.search.SortField;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.search.highlight.Highlighter;
import org.apache.lucene.search.highlight.NullFragmenter;
import org.apache.lucene.search.highlight.QueryScorer;
import org.apache.lucene.search.highlight.SimpleHTMLFormatter;
import org.wltea.analyzer.lucene.IKAnalyzer;

import com.gionee.game.search.index.job.GameIndexJob;
import com.gionee.game.search.server.bean.otherBean.IndexBean;
import com.gionee.game.search.server.bean.paramBean.GameSchParam;
import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.paramBean.SchParamConstants;
import com.gionee.game.search.server.bean.resultBean.BaseBean;
import com.gionee.game.search.server.bean.resultBean.GameBean;
import com.gionee.game.search.server.bean.resultBeans.BaseBeans;
import com.gionee.game.search.server.bean.resultBeans.GameBeans;
import com.gionee.game.search.server.model.QuerySort;
import com.gionee.game.search.server.util.Page;
import com.gionee.game.search.server.util.QueryUtil;
import com.gionee.game.search.server.util.SearchUtil;

public class GameSearchServiceImpl extends SearchServiceImplBase {
	private static final Log logger = LogFactory.getLog(GameSearchServiceImpl.class);
	private static Analyzer analyzer = new IKAnalyzer();

	// 索引工作
	private GameIndexJob indexJob;

	/** 
	 * 查询搜索结果
	 * 
	 * @schParam
	 * @return
	 */
	public BaseBeans search(SchParam schParam) {
		GameBeans beans = new GameBeans();

		// 禁词
		if (schParam.getForbid() == SchParamConstants.FORBID_YES) {
			return beans;
		}

		try {
			// 得到搜索器
			IndexSearcher searcher = indexJob.getSearcher();

			// 总查询
			Query totalQuery = null;

			// 查询和排序对象
			QuerySort querySort = getQuerySort(schParam);
			
			// 总查询条件
			totalQuery = querySort.getTotalQuery();

			// 校验翻页参数
			SearchUtil.checkPage(schParam);
			
			// 搜索,得到结果
			TopDocs topDocs = searcher.search(totalQuery, schParam.getPageNum()
					* schParam.getPageSize(), querySort.getSort());
			Page page = super.getPage(topDocs.totalHits, schParam.getPageNum(), schParam.getPageSize());
			if (topDocs.totalHits > 0) {
				beans = createBeans(searcher, topDocs, page, schParam);
			}
			
			// 高亮结果
			if (schParam.isHighLight() && querySort.getKeywordQuery() != null) {
				highLight(beans, querySort.getKeywordQuery(), schParam);
			}
			
			// 设置查询报告
			logger.debug("totalQuery==" + totalQuery);
			if (schParam.isDebug()) {
				beans.setReport(totalQuery.toString()); // 设置查询报告
			}
		} catch (Exception e) {
			logger.error(e, e);
		}

		return beans;
	}
	
	/**
	 * 得到查询排序对象
	 * 
	 * @param schParam
	 * @return 
	 */
	private QuerySort getQuerySort(SchParam schParam) {
		GameSchParam gameSchParam = (GameSchParam) schParam;
		
		// 查询排序对象
		QuerySort querySort = new QuerySort();
		
		// 构建查询条件
		BooleanQuery totalQuery = new BooleanQuery(true);

		// 关键词查询
		if (gameSchParam.getKeyword() != null
				&& !gameSchParam.getKeyword().trim().isEmpty()) {
			Query keywordQuery = getKeywordQuery(gameSchParam.getKeyword());
			totalQuery.add(keywordQuery, BooleanClause.Occur.MUST);
			querySort.setKeywordQuery(keywordQuery);
		}

		// 查询条件
		if (totalQuery.getClauses().length > 0) { // 有查询条件
			querySort.setTotalQuery(totalQuery);
		} else { // 无查询条件，则搜索全部资源
			querySort.setTotalQuery(new MatchAllDocsQuery());
		}

		// 排序
		switch (gameSchParam.getSort()) {
		default: // 按相关度排序
			Sort sort = new Sort(new SortField[] { SortField.FIELD_SCORE,
					new SortField("monthDownloads", SortField.Type.LONG, true),
					new SortField("downloads", SortField.Type.LONG, true) });
			querySort.setSort(sort);
			break;
		}
		
		return querySort;
	}
	
	/**
	 * 得到关键词查询
	 * 
	 * @param keyword
	 * @return
	 */
	private Query getKeywordQuery(String keyword) {
		// 构造关键词查询条件
		DisjunctionMaxQuery maxQuery = new DisjunctionMaxQuery(0.0f);
		
		// 名称查询
		Query query = QueryUtil.getPartHitQuery("name", keyword, analyzer);
		if (query != null) {
			query.setBoost(100.0f);
			maxQuery.add(query);
		}
		
		// 简述查询
		query = QueryUtil.getPartHitQuery("resume", keyword, analyzer);
		if (query != null) {
			query.setBoost(1.0f);
			maxQuery.add(query);
		}
		
		// 标签查询
		query = QueryUtil.getPartHitQuery("label", keyword, analyzer);
		if (query != null) {
			query.setBoost(0.01f);
			maxQuery.add(query);
		}
		
		return maxQuery;
	}

	/**
	 * 高亮结果
	 * 
	 * @param beans
	 * @param query
	 * @param schParam
	 */
	private void highLight(GameBeans beans, Query query, SchParam schParam) {
		if (beans == null || query == null) {
			return;
		}

		// 高亮器
		SimpleHTMLFormatter simpleHTMLFormatter = new SimpleHTMLFormatter(
				SchParamConstants.HIGHLIGHT_BEGIN,
				SchParamConstants.HIGHLIGHT_END);
		Highlighter highlighter = new Highlighter(simpleHTMLFormatter,
				new QueryScorer(query));
		highlighter.setTextFragmenter(new NullFragmenter());

		List<BaseBean> beanList = beans.getBeanList();
		if (beanList != null && beanList.size() > 0) {
			for (BaseBean baseBean : beanList) {
				try {
					GameBean bean = (GameBean) baseBean;
					
					// 高亮名称
					bean.setName(SearchUtil.highLight(bean.getName(), highlighter, analyzer));
					
					if (schParam.isDebug()) {
						// 高亮简述
						bean.setResume(SearchUtil.highLight(bean.getResume(), highlighter, analyzer));
						
						// 高亮标签
						bean.setLabel(SearchUtil.highLight(bean.getLabel(), highlighter, analyzer));
					}
				} catch (Exception e) {
					logger.error(e, e);
				}
			}
		}
	}


	/**
	 * 创建搜索结果
	 * 
	 * @param searcher
	 * @param topDocs
	 * @param page
	 * @param schParam
	 * @return
	 * @throws Exception
	 */
	private GameBeans createBeans(IndexSearcher searcher, TopDocs topDocs,
			Page page, SchParam schParam) throws Exception {
		GameBeans beans = new GameBeans();
		beans.setTotalCount(topDocs.totalHits);

		ScoreDoc[] scoreDocs = topDocs.scoreDocs;
		int startIndex = page.getStartIndex();
		int endIndex = page.getEndIndex();
		for (int i = startIndex; i < endIndex; i++) {
			try {
				Document doc = searcher.doc(scoreDocs[i].doc);
				GameBean bean = getSearchResultBean(doc, schParam);
				if (schParam.isDebug()) {
					bean.setExplain(scoreDocs[i].toString());
				}
				beans.addBean(bean);
			} catch (IOException e) {
				logger.error(e, e);
			}
		}

		return beans;
	}
	
	/**
	 * 得到搜索结果页bean
	 * 
	 * @param doc
	 * @param schParam
	 * @return
	 * @throws CorruptIndexException
	 * @throws IOException
	 */
	private GameBean getSearchResultBean(Document doc, SchParam schParam)
			throws CorruptIndexException, IOException {
		GameBean bean = new GameBean();
		
		// 设置基本信息
		bean.setId(doc.get("id"));
		bean.setName(doc.get("name"));
		bean.setResume(doc.get("resume"));
		bean.setLabel(doc.get("label"));
		bean.setCreateTime(doc.get("createTime"));
		
		// 设置调试信息
		if (schParam.isDebug()) {
			bean.setMonthDownloads(doc.get("monthDownloads"));
			bean.setDownloads(doc.get("downloads"));
			bean.setOnlineTime(doc.get("onlineTime"));
		}

		return bean;
	}
	
	/**
	 * id搜索
	 * 
	 * @param schParam
	 */
	public BaseBeans searchById(SchParam schParam) {
		return null;
	}

	/**
	 * 分类搜索
	 * 
	 * @return 搜索结果集
	 */
	public BaseBeans searchByType(SchParam schParam) {
		return null;
	}
	
	/**
	 * 同步索引(与数据库)
	 * 
	 */
	public void syncIndex() {
		indexJob.syncIndex();
	}
	
	
	/** 
	 * 重建索引
	 * 
	 */
	public void recreateIndex() {
		indexJob.recreateIndex();
	}
	
	@Override
	public String getIndexPath() {
		return indexJob.getIndexPath();
	}
	
	public GameIndexJob getIndexJob() {
		return indexJob;
	}

	public void setIndexJob(GameIndexJob indexJob) {
		this.indexJob = indexJob;
	}

	/**
	 * 得到索引信息bean
	 * 
	 * @return 失败:null
	 */
	public IndexBean getIndexBean() {
		try {
			IndexSearcher searcher = indexJob.getSearcher();
			IndexReader reader = searcher.getIndexReader();

			IndexBean indexBean = new IndexBean();
			
			indexBean.setIndexPath(indexJob.getIndexPath());
			indexBean.setNumDocs(reader.numDocs());
			indexBean.setNumDeletedDocs(reader.numDeletedDocs());
			indexBean.setMaxDoc(reader.maxDoc());

			Sort sort = new Sort(new SortField("opTimeLong", SortField.Type.LONG, true));
			TopDocs topDocs = searcher.search(new MatchAllDocsQuery(), 1, sort);
			indexBean.setSearchDocNum(topDocs.totalHits);
			if (topDocs.totalHits > 0) {
				Document doc = searcher.doc(topDocs.scoreDocs[0].doc);
				DateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
				String maxUpdateTime = sdf.format(new Date(Long.parseLong(doc.get("opTimeLong"))));
				indexBean.setMaxUpdateTime(maxUpdateTime);
			}

			return indexBean;
		} catch (Exception e) {
			logger.error(e, e);
		}

		return null;
	}
	
	public static void main(String[] args) {
	}
}
