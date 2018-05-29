package com.gionee.game.search.server.service.impl;

import java.util.Iterator;
import java.util.List;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.Sort;
import org.apache.lucene.search.SortField;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.search.TopFieldDocs;
import org.apache.lucene.search.highlight.Highlighter;
import org.apache.lucene.search.highlight.NullFragmenter;
import org.apache.lucene.search.highlight.QueryScorer;
import org.apache.lucene.search.highlight.SimpleHTMLFormatter;

import com.gionee.game.search.index.job.GameIndexJob;
import com.gionee.game.search.server.analysis.PatternAnalyzer;
import com.gionee.game.search.server.bean.otherBean.IndexBean;
import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.paramBean.SchParamConstants;
import com.gionee.game.search.server.bean.resultBean.BaseBean;
import com.gionee.game.search.server.bean.resultBean.GameSuggestionBean;
import com.gionee.game.search.server.bean.resultBeans.BaseBeans;
import com.gionee.game.search.server.bean.resultBeans.GameSuggestionBeans;
import com.gionee.game.search.server.model.QuerySort;
import com.gionee.game.search.server.model.TextPinyin;
import com.gionee.game.search.server.util.PinyinUtil;
import com.gionee.game.search.server.util.QueryUtil;
import com.gionee.game.search.server.util.SearchUtil;
import com.gionee.game.search.server.util.StringUtil;

public class GameSuggestionSearchServiceImpl extends SearchServiceImplBase {
	private static final Log logger = LogFactory
			.getLog(GameSuggestionSearchServiceImpl.class);
	private Analyzer analyzer = new PatternAnalyzer("");

	// 索引工作
	private GameIndexJob indexJob;

	/**
	 * 搜索
	 * 
	 * @schParam
	 * @return
	 */
	public BaseBeans search(SchParam schParam) {
		GameSuggestionBeans beans = new GameSuggestionBeans();

		// 禁词或null或空
		if (schParam.getForbid() == SchParamConstants.FORBID_YES
				|| null == schParam.getKeyword()
				|| schParam.getKeyword().trim().isEmpty()) {
			return beans;
		}

		// 关键词处理
		String keyword = schParam.getKeyword().trim();
		keyword = keyword.replaceAll("\\s+", " ");

		try {
			// 校验pageSize
			if (schParam.getPageSize() <= 0) {
				schParam.setPageSize(10);// 默认值
			}

			// 得到检索器
			IndexSearcher searcher = indexJob.getSearcher();

			// 得到查询排序对象
			QuerySort querySort = getQuerySort(keyword);

			// 搜索,得到结果
			TopFieldDocs topDocs = searcher.search(querySort.getTotalQuery(),
					schParam.getPageSize(), querySort.getSort());
			if (topDocs.totalHits > 0) {
				beans = createBeans(searcher, topDocs, schParam);
			}

			// 高亮结果
			if (schParam.isHighLight() && querySort.getHighLightQuery() != null) {
				highLight(beans, querySort.getHighLightQuery());
			}

			// 设置查询报告
			if (schParam.isDebug()) {
				beans.setReport(querySort.getTotalQuery().toString());
			}
		} catch (Exception e) {
			logger.error(e, e);
		}

		return beans;
	}

	/**
	 * 高亮结果
	 * 
	 * @param beans
	 * @param query
	 */
	private void highLight(GameSuggestionBeans beans, Query query) {
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
					GameSuggestionBean bean = (GameSuggestionBean) baseBean;

					// 高亮名称
					bean.setName(SearchUtil.highLight(bean.getName(),
							highlighter, analyzer));
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
	 * @param schParam
	 * @return
	 * @throws Exception
	 */
	private GameSuggestionBeans createBeans(IndexSearcher searcher,
			TopDocs topDocs, SchParam schParam) throws Exception {
		GameSuggestionBeans beans = new GameSuggestionBeans();
		beans.setTotalCount(topDocs.totalHits);

		// 获取搜索结果
		ScoreDoc[] scoreDocs = topDocs.scoreDocs;
		for (int i = 0; i < scoreDocs.length; i++) {
			try {
				Document doc = searcher.doc(scoreDocs[i].doc);

				GameSuggestionBean bean = new GameSuggestionBean();
				bean.setId(doc.get("id"));
				bean.setName(doc.get("name"));
				if (schParam.isDebug()) {
					bean.setNum(doc.get("monthDownloads") + ","
							+ doc.get("downloads"));
				}
				beans.addBean(bean);
			} catch (Exception e) {
				logger.error(e, e);
			}
		}

		return beans;
	}

	/**
	 * 删除跟关键词相同的结果
	 * 
	 * @param beans
	 * @param keyword
	 * @return 有删除：true，无删除：false
	 */
	private boolean deleteSame(GameSuggestionBeans beans, String keyword) {
		boolean isDeleted = false;
		List<BaseBean> list = beans.getBeanList();
		if (list.size() > 0) {
			Iterator<BaseBean> iter = list.iterator();
			while (iter.hasNext()) {
				GameSuggestionBean bean = (GameSuggestionBean) iter.next();
				if (bean.getName().equalsIgnoreCase(keyword)) {
					iter.remove();
					isDeleted = true;
				}
			}
		}
		return isDeleted;
	}

	/**
	 * 得到查询排序对象
	 * 
	 * @param keyword
	 * @return
	 */
	private QuerySort getQuerySort(String keyword) {
		// 查询排序对象
		QuerySort querySort = new QuerySort();

		// 设置查询条件
		setQuery(querySort, keyword);

		// 设置排序方式
		Sort sort = new Sort(new SortField[] {
				new SortField("monthDownloads", SortField.Type.LONG, true),
				new SortField("downloads", SortField.Type.LONG, true) });
		querySort.setSort(sort);

		return querySort;
	}

	/**
	 * 设置查询条件
	 * 
	 * @param querySort
	 * @param keyword
	 */
	private void setQuery(QuerySort querySort, String keyword) {
		BooleanQuery keywordQuery = new BooleanQuery();

		TextPinyin textPinyin = PinyinUtil.getTextPinyin(keyword);
		String processedKeyword = textPinyin.getText();

		char endingChar = processedKeyword.charAt(processedKeyword.length() - 1);
		if (StringUtil.isAlphaNumeric(endingChar)) {
			if (StringUtil.isAlphaNumericString(processedKeyword)) { // 字母数字关键词(可能是英文单词，也可能是拼音)
				// 示例：angry、doudizhu(斗地主)、ddz(斗地主)、3dquanmin(3D全民)、3dqm(3D全民)、hongjing4(红警4)、hj4(红警4)
				keywordQuery.add(
						QueryUtil.getPhraseQuery("suggestionNameFullPinYin",
								processedKeyword, analyzer),
						BooleanClause.Occur.SHOULD);
				keywordQuery.add(QueryUtil.getPhraseQuery(
						"suggestionNameAcronymPinYin", processedKeyword,
						analyzer), BooleanClause.Occur.SHOULD);
			} else { // 示例："斗地zh"
				// 全拼查询
				if (!textPinyin.getFullPinyin().isEmpty()) {
					keywordQuery.add(QueryUtil.getPhraseQuery(
							"suggestionNameFullPinYin",
							textPinyin.getFullPinyin(), analyzer),
							BooleanClause.Occur.MUST);
				}

				// 中文部分查询
				for (int i = processedKeyword.length() - 2; i >= 0; i--) {
					if (!StringUtil.isAlphaNumeric(processedKeyword.charAt(i))) {
						keywordQuery.add(
								QueryUtil.getPhraseQuery("suggestionName",
										processedKeyword.substring(0, i + 1),
										analyzer), BooleanClause.Occur.MUST);
						break;
					}
				}
			}
		} else { // 搜索词查询
			keywordQuery.add(QueryUtil.getPhraseQuery("suggestionName",
					processedKeyword, analyzer), BooleanClause.Occur.MUST);
		}

		// 设置查询
		querySort.setKeywordQuery(keywordQuery);
		querySort.setTotalQuery(keywordQuery);
		querySort.setHighLightQuery(QueryUtil.getPhraseQuery("suggestionName",
				keyword, analyzer));
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
	}
	
	/**
	 * 重建索引
	 * 
	 */
	public void recreateIndex() {
	}

	@Override
	public String getIndexPath() {
		return indexJob.getIndexPath();
	}

	@Override
	public IndexBean getIndexBean() {
		return null;
	}

	public GameIndexJob getIndexJob() {
		return indexJob;
	}

	public void setIndexJob(GameIndexJob indexJob) {
		this.indexJob = indexJob;
	}

	public static void main(String[] args) {
	}
}
