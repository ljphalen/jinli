package com.gionee.game.search.server.analysis;

import java.io.Reader;
import java.util.regex.Pattern;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.core.LowerCaseFilter;
import org.apache.lucene.analysis.pattern.PatternTokenizer;

/**
 *正则分词器：使用正则表达式作为分隔符的分词器，功能等同于String.split(String regex)
 *
 */
public class PatternAnalyzer extends Analyzer {
	private Pattern pattern;
	private boolean toLowerCase;

	public PatternAnalyzer(String regex) {
		this(regex, true);
	}
	
	/**
	 * @param regex
	 * @param toLowerCase(是否将分词后的词条转换成小写)
	 */
	public PatternAnalyzer(String regex, boolean toLowerCase) {
		this.pattern = Pattern.compile(regex);
		this.toLowerCase = toLowerCase;
	}
	
	public PatternAnalyzer(Pattern pattern) {
		this(pattern, true);
	}
	
	/**
	 * @param pattern
	 * @param toLowerCase(是否将分词后的词条转换成小写)
	 */
	public PatternAnalyzer(Pattern pattern, boolean toLowerCase) {
		this.pattern = pattern;
		this.toLowerCase = toLowerCase;
	}

	@Override
	protected TokenStreamComponents createComponents(String arg0, Reader arg1) {
		PatternTokenizer tokenizer = new PatternTokenizer(arg1,	pattern, -1);
		TokenStream result = toLowerCase ? new LowerCaseFilter(tokenizer) : tokenizer;
		return new TokenStreamComponents(tokenizer, result);
	}
}
