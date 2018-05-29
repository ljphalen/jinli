package com.gionee.game.search.server.util;

import java.util.List;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.DisjunctionMaxQuery;
import org.apache.lucene.search.PhraseQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.spans.SpanNearQuery;
import org.apache.lucene.search.spans.SpanQuery;
import org.apache.lucene.search.spans.SpanTermQuery;

public class QueryUtil {
	private static final Log log = LogFactory.getLog(QueryUtil.class);
	
	/**
	 * 得到类标题查询
	 * 
	 * @param field
	 * @param keyword
	 * @param analyzer
	 * @return 失败：null
	 */
	public static Query getTitleQuery(String field, String keyword, Analyzer analyzer) {
		if (keyword == null || keyword.length() == 0) {
			return null;
		}
		
		try {
			// 最大得分查询
			DisjunctionMaxQuery maxQuery = new DisjunctionMaxQuery(0.0f);

			// 短语查询
			PhraseQuery phraseQuery = getPhraseQuery(field, keyword, analyzer);
			if (null != phraseQuery) {
				phraseQuery.setSlop(1); // 使让比如"手机qq"能成为"手机qq2012"的短语
				maxQuery.add(phraseQuery);
			}

			// 全部命中查询
			Query fullHitQuery = getFullHitQuery(field, keyword, analyzer);
			if (null != fullHitQuery) {
				fullHitQuery.setBoost(0.1f);// 权重略低
				maxQuery.add(fullHitQuery);
			}

			return maxQuery;
		} catch (Exception e) {
			log.error(e, e);
		}
		
		return null;
	}

	/**
	 * 构造短语查询
	 * 
	 * @param field
	 * @param keyword
	 * @param analyzer
	 * @return 失败：null
	 */
	public static PhraseQuery getPhraseQuery(String field, String keyword,
			Analyzer analyzer) {
		if (keyword == null || keyword.length() == 0) {
			return null;
		}

		try {
			// 得到分词词条
			List<String> wordList = SearchUtil.getTokenList(keyword, analyzer);
			if (wordList.size() == 0) {
				return null;
			}
			// 构造短语查询
			PhraseQuery phraseQuery = new PhraseQuery();
			for (String word : wordList) {
				phraseQuery.add(new Term(field, word));
			}
			return phraseQuery;
		} catch (Exception e) {
			log.error(e, e);
		}
		return null;
	}

	/**
	 * 得到部分命中查询(包含全命中查询)
	 * 
	 * @param field
	 * @param keyword
	 * @param analyzer
	 * @return 失败：null
	 */
	public static Query getPartHitQuery(String field, String keyword,
			Analyzer analyzer) {
		try {
			// 得到分词列表
			List<String> wordList = SearchUtil.getTokenList(keyword, analyzer);
			if (wordList.size() == 0) {
				return null;
			}

			BooleanQuery partHitBQ = new BooleanQuery(true); // 部分命中查询
			for (String word : wordList) {
				partHitBQ.add(new TermQuery(new Term(field, word)),
						BooleanClause.Occur.SHOULD);
			}
			return partHitBQ;
		} catch (Exception e) {
			log.error(e, e);
		}

		return null;
	}

	/**
	 * 得到部分命中查询(包含全命中查询)
	 * 
	 * @param field
	 * @param keyword
	 * @param stopWords
	 * @param analyzer
	 * @return 失败：null
	 */
	public static Query getPartHitQuery(String field, String keyword,
			String[] stopWords, String searchword, Analyzer analyzer) {
		try {
			BooleanQuery blQuery = new BooleanQuery(true);
			List<String> wordList = SearchUtil.getTokenList(keyword, analyzer);
			if (wordList.size() > 0) {
				BooleanQuery partHitBQ = new BooleanQuery(true); // 部分命中查询
				for (String word : wordList) {
					partHitBQ.add(new TermQuery(new Term(field, word)),
							BooleanClause.Occur.SHOULD);
				}
				blQuery.add(partHitBQ, BooleanClause.Occur.MUST);
			}

			// 停止词查询
			Query swQuery = getStopWordQuery(field, stopWords, analyzer);
			if (swQuery != null) {
				swQuery.setBoost(0.001f);
				blQuery.add(swQuery, BooleanClause.Occur.SHOULD);
			}

			// 校验返回
			if (blQuery.getClauses().length > 0) {
				return blQuery;
			} else {
				return null;
			}
		} catch (Exception e) {
			log.error(e, e);
		}

		return null;
	}
	
	/**
	 * 得到部分命中查询(包含全命中查询)
	 * 
	 * @param field
	 * @param keyword
	 * @param stopWords
	 * @param analyzer
	 * @param minHitRate
	 *            (最小命中率)
	 * @return 失败：null
	 */
	public static Query getPartHitQuery(String field, String keyword,
			String[] stopWords, String searchword, Analyzer analyzer,
			float minHitRate) {
		try {
			BooleanQuery blQuery = new BooleanQuery(true);
			List<String> wordList = SearchUtil.getTokenList(keyword, analyzer);
			if (wordList.size() > 0) {
				BooleanQuery partHitBQ = new BooleanQuery(true); // 部分命中查询
				for (String word : wordList) {
					partHitBQ.add(new TermQuery(new Term(field, word)),
							BooleanClause.Occur.SHOULD);
				}
				partHitBQ.setMinimumNumberShouldMatch((int) Math.ceil(wordList
						.size() * minHitRate));
				blQuery.add(partHitBQ, BooleanClause.Occur.MUST);
			}

			// 停止词查询
			Query swQuery = getStopWordQuery(field, stopWords, analyzer);
			if (swQuery != null) {
				swQuery.setBoost(0.001f);
				blQuery.add(swQuery, BooleanClause.Occur.SHOULD);
			}

			// 校验返回
			if (blQuery.getClauses().length > 0) {
				return blQuery;
			} else {
				return null;
			}
		} catch (Exception e) {
			log.error(e, e);
		}

		return null;
	}

	/**
	 * 得到停止词查询
	 * 
	 * @param field
	 * @param stopWords
	 * @param analyzer
	 * @return 失败:null
	 */
	public static Query getStopWordQuery(String field, String[] stopWords,
			Analyzer analyzer) {
		if (stopWords == null || stopWords.length == 0) {
			return null;
		}

		// 停止词查询
		BooleanQuery swQuery = new BooleanQuery(true);
		for (String sw : stopWords) {
			swQuery.add(getPhraseQuery(field, sw, analyzer),
					BooleanClause.Occur.SHOULD);
		}
		return swQuery;
	}

	/**
	 * 得到字段查询
	 * 
	 * @param field
	 * @param keyword
	 * @param stopWords
	 * @param analyzer
	 * @return
	 */
	public static Query getPhraseQuery(String field, String keyword,
			String[] stopWords, Analyzer analyzer) {
		BooleanQuery blQuery = new BooleanQuery(true);

		// 字段查询
		Query fieldQuery = getPhraseQuery(field, keyword, analyzer);
		if (null != fieldQuery) {
			blQuery.add(fieldQuery, BooleanClause.Occur.MUST);
		}

		// 停止词查询
		Query swQuery = getStopWordQuery(field, stopWords, analyzer);
		if (swQuery != null) {
			swQuery.setBoost(0.001f);
			blQuery.add(swQuery, BooleanClause.Occur.SHOULD);
		}

		// 校验返回
		if (blQuery.getClauses().length > 0) {
			return blQuery;
		} else {
			return null;
		}
	}

	/**
	 * 得到全部命中且可以有间隔的查询(有序)
	 * 
	 * @param field
	 * @param keyword
	 * @param stopWords
	 * @param analyzer
	 * @return
	 */
	public static Query getFullHitQueryWithOrder(String field, String keyword,
			String[] stopWords, Analyzer analyzer) {
		try {
			BooleanQuery blQuery = new BooleanQuery(true);

			// 分词列表
			List<String> wordList = SearchUtil.getTokenList(keyword, analyzer);
			if (wordList.size() > 0) {
				SpanQuery[] spqs = new SpanTermQuery[wordList.size()];
				int i = 0;
				for (String word : wordList) {
					spqs[i++] = new SpanTermQuery(new Term(field, word));
				}
				SpanNearQuery spanNearQuery = new SpanNearQuery(spqs, 1024,
						true);
				blQuery.add(spanNearQuery, BooleanClause.Occur.MUST); // 全部命中，顺序一样且可以有间隔的跨度查询
			}

			// 停止词查询
			Query swQuery = getStopWordQuery(field, stopWords, analyzer);
			if (swQuery != null) {
				swQuery.setBoost(0.001f);
				blQuery.add(swQuery, BooleanClause.Occur.SHOULD);
			}

			// 校验返回
			if (blQuery.getClauses().length > 0) {
				return blQuery;
			} else {
				return null;
			}
		} catch (Exception e) {
			log.error(e, e);
		}

		return null;
	}

	/**
	 * 得到全部命中查询(无序)
	 * 
	 * @param field
	 * @param keyword
	 * @param analyzer
	 * @return 失败：null
	 */
	public static Query getFullHitQuery(String field, String keyword,
			Analyzer analyzer) {
		try {
			// 分词列表
			List<String> wordList = SearchUtil.getTokenList(keyword, analyzer);
			if (wordList.size() == 0) {
				return null;
			}

			// 构造查询
			BooleanQuery fullHitBQ = new BooleanQuery(true); // 全部命中查询
			for (String word : wordList) {
				fullHitBQ.add(new TermQuery(new Term(field, word)),
						BooleanClause.Occur.MUST);
			}

			return fullHitBQ;
		} catch (Exception e) {
			log.error(e, e);
		}

		return null;
	}

	/**
	 * 得到全部命中查询(无序)
	 * 
	 * @param field
	 * @param keyword
	 * @param stopWords
	 * @param analyzer
	 * @return 失败：null
	 */
	public static Query getFullHitQuery(String field, String keyword,
			String[] stopWords, Analyzer analyzer) {
		try {
			BooleanQuery blQuery = new BooleanQuery(true);

			// 分词列表
			List<String> wordList = SearchUtil.getTokenList(keyword, analyzer);
			if (wordList.size() > 0) {
				BooleanQuery fullHitBQ = new BooleanQuery(true); // 全部命中查询
				for (String word : wordList) {
					fullHitBQ.add(new TermQuery(new Term(field, word)),
							BooleanClause.Occur.MUST);
				}
				blQuery.add(fullHitBQ, BooleanClause.Occur.MUST);
			}

			// 停止词查询
			Query swQuery = getStopWordQuery(field, stopWords, analyzer);
			if (swQuery != null) {
				swQuery.setBoost(0.001f);
				blQuery.add(swQuery, BooleanClause.Occur.SHOULD);
			}

			// 校验返回
			if (blQuery.getClauses().length > 0) {
				return blQuery;
			} else {
				return null;
			}
		} catch (Exception e) {
			log.error(e, e);
		}

		return null;
	}
	
	/**
	 * 多值或查询
	 * 
	 * @param values(值之间用英文逗号分隔)
	 * @return 失败：null
	 */
	public static Query getMultiValueQuery(String field, String values) {
		if (field == null || field.trim().isEmpty() || values == null
				|| values.trim().isEmpty()) {
			return null;
		}
		
		DisjunctionMaxQuery maxQuery = new DisjunctionMaxQuery(0.0f); 
		String[] valueArr = values.split(",");
		for (String value : valueArr) {
			value = value.trim();
			if (!value.isEmpty()) {
				maxQuery.add(new TermQuery(new Term(field, value)));
			}
		}
		if (maxQuery.getDisjuncts().size() > 0) {
			return maxQuery;
		}
		else {
			return null;
		}
	}
	
	/**
	 * 多值多域或查询
	 * 
	 * @param fields
	 * @param values(值之间用英文逗号分隔)
	 * @return 失败：null
	 */
	public static Query getMultiValueMultiFieldQuery(String[] fields, String values) {
		if (fields == null || fields.length == 0 || values == null
				|| values.trim().isEmpty()) {
			return null;
		}
		
		DisjunctionMaxQuery maxQuery = new DisjunctionMaxQuery(0.0f); 
		String[] valueArr = values.split(",");
		for (String field : fields) {
			field = field.trim();
			if (!field.isEmpty()) {
				for (String value : valueArr) {
					value = value.trim();
					if (!value.isEmpty()) {
						maxQuery.add(new TermQuery(new Term(field, value)));
					}
				}
			}
		}
		if (maxQuery.getDisjuncts().size() > 0) {
			return maxQuery;
		}
		else {
			return null;
		}
	}

	public static void main(String[] args) throws Exception {
	}
}
