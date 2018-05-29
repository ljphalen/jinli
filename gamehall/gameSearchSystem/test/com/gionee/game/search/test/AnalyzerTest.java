package com.gionee.game.search.test;

import java.util.List;

import junit.framework.TestCase;

import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.analysis.util.CharArraySet;

import com.gionee.game.search.server.util.SearchUtil;

public class AnalyzerTest extends TestCase {
	@Override
	public void setUp() throws Exception {
		super.setUp();
	}
	
	public void testStandardAnalyzer() throws Exception {
//		StandardAnalyzer standardAnalyzer = new StandardAnalyzer(); // 含英文停止词
		StandardAnalyzer standardAnalyzer = new StandardAnalyzer((CharArraySet) null); // 不含任何停止词
		
		String text = "热血冰球（Ice Rage_Free）";
		text = "龍飞凤舞"; // 含繁体字测试
		text = "Real,Drift3 Free45。678";
		text = "an interesting book";
		text = "how are you";
		text = "斗地主[!\"#$%&`()*+,-./:;<=>?@\\^_'{|}~]";
		
		List<String> wordList = SearchUtil.getTokenList(text, standardAnalyzer); // 分词
		System.out.println(text);
		System.out.println(wordList);
	}
}
