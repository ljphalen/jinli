package com.gionee.game.search.test;

import java.io.File;
import java.io.IOException;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.DoubleField;
import org.apache.lucene.document.Field;
import org.apache.lucene.document.FieldType;
import org.apache.lucene.document.FloatField;
import org.apache.lucene.document.IntField;
import org.apache.lucene.document.LongField;
import org.apache.lucene.index.DirectoryReader;
import org.apache.lucene.index.FieldInfo.IndexOptions;
import org.apache.lucene.index.FieldInvertState;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.IndexWriterConfig;
import org.apache.lucene.index.IndexWriterConfig.OpenMode;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.DisjunctionMaxQuery;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.search.similarities.DefaultSimilarity;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;
import org.wltea.analyzer.lucene.IKAnalyzer;

import com.gionee.game.search.server.util.Constants;
import com.gionee.game.search.server.util.QueryUtil;

public class SearchTest {
	public static final FieldType STORE_YES_ANALYZED = new FieldType();
	public static final FieldType STORE_YES_ANALYZED_NO = new FieldType();
	public static final FieldType STORE_YES_INDEX_NO = new FieldType();
	public static final FieldType STORE_NO_ANALYZED = new FieldType();
	public static final FieldType STORE_NO_ANALYZED_NO = new FieldType();
	public static final FieldType STORE_YES_INT = IntField.TYPE_STORED;
	public static final FieldType STORE_YES_LONG = LongField.TYPE_STORED;
	public static final FieldType STORE_YES_FLOAT = FloatField.TYPE_STORED;
	public static final FieldType STORE_YES_DOUBLE = DoubleField.TYPE_STORED;
	
	private static final MySimilarity mySimilarity = new MySimilarity();
	private static final String indexPath = "f:\\test\\index";
	private static final IKAnalyzer iKAnalyzer = new IKAnalyzer();
	static {
		STORE_YES_ANALYZED.setStored(true);
		STORE_YES_ANALYZED.setIndexed(true);
		STORE_YES_ANALYZED.setTokenized(true);
		STORE_YES_ANALYZED.setOmitNorms(false);
		STORE_YES_ANALYZED
				.setIndexOptions(IndexOptions.DOCS_AND_FREQS_AND_POSITIONS);
		STORE_YES_ANALYZED.freeze();

		STORE_YES_ANALYZED_NO.setStored(true);
		STORE_YES_ANALYZED_NO.setIndexed(true);
		STORE_YES_ANALYZED_NO.setTokenized(false);
		STORE_YES_ANALYZED_NO.setOmitNorms(false);
		STORE_YES_ANALYZED_NO
				.setIndexOptions(IndexOptions.DOCS_AND_FREQS_AND_POSITIONS);
		STORE_YES_ANALYZED_NO.freeze();

		STORE_YES_INDEX_NO.setStored(true);
		STORE_YES_INDEX_NO.setIndexed(false);
		STORE_YES_INDEX_NO.setTokenized(false);
		STORE_YES_INDEX_NO.setOmitNorms(true);
		STORE_YES_INDEX_NO.setIndexOptions(null);
		STORE_YES_INDEX_NO.freeze();

		STORE_NO_ANALYZED.setStored(false);
		STORE_NO_ANALYZED.setIndexed(true);
		STORE_NO_ANALYZED.setTokenized(true);
		STORE_NO_ANALYZED.setOmitNorms(false);
		STORE_NO_ANALYZED
				.setIndexOptions(IndexOptions.DOCS_AND_FREQS_AND_POSITIONS);
		STORE_NO_ANALYZED.freeze();

		STORE_NO_ANALYZED_NO.setStored(false);
		STORE_NO_ANALYZED_NO.setIndexed(true);
		STORE_NO_ANALYZED_NO.setTokenized(false);
		STORE_NO_ANALYZED_NO.setOmitNorms(false);
		STORE_NO_ANALYZED_NO
				.setIndexOptions(IndexOptions.DOCS_AND_FREQS_AND_POSITIONS);
		STORE_NO_ANALYZED_NO.freeze();
	}
	
	private static class MySimilarity extends DefaultSimilarity {
		@Override
		public float tf(float freq) {
//			if (freq >= 3.0f) {
//				return 2.0f;
//			} else {
//				return 1.0f;
//			}
			return 1.0f;
		}
		
		@Override
		public float idf(long docFreq, long numDocs) {
			return 1.0f;
		}
		
		@Override
		public float lengthNorm(FieldInvertState state) {
			return 1.0f;
		}
	}

	public static IndexWriter getIndexWriter(String indexPath) {
		try {
			Directory dir = FSDirectory.open(new File(indexPath));
			IndexWriterConfig iwc = new IndexWriterConfig(Constants.LUCENE_CURRENT_VERSION,
					iKAnalyzer);
			iwc.setOpenMode(OpenMode.CREATE);
			iwc.setSimilarity(mySimilarity); // 设置相似器
			return new IndexWriter(dir, iwc);
		} catch (IOException e) {
			e.printStackTrace();
			return null;
		}
	}

	public static IndexSearcher getIndexSearcher(String indexPath) {
		try {
			Directory dir = FSDirectory.open(new File(indexPath));
			IndexReader reader = DirectoryReader.open(dir);
			IndexSearcher indexSearcher = new IndexSearcher(reader);
			indexSearcher.setSimilarity(mySimilarity);
			return indexSearcher;
		} catch (IOException e) {
			e.printStackTrace();
			return null;
		}
	}
	
//	public static void createIndex() {
//		try {
//			IndexWriter indexWriter = getIndexWriter(indexPath);
//
//			Document doc = new Document();
//			Field field = new Field("title", "半导体工艺基础 ", STORE_YES_ANALYZED);
////			field.setBoost(20.0f);
//			doc.add(field);
//			field = new Field("content", "半导体工艺基础 ", STORE_YES_ANALYZED);
//			doc.add(field);
//			indexWriter.addDocument(doc);
//
//			doc = new Document();
//			field = new Field("title", "中微半导体首次进入半导体照明半导体照明半导体", STORE_YES_ANALYZED);
////			field.setBoost(20.0f);
//			doc.add(field);
//			field = new Field("content", "中微半导体首次进入半导体照明半导体照明半导体", STORE_YES_ANALYZED);
//			doc.add(field);
//			indexWriter.addDocument(doc);
//			
////			doc = new Document();
////			doc.add(new Field("title", "深度观察：日本半导体牵动全球半导体产业", STORE_YES_ANALYZED));
////			indexWriter.addDocument(doc);
////			
////			doc = new Document();
////			doc.add(new Field("title", "半导体激光美牙", STORE_YES_ANALYZED));
////			indexWriter.addDocument(doc);
////			
////			doc = new Document();
////			doc.add(new Field("title", "功率半导体的春天", STORE_YES_ANALYZED));
////			indexWriter.addDocument(doc);
//
//			indexWriter.commit();
//			indexWriter.close();
//		} catch (Exception e) {
//			e.printStackTrace();
//		}
//	}
	
//	public static void createIndex() {
//		try {
//			IndexWriter indexWriter = getIndexWriter(indexPath);
//
//			Document doc = new Document();
//			Field field = new Field("title", "文档1", STORE_YES_ANALYZED_NO);
//			doc.add(field);
//			field = new Field("content", "半导体工艺基础 ", STORE_YES_ANALYZED);
//			doc.add(field);
//			field = new Field("  `~!@#$a%^&*()-_  b+=|\\{[12]}:;'\"<,>./?    ", "led", STORE_YES_ANALYZED);
//			doc.add(field);
//			field = new Field("An innovative High Speed, Real Time, Low Cost, Precision gas sensor using Al-In-Sb (Aluminium Indium Antimonde) NDIR LED technology. The base Mid IR solid state technology, developed by QinetiQ and licensed on a sole basis has been incorporated into an advanced CO2 sensor that is suitable for high volume manufacture and is targeted at a number of Carbon Dioxide Sensor applications.This Carbon Dioxide sensor is a fast, low power NDIR sensorAn innovative High Speed, Real Time, Low Cost, Precision gas sensor using Al-In-Sb (Aluminium Indium Antimonde) NDIR LED technology. The base Mid IR solid state technology, developed by QinetiQ and licensed on a sole basis has been incorporated into an advanced CO2 sensor that is suitable for high volume manufacture and is targeted at a number of Carbon Dioxide Sensor applications.This Carbon Dioxide sensor is a fast, low power NDIR sensorAn innovative High Speed, Real Time, Low Cost, Precision gas sensor using Al-In-Sb (Aluminium Indium Antimonde) NDIR LED technology. The base Mid IR solid state technology, developed by QinetiQ and licensed on a sole basis has been incorporated into an advanced CO2 sensor that is suitable for high volume manufacture and is targeted at a number of Carbon Dioxide Sensor applications.This Carbon Dioxide sensor is a fast, low power NDIR sensor", "led", STORE_YES_ANALYZED);
//			doc.add(field);
////			field = new Field("aaa", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("aaa1", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("aaa2", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("aaa3", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("aaa4", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
//			indexWriter.addDocument(doc);
//
////			doc = new Document();
////			field = new Field("title", "文档2", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("content", "中微半导体首次进入半导体照明半导体照明半导体", STORE_NO_ANALYZED);
////			doc.add(field);
////			field = new Field("bbb", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("xxx", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("yyy", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			indexWriter.addDocument(doc);
//			
////			doc = new Document();
////			field = new Field("title", "文档3", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("content", "中微半导体首次进入半导体照明半导体照明半导体", STORE_YES_ANALYZED);
////			doc.add(field);
////			field = new Field("ccc", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("ccc1", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("ccc2", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			field = new Field("ccc3", "led照明", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			indexWriter.addDocument(doc);
////			
////			doc = new Document();
////			field = new Field("title", "文档4", STORE_YES_ANALYZED_NO);
////			doc.add(field);
////			indexWriter.addDocument(doc);
//
//			indexWriter.commit();
//			indexWriter.close();
//		} catch (Exception e) {
//			e.printStackTrace();
//		}
//	}
	
//	public static void createIndex() {
//		try {
//			IndexWriter indexWriter = getIndexWriter(indexPath);
//
//			Document doc = null;
//			
//			doc = new Document();
//			doc.add(new IntField("id", 4, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123456", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			doc = new Document();
//			doc.add(new IntField("id", 3, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123456", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			doc = new Document();
//			doc.add(new IntField("id", 1, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123456", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			doc = new Document();
//			doc.add(new IntField("id", 100, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123454", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			doc = new Document();
//			doc.add(new IntField("id", 88, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123451", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			doc = new Document();
//			doc.add(new IntField("id", 99, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123455", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			
//			doc = new Document();
//			doc.add(new IntField("id", 6, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123456", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			doc = new Document();
//			doc.add(new IntField("id", 7, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123456", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			doc = new Document();
//			doc.add(new IntField("id", 2, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123456", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			doc = new Document();
//			doc.add(new IntField("id", 5, STORE_YES_INT));
//			doc.add(new Field("duplicate", "123456", STORE_YES_ANALYZED_NO));
//			doc.add(new Field("string", "haha", STORE_YES_ANALYZED_NO));
//			indexWriter.addDocument(doc);
//			
//			indexWriter.commit();
//			indexWriter.close();
//		} catch (Exception e) {
//			e.printStackTrace();
//		}
//	}
	
	public static void createIndex() {
		try {
			IndexWriter indexWriter = getIndexWriter(indexPath);

			Document doc = null;
			
			doc = new Document();
			doc.add(new IntField("id", 1, STORE_YES_INT));
			doc.add(new Field("title", "产品开发工程师", STORE_YES_ANALYZED));
			doc.add(new Field("content", "深圳市qq公司招聘", STORE_YES_ANALYZED));
			indexWriter.addDocument(doc);
			
			doc = new Document();
			doc.add(new IntField("id", 2, STORE_YES_INT));
			doc.add(new Field("title", "led测试工程师", STORE_YES_ANALYZED));
			doc.add(new Field("content", "深圳市qq公司招聘软件开发工程师", STORE_YES_ANALYZED));
			indexWriter.addDocument(doc);
			
//			doc = new Document();
//			doc.add(new IntField("id", 3, STORE_YES_INT));
//			doc.add(new Field("title", "产品品质工程师", STORE_YES_ANALYZED));
//			doc.add(new Field("content", "深圳市qq公司招聘软件开发工程师", STORE_YES_ANALYZED));
//			indexWriter.addDocument(doc);
			
			indexWriter.commit();
			indexWriter.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	public static void main(String[] args) { // 对搜索结果按单个字段去重
		try {
			createIndex();
			
			IndexSearcher indexSearcher = getIndexSearcher(indexPath);
			
			String keyword = "软件开发";
			DisjunctionMaxQuery maxQuery = new DisjunctionMaxQuery(0.0f);
			
			Query query = QueryUtil.getPartHitQuery("title", keyword, iKAnalyzer);
//			Query query = QueryUtil.getPhraseQuery("title", keyword, iKAnalyzer);
//			query.setBoost(100.0f);
			maxQuery.add(query);
			
			query = QueryUtil.getPartHitQuery("content", keyword, iKAnalyzer);
//			query = QueryUtil.getPhraseQuery("content", keyword, iKAnalyzer);
//			query.setBoost(0.01f);
			maxQuery.add(query);
			
			System.out.println("maxQuery==" + maxQuery);
			
			TopDocs topDocs = indexSearcher.search(maxQuery, 200);
			ScoreDoc[] scoreDocs = topDocs.scoreDocs;
			for (int i = 0; i < scoreDocs.length; i++) {
				Document doc = indexSearcher.doc(scoreDocs[i].doc);
				System.out.println("id=" + doc.get("title") + "====" + scoreDocs[i].toString());
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
//	public static void main3(String[] args) { // 对搜索结果按单个字段去重
//		try {
//			createIndex();
//			
//			IndexSearcher indexSearcher = getIndexSearcher(indexPath);
//			
//			Query query = new TermQuery(new Term("string", "haha"));
////			DuplicateFilter filter = new DuplicateFilter("duplicate");
//			
//			KeepMode keepMode = KeepMode.KM_USE_LAST_OCCURRENCE;
//			ProcessingMode processingMode = ProcessingMode.PM_FULL_VALIDATION;
//			DuplicateFilter filter = new DuplicateFilter("duplicate", keepMode, processingMode);
//			
////			TopDocs topDocs = indexSearcher.search(query, filter, 200);
//			Sort sort = new Sort(new SortField[] {
//					new SortField("id", SortField.Type.INT, false),
//					SortField.FIELD_SCORE });
//			TopDocs topDocs = indexSearcher.search(query, filter, 200, sort);
//			ScoreDoc[] scoreDocs = topDocs.scoreDocs;
//			System.out.println("topDocs.totalHits=" + topDocs.totalHits);
//			for (int i = 0; i < scoreDocs.length; i++) {
//				Document doc = indexSearcher.doc(scoreDocs[i].doc);
//				System.out.println(doc.toString());
//			}
//		} catch (Exception e) {
//			e.printStackTrace();
//		}
//	}
	
	public static void main2(String[] args) {
		try {
			createIndex();
			IndexSearcher indexSearcher = getIndexSearcher(indexPath);
			
			String keyword = "led";
			TermQuery query = new TermQuery(new Term("An innovative High Speed, Real Time, Low Cost, Precision gas sensor using Al-In-Sb (Aluminium Indium Antimonde) NDIR LED technology. The base Mid IR solid state technology, developed by QinetiQ and licensed on a sole basis has been incorporated into an advanced CO2 sensor that is suitable for high volume manufacture and is targeted at a number of Carbon Dioxide Sensor applications.This Carbon Dioxide sensor is a fast, low power NDIR sensorAn innovative High Speed, Real Time, Low Cost, Precision gas sensor using Al-In-Sb (Aluminium Indium Antimonde) NDIR LED technology. The base Mid IR solid state technology, developed by QinetiQ and licensed on a sole basis has been incorporated into an advanced CO2 sensor that is suitable for high volume manufacture and is targeted at a number of Carbon Dioxide Sensor applications.This Carbon Dioxide sensor is a fast, low power NDIR sensorAn innovative High Speed, Real Time, Low Cost, Precision gas sensor using Al-In-Sb (Aluminium Indium Antimonde) NDIR LED technology. The base Mid IR solid state technology, developed by QinetiQ and licensed on a sole basis has been incorporated into an advanced CO2 sensor that is suitable for high volume manufacture and is targeted at a number of Carbon Dioxide Sensor applications.This Carbon Dioxide sensor is a fast, low power NDIR sensor", keyword));
			
			TopDocs topDocs = indexSearcher.search(query, 10);
			ScoreDoc[] scoreDocs = topDocs.scoreDocs;
			for (int i = 0; i < scoreDocs.length; i++) {
				Document doc = indexSearcher.doc(scoreDocs[i].doc);
				System.out.println(doc.get("title") + "====>" + scoreDocs[i].toString());
//				System.out.println(doc.toString());
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	public static void main1(String[] args) {
		try {
			createIndex();
			IndexSearcher indexSearcher = getIndexSearcher(indexPath);
			
			String keyword = "半导体";
			
			// 最大得分查询
			DisjunctionMaxQuery maxQuery = new DisjunctionMaxQuery(0.0f);
			
			// 全部命中查询
			Query fullHitQuery = QueryUtil.getFullHitQuery("title", keyword,
					iKAnalyzer);
			fullHitQuery.setBoost(20.0f);
//			maxQuery.add(fullHitQuery);
//			System.out.println(fullHitQuery);
			
			fullHitQuery = QueryUtil.getFullHitQuery("content", keyword,
					iKAnalyzer);
			maxQuery.add(fullHitQuery);
			
			System.out.println(maxQuery);
			
			TopDocs topDocs = indexSearcher.search(maxQuery, 10);
			ScoreDoc[] scoreDocs = topDocs.scoreDocs;
			for (int i = 0; i < scoreDocs.length; i++) {
				Document doc = indexSearcher.doc(scoreDocs[i].doc);
				System.out.println(doc.get("title") + "====>" + scoreDocs[i].toString());
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
}
