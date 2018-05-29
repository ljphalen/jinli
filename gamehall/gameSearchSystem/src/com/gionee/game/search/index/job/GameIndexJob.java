package com.gionee.game.search.index.job;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.miscellaneous.PerFieldAnalyzerWrapper;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.AtomicReader;
import org.apache.lucene.index.BinaryDocValues;
import org.apache.lucene.index.DirectoryReader;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.IndexWriterConfig;
import org.apache.lucene.index.IndexWriterConfig.OpenMode;
import org.apache.lucene.index.SlowCompositeReaderWrapper;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.FieldCache;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.MatchAllDocsQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.Sort;
import org.apache.lucene.search.SortField;
import org.apache.lucene.search.SortField.Type;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.search.similarities.Similarity;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;
import org.wltea.analyzer.lucene.IKAnalyzer;

import com.gionee.game.search.index.dao.GameDao;
import com.gionee.game.search.index.model.Game;
import com.gionee.game.search.server.analysis.PatternAnalyzer;
import com.gionee.game.search.server.model.TextPinyin;
import com.gionee.game.search.server.similarities.MySimilarity;
import com.gionee.game.search.server.util.Constants;
import com.gionee.game.search.server.util.PinyinUtil;
import com.gionee.game.search.server.util.SearchUtil;

/**
 * 游戏索引工作
 * 
 */
public class GameIndexJob extends BaseIndexJob {
//	private static final long MAX_INCREMENT_INDEX_NUMBER = 1300000; // 最大增量索引资源数
	private static final int ROWS = 20000;

	// 资源dao
	private GameDao gameDao;

	// 上次更新时间
	private Date lastUpdateTime;
	
	// 索引路径
	private String indexPath;

	// 索引写者
	private IndexWriter indexWriter;

	// 索引搜索器
	private IndexSearcher searcher;

	// 上次索引同步日期(年中的天-DAY_OF_YEAR)---每天第一次执行任务时检查
	private int lastSyncIndexDay;
	
	// 是否手动同步索引中
	private boolean humanSyncingIndex;
	
	// 是否手动重建索引中
	private boolean humanRecreatingIndex;

	// 相似器
	private Similarity mySimilarity = new MySimilarity();
	
	// 索引字段
	private List<MyField> fields = new ArrayList<MyField>();
	{
		// 初始化
		try {
			fields.add(new MyField("id", STORE_YES_ANALYZED_NO, Game.class));
			fields.add(new MyField("name", STORE_YES_ANALYZED, Game.class));
			fields.add(new MyField("resume", STORE_YES_ANALYZED, Game.class));
			fields.add(new MyField("label", STORE_YES_ANALYZED, Game.class));
			fields.add(new MyField("monthDownloads", STORE_YES_LONG, Game.class));
			fields.add(new MyField("downloads", STORE_YES_LONG, Game.class));
			fields.add(new MyField("createTime", STORE_YES_LONG, Game.class));
			fields.add(new MyField("onlineTime", STORE_YES_LONG, Game.class));
			fields.add(new MyField("opTimeLong", STORE_YES_LONG, Game.class));
			
			fields.add(new MyField("suggestionName", STORE_NO_ANALYZED, Game.class));
			fields.add(new MyField("suggestionNameFullPinYin", STORE_NO_ANALYZED, Game.class));
			fields.add(new MyField("suggestionNameAcronymPinYin", STORE_NO_ANALYZED, Game.class));
		} catch (Exception e) {
			logger.error(e.getMessage(), e);
		}
	}
	
	/**
	 * 初始化实例
	 * 
	 */
	public void init() {
		// 启动一个线程初始化,以免影响系统启动时间
		new Thread() {
			public void run() {
				// 初始化相关数据
				initial();
			}
		}.start();
	}
	
	/**
	 * 初始化相关数据
	 * 
	 */
	private synchronized void initial() {
		try {
			// 索引路径校验
			indexPath = indexPath.trim();
			logger.info("indexPath=" + indexPath);
			if (indexPath.startsWith("${") || indexPath.isEmpty()) {
				logger.info("no config index path!");
				return;
			}

			// 索引目录
			Directory indexDir = FSDirectory.open(new File(indexPath));

			// 分词器
			Analyzer defaultAnalyzer = new IKAnalyzer();
			Map<String, Analyzer> fieldAnalyzers = new HashMap<String, Analyzer>();
			// fieldAnalyzers.put("label", new PatternAnalyzer("[ ,，;；、]"));
			Analyzer emptyAnalyzer = new PatternAnalyzer("");
			fieldAnalyzers.put("suggestionName", emptyAnalyzer);
			fieldAnalyzers.put("suggestionNameFullPinYin", emptyAnalyzer);
			fieldAnalyzers.put("suggestionNameAcronymPinYin", emptyAnalyzer);
			PerFieldAnalyzerWrapper analyzer = new PerFieldAnalyzerWrapper(defaultAnalyzer, fieldAnalyzers);

			// 索引配置
			IndexWriterConfig iwc = new IndexWriterConfig(Constants.LUCENE_CURRENT_VERSION, analyzer);
			iwc.setOpenMode(OpenMode.CREATE_OR_APPEND);
			iwc.setSimilarity(mySimilarity); // 设置相似器

			// 索引写者
			indexWriter = new IndexWriter(indexDir, iwc);

			// 创建近实时reader
			IndexReader indexReader = DirectoryReader.open(indexWriter, true);
			logger.debug("indexReader.numDocs()=" + indexReader.numDocs());
			logger.debug("indexReader.numDeletedDocs()=" + indexReader.numDeletedDocs());
			logger.debug("indexReader.maxDoc()="	+ indexReader.maxDoc());

			// 构建搜索器
			searcher = new IndexSearcher(indexReader);
			searcher.setSimilarity(mySimilarity);

			// 设置上次更新时间
			setLastUpdateTime();

			// 设置为当天
			lastSyncIndexDay = Calendar.getInstance().get(Calendar.DAY_OF_YEAR);
			
			logger.info("init end!");
		} catch (Exception e) {
			logger.error(e.getMessage(), e);
		}
	}

	/**
	 * 任务执行方法
	 * 
	 */
	public synchronized void execute() {
		try {
			// 索引写者校验
			if (indexWriter == null) {
				logger.warn("indexWriter is null");
				return;
			}
			
			// 创建索引
			if (null == lastUpdateTime) {// 全量索引(第一次创建索引)
				createFullIndex();
			} else { // 增量索引
				createIncrementIndex();
			}
		} catch (Exception e) {
			logger.error(e, e);
		}
	}
	
	/**
	 * 创建全量索引
	 * 
	 */
	private synchronized void createFullIndex() {
		try {
			logger.info("start create full index");

			// 得到数据库资源最大更新时间
			Date maxUpdateTime = gameDao.findMaxUpdateTime();
			logger.debug("maxUpdateTime=" + new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(maxUpdateTime));
			
			long total = 0;
			long minId = gameDao.findMinID();
			long maxId = gameDao.findMaxID();
			long startId = minId;
			long endId = 0;
			boolean isFirstCreateIndex = (null == lastUpdateTime);// 是否第一次创建索引
			List<Game> resList = null;// 资源列表
			long begin = System.currentTimeMillis();
			do {
				// 全量索引
				endId = startId + ROWS;
				if (endId > maxId) {
					endId = maxId + 1; // 只取到maxId为止
				}
				
				// 查询资源
				long b = System.currentTimeMillis();
				resList = gameDao.findFullIndexRes(startId, endId);
				long e = System.currentTimeMillis();
				logger.info("select data, resList.size()=" + resList.size()
						+ ",elasped time " + ((e - b) / 1000) + " seconds");
				
				if (isFirstCreateIndex) {// 添加到索引
					createIntoIndex(resList, true);
				} else {// 更新到索引
					createIntoIndex(resList, false);
				}
				
				// 统计索引资源数
				total += (resList != null ? resList.size() : 0);
				logger.info("full index num:" + total);
				
				startId +=  ROWS;
			} while (endId <= maxId);
			long end = System.currentTimeMillis();
			logger.info("end create full index, index total num:"
					+ total + ",elasped time " + ((end - begin) / 1000)
					+ " seconds" + ",isFirstCreateIndex=" + isFirstCreateIndex);
			lastUpdateTime = maxUpdateTime;

			// 提交索引，刷新搜索器
			commitIndexAndRefreshSearcher();

			// 不是第一次全量索引，需要与数据库做下同步
			if (!isFirstCreateIndex) {
				syncIndexWithDatabase();
			}
		} catch (Exception e) {
			logger.error(e, e);
		}
	}
	
	/**
	 * 创建增量索引
	 * 
	 */
	private synchronized void createIncrementIndex() {
		try {
			// 得到数据库资源最大更新时间
			Date maxUpdateTime = gameDao.findMaxUpdateTime();
			logger.debug("maxUpdateTime=" + new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(maxUpdateTime));
			
			// 无资源更新
			if (maxUpdateTime.getTime() <= lastUpdateTime.getTime()) {
				logger.info("no resource update!");
				syncIndexByDaily(); // 每日同步索引
				return;
			}
			
//			// 大增量索引，转为全量索引(速度更快)
//			long incremNum = gameDao.findIncrementIndexResNumber(lastUpdateTime,
//					maxUpdateTime); 
//			logger.info("increment index resource number:" + incremNum);
//			if (incremNum > MAX_INCREMENT_INDEX_NUMBER) {
//				createFullIndex();
//				return;
//			}
						
			// 停1秒后,再取数据创建索引(为了确保最后1秒钟数据库数据的完整性)
			Thread.sleep(1000);
			
			logger.info("start create increment index");

			long start = 0;
			long incremTotal = 0; // 增量索引总数
			long begin = System.currentTimeMillis();
			List<Game> resList = null;// 资源列表
			do {
				// 增量索引
				resList = gameDao.findIncrementIndexRes(lastUpdateTime,
						maxUpdateTime, start, ROWS);
				createIntoIndex(resList, false);

				// 统计索引资源数
				incremTotal += (resList != null ? resList.size() : 0);
				logger.info("inrement index num:" + incremTotal);

				start += ROWS;
			} while (resList.size() == ROWS);
			long end = System.currentTimeMillis();
			logger.info("end create increment index, index total num:"
					+ incremTotal
					+ ",elasped time "
					+ ((end - begin) / 1000)
					+ " seconds");
			lastUpdateTime = maxUpdateTime;

			// 提交索引，刷新搜索器
			commitIndexAndRefreshSearcher();

			// 每日同步索引
			syncIndexByDaily();
		} catch (Exception e) {
			logger.error(e, e);
		}
	}
	
	/**
	 * 每天同步一次索引
	 * 
	 */
	private synchronized void syncIndexByDaily() {
		int currentDay = Calendar.getInstance().get(Calendar.DAY_OF_YEAR);
		if (lastSyncIndexDay != currentDay) {
			syncIndexWithDatabase(); 
			lastSyncIndexDay = currentDay;
		}
	}

	/**
	 * 提交索引,刷新搜索器
	 * 
	 */
	private synchronized void commitIndexAndRefreshSearcher()
			throws IOException {
		// 提交索引
		indexWriter.commit(); // 将新增索引提交到磁盘(即刷新合并到磁盘)

		// 刷新搜索器
		IndexReader oldReader = searcher.getIndexReader();
		IndexReader newReader = DirectoryReader.openIfChanged(
				(DirectoryReader) oldReader, indexWriter, true); // 重启reader
		if (null != newReader && newReader != oldReader) {
			searcher = new IndexSearcher(newReader); // 创建新的搜索器，更新搜索器
			searcher.setSimilarity(mySimilarity);
			try {
				Thread.sleep(5 * 1000); // 有可能还有检索线程在使用老的检索器，因此，等待n秒后，再关闭老的reader!
			} catch (Exception e) {
				logger.error(e, e);
			}
			oldReader.close(); // 关闭老reader
		}

		// 测试搜索
		testSearch();
	}

	/**
	 * 测试搜索
	 * 
	 */
	private void testSearch() {
		try {
			TopDocs topDocs = searcher.search(new MatchAllDocsQuery(), 1);
			logger.info("MatchAllDocsQuery, topDocs.totalHits=" + topDocs.totalHits);
		} catch (Exception e) {
			logger.error(e, e);
		}
	}

	/**
	 * 对资源做处理
	 * 
	 * @param res
	 */
	private void process(Game res) {
		if (res == null) {
			return;
		}

		// 设置是否为下线资源
		if (!(res.getStatus() == 1)) { // 非有效资源，设为下线资源
			res.setOffline(true);
			return;
		}
		
		// 设置联想词相关字段
		TextPinyin textPinyin = PinyinUtil.getTextPinyin(res.getName());
		res.setSuggestionName(textPinyin.getText());
		res.setSuggestionNameFullPinYin(textPinyin.getFullPinyin());
		res.setSuggestionNameAcronymPinYin(textPinyin.getAcronymPinYin());
		
		// 设置更新时间(long型)
		if (res.getOpTime() != null) {
			res.setOpTimeLong(res.getOpTime().getTime());
		}
	}
	
	/**
	 * 设置上次更新时间
	 * 
	 */
	private void setLastUpdateTime() {
		try {
			Sort sort = new Sort(new SortField("opTimeLong", Type.LONG, true));
			TopDocs topDocs = searcher.search(new MatchAllDocsQuery(), 1, sort);
			if (topDocs.totalHits == 0) {
				logger.info("search no result for index path:" + indexPath);
				return;
			}

			Document doc = searcher.doc(topDocs.scoreDocs[0].doc);
			lastUpdateTime = new Date(Long.parseLong(doc.get("opTimeLong")));
			logger.info("lastUpdateTime=" + new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(lastUpdateTime));
		} catch (Exception e) {
			logger.error(e, e);
		}
	}

	/**
	 * 释放相关资源
	 * 
	 */
	public synchronized void destroy() {
		try {
			logger.info("==destroy==");
			if (searcher != null) {
				searcher.getIndexReader().close();// 关闭索引读者
			}
			if (indexWriter != null) {
				indexWriter.close();// 关闭索引写者(可将内存中的缓存索引刷新到磁盘索引中)
			}
		} catch (Exception e) {
			logger.error(e.getMessage(), e);
		}
	}

	/**
	 * 创建进索引
	 * 
	 * @param list
	 * @param append
	 *            (true:添加文档到索引，false:更新文档到索引(根据资源id更新文档,若索引中不存在此id文档,则自动变为新增文档)
	 *            )
	 * @return
	 */
	private synchronized void createIntoIndex(List<Game> list, boolean append) {
		if (null == list || list.isEmpty()) {
			return;
		}

		for (Game res : list) {
			try {
				process(res); // 预处理资源
				if (res.isOffline()) { // 下线资源，从索引中删除
					if (!append) {
						deleteFromIndexById(res);
					}
				} else { // 增、改资源
					Document doc = newDocument(fields, res);
					if (doc != null) {
						createIntoIndex(doc, append);
					}
				}
			} catch (Exception e) {
				logger.error("id=" + res.getId() + "," + e.getMessage(), e);
			}
		}
	}

	/**
	 * 从索引中删除(通过id)
	 * 
	 * @param res
	 */
	private synchronized void deleteFromIndexById(Game res) {
		if (null == res) {
			return;
		}

		try {
			indexWriter.deleteDocuments(new Term("id", res.getId() + ""));
		} catch (Exception e) {
			logger.error(e.getMessage(), e);
		}
	}

	/**
	 * 创建到索引
	 * 
	 * @param doc
	 * @param append
	 *            (true:添加文档到索引，false:更新文档到索引(根据id更新文档,若不存在此id文档,则自动变为新增文档))
	 */
	private synchronized void createIntoIndex(Document doc, boolean append) {
		if (null == doc) {
			return;
		}

		try {
			if (append) {// 添加到索引
				indexWriter.addDocument(doc);
			} else {// 更新到索引
				indexWriter.updateDocument(new Term("id", doc.get("id")), doc);
			}
		} catch (Exception e) {
			logger.error(e.getMessage(), e);
		}
	}

	/**
	 * 从索引中删除
	 * 
	 * @param ids
	 */
	private synchronized void deleteFromIndex(List<Long> ids) {
		if (null == ids || ids.isEmpty()) {
			return;
		}

		try {
			for (long id : ids) {
				indexWriter.deleteDocuments(new Term("id", id + ""));
			}
		} catch (Exception e) {
			logger.error(e.getMessage(), e);
		}
	}
	
	/**
	 * 与数据库同步索引
	 * 
	 */
	private synchronized void syncIndexWithDatabase() {
		try {
			logger.info("sync index with database");

			if (lastUpdateTime == null) {
				logger.info("lastUpdateTime is null");
				return;
			}

			int dbValidResNum = gameDao.findValidResNum(lastUpdateTime); // 数据库中有效资源数
			Query matchAllDocsQuery = new MatchAllDocsQuery();
			TopDocs topDocs = searcher.search(matchAllDocsQuery, 1); // 索引中资源数
			if (topDocs.totalHits == dbValidResNum) {
				logger.info("index same with database,database num="
						+ dbValidResNum + ",index num=" + topDocs.totalHits);
			} else {
				logger.info("index no same with database,database num="
						+ dbValidResNum + ",index num=" + topDocs.totalHits);

				// 得到数据库和索引资源id集合
				Set<Long> dbIds = getValidIdsInDb();
				Set<Long> indexIds = getIndexIds(matchAllDocsQuery, topDocs.totalHits);

				// 补建漏建的资源索引
				createLeakResIndex(indexIds, dbIds);

				// 删除下线资源(从索引中)
				deleteOfflineResFromIndex(indexIds, dbIds);

				// 提交索引，刷新搜索器
				commitIndexAndRefreshSearcher();
			}
		} catch (Exception e) {
			logger.error(e, e);
		}
	}
	
	/**
	 * 补建漏建的资源索引
	 * 
	 * @param indexIds
	 * @param dbIds
	 */
	private void createLeakResIndex(Set<Long> indexIds, Set<Long> dbIds) {
		List<Game> leakResList = new ArrayList<Game>();
		for (Long id : dbIds) {
			if (!indexIds.contains(id)) {
				Game resource = gameDao.findById(id);
				if (resource != null) {
					leakResList.add(resource);
				}
				logger.debug("to add id=" + id);
			}
		}
		if (leakResList.size() > 0) {
			createIntoIndex(leakResList, true);
			logger.info("to add res num=" + leakResList.size());
		}
	}
	
	/**
	 * 删除下线资源(从索引中)
	 * 
	 * @param indexIds
	 * @param dbIds
	 */
	private void deleteOfflineResFromIndex(Set<Long> indexIds, Set<Long> dbIds) {
		List<Long> delResList = new ArrayList<Long>();
		for (Long id : indexIds) {
			if (!dbIds.contains(id)) {
				delResList.add(id);
				logger.debug("to delete id=" + id);
			}
		}
		if (delResList.size() > 0) {
			deleteFromIndex(delResList);
			logger.info("to delete res num=" + delResList.size());
		}
	}
	
	/**
	 * 得到数据库中有效资源id集合
	 * 
	 * @return
	 */
	private Set<Long> getValidIdsInDb() {
		long begin = System.currentTimeMillis();
		Set<Long> dbIds = gameDao.findValidResIds(lastUpdateTime);
		long end = System.currentTimeMillis();
		logger.info("get database ids size=" + dbIds.size()
				+ ",elapsed time " + (end - begin) + " ms");
		return dbIds;
	}
	
	/**
	 * 得到索引中资源id集合
	 * 
	 * @param matchAllDocsQuery
	 * @param totalHits
	 * @return
	 * @throws IOException
	 */
	private Set<Long> getIndexIds(Query matchAllDocsQuery, int totalHits) throws IOException {
		long begin = System.currentTimeMillis();
		Set<Long> indexIds = new HashSet<Long>();
		AtomicReader atomicReader = SlowCompositeReaderWrapper.wrap(searcher.getIndexReader()); // 包装成AtomicReader
		BinaryDocValues idValues = FieldCache.DEFAULT.getTerms(atomicReader, "id", false);
		TopDocs topDocs = searcher.search(matchAllDocsQuery, totalHits);
		ScoreDoc[] scoreDocs = topDocs.scoreDocs;
		for (int i = 0; i < scoreDocs.length; i++) {
			String id = SearchUtil.getFiledCacheValue(idValues, scoreDocs[i].doc);
			indexIds.add(Long.parseLong(id));
		}
		long end = System.currentTimeMillis();
		logger.info("get index ids size=" + indexIds.size()
				+ ",elapsed time " + (end - begin) + " ms");
		return indexIds;
	}
	
	/**
	 * 通过id删除索引，多个用英文逗号分隔
	 * 
	 * @param ids
	 */
	public void deleteIndex(final String ids) {
		if (ids != null && !ids.isEmpty()) {
			// 启动一个线程去执行，以免在此阻塞
			new Thread() {
				public void run() {
					try {
						logger.info("start delete ids=" + ids);
						
						String[] idArr = ids.split(",");
						List<Long> idList = new ArrayList<Long>();
						for (String id : idArr) {
							id = id.trim();
							idList.add(Long.parseLong(id));
						}
						deleteFromIndex(idList);
						
						commitIndexAndRefreshSearcher();
						
						logger.info("end delete ids=" + ids);
					} catch (Exception e) {
						logger.error(e, e);
					}
				}
			}.start();
		}
	}

	/**
	 * 同步索引(与数据库)
	 * 
	 */
	public void syncIndex() {
		if (!humanSyncingIndex) {
			humanSyncingIndex = true; // 同步中
			// 启动一个线程执行索引同步，以免在此阻塞
			new Thread() {
				public void run() {
					logger.info("begin human syncing index");
					syncIndexWithDatabase();
					humanSyncingIndex = false; // 同步完成
					logger.info("end human syncing index");
				}
			}.start();
		} else {
			logger.info(indexPath + " already human syncing index!");
		}
	}
	
	/**
	 * 重建索引
	 * 
	 */
	public void recreateIndex() {
		if (!humanRecreatingIndex) {
			humanRecreatingIndex = true; // 重建中
			// 启动一个线程执行索引重建，以免在此阻塞
			new Thread() {
				public void run() {
					logger.info("begin human recreating index");
					createFullIndex();
					humanRecreatingIndex = false; // 重建完成
					logger.info("end human recreating index");
				}
			}.start();
		} else {
			logger.info(indexPath + " already human recreating index!");
		}
	}

	public String getIndexPath() {
		return indexPath;
	}

	public void setIndexPath(String indexPath) {
		this.indexPath = indexPath;
	}

	public GameDao getGameDao() {
		return gameDao;
	}

	public void setGameDao(GameDao gameDao) {
		this.gameDao = gameDao;
	}

	public IndexSearcher getSearcher() {
		return searcher;
	}
}
