package com.gionee.game.search.test;

import java.io.StringReader;

import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.tokenattributes.CharTermAttribute;

import com.gionee.game.search.server.analysis.PatternAnalyzer;

public class PatternAnalyzerTestMain {
	public static void main(String[] args) throws Exception {
		PatternAnalyzer analyzer = new PatternAnalyzer("#-fd-#");// 空字符串代表单字符切分
		TokenStream ts = analyzer.tokenStream("field", new StringReader("aaa#-fd-#BBb#-fd-#你好"));
		CharTermAttribute term = ts.addAttribute(CharTermAttribute.class);
		ts.reset();
		System.out.println("====================");
		while (ts.incrementToken()) {
			System.out.println(term.toString());
		}
		System.out.println("====================");
		ts.end();
		ts.close();
		analyzer.close();
	}
}
