package com.gionee.game.search.test;

import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.List;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.analysis.tokenattributes.CharTermAttribute;
import org.wltea.analyzer.lucene.IKAnalyzer;


public class TestAnalyzerMain {
	public static void main(String[] args) {
		String keywords = "天通控股";
		keywords = "手机qq2012";
		keywords = "+(asp 销售工程师 +研发工程师";
		keywords = "半导体";
		keywords = "K9K8G08U0D-SCB";
		keywords = "PM2.5";
		keywords = "环保部首次回应PM2.5监测 ";
		keywords = "酷爱Kugou_SCB";
		keywords = "18L/209L";
//		keywords = "8-1020";
//		keywords = "P10";
//		keywords = "www.86031111.com";
//		keywords = "www.baidu.com";
//		keywords = "www.hao123.com";
		keywords = "40/33";
		keywords = "桶18L/208L";
		keywords = "GTX-72型";
		keywords = "UL224";
		keywords = "HD-700\\800\\900";
		keywords = "133*2450";
		keywords = "40cmX30cm";
		keywords = "14.8V/4000mAh";
		keywords = "深圳万润科技股份有限公司脱硝催化剂par灯DILAS鞍山激光百超半导体激光器菲涅尔透镜冷阴极荧光灯";
		keywords = "软件开发工程师";
		
		//ik分词器测试
		testIKAnalyzer(keywords);
		
		System.out.println("=================================");
		
//		//标准分词器测试
//		testStandAnalyzer(keywords);
		
//		BooleanQuery q = new BooleanQuery();
//		q.add(new TermQuery(new Term("a","b")), BooleanClause.Occur.SHOULD);
//		q.add(new TermQuery(new Term("a","b")), BooleanClause.Occur.SHOULD);
//		q.add(new TermQuery(new Term("a","b")), BooleanClause.Occur.MUST);
//		q.add(new TermQuery(new Term("a","b")), BooleanClause.Occur.MUST_NOT);
//		System.out.println(q.clauses().size());
//		System.out.println(q.getClauses().length);
	}
	
	/**
	 * IK分词器测试
	 * @param keywords
	 */
	public static void testIKAnalyzer(String keywords) {
		IKAnalyzer ikAnalyzer = new IKAnalyzer();
		List<String> tokenList = getTokenList(keywords, ikAnalyzer);
		System.out.println(keywords + "---IKAnalyzer" + "---tokenList.size()=" + tokenList.size());
		for (String token : tokenList) {
			System.out.println(token);
		}
	}
	
	/**
	 * IK分词器测试
	 * @param keywords
	 */
	public static void testStandAnalyzer(String keywords) {
		Analyzer analyzer = new StandardAnalyzer();
		
		List<String> tokenList = getTokenList(keywords, analyzer);
		System.out.println(keywords + "---StandardAnalyzer" + "---tokenList.size()=" + tokenList.size());
		for (String token : tokenList) {
			System.out.println(token);
		}
	}

	/**
	 * 返回分词后的词列表
	 * 
	 * @param text
	 * @param analyzer
	 * @return 失败：返回空List
	 */
	public static List<String> getTokenList(String text, Analyzer analyzer) {
		if (null == text || text.trim().isEmpty() || null == analyzer) {
			return new ArrayList<String>();
		}

		List<String> list = new ArrayList<String>();
		
	    TokenStream ts = null;//获取Lucene的TokenStream对象
		try {
			ts = analyzer.tokenStream(null, new StringReader(text));
		    CharTermAttribute term = ts.addAttribute(CharTermAttribute.class);//获取词元文本属性
			ts.reset(); //重置TokenStream（重置StringReader）
			while (ts.incrementToken()) {//迭代获取分词结果
			  list.add(term.toString());
			}
			ts.end(); // Perform end-of-stream operations, e.g. set the final offset.
		} catch (IOException e) {
			e.printStackTrace();
		} finally {
			if(ts != null){//释放TokenStream的所有资源
		      try {
				ts.close();
		      } catch (IOException e) {
				e.printStackTrace();
		      }
			}
	    }
		
		return list;
	}
}
