package com.gionee.game.search.server.util;

import junit.framework.TestCase;

public class StringUtilTest extends TestCase {
	@Override
	public void setUp() throws Exception {
		super.setUp();
	}
	
	public void testRemoveSymbolAndToLowerCase() throws Exception {
		String text = "热血冰球（Ice Rage_Free）";
		text = "Real,Drift3 Free4.5.&~^~!%&*()_+。6{];;:\">?78";
		text = "how are you";
		text = "abc@163.com,xyz@qq.com";
		
		String newText = StringUtil.removeSymbolAndToLowerCase(text);
		System.out.println(text);
		System.out.println(newText);
	}
	
	public void testIsAlphaNumeric() throws Exception {
		char c = 'a';
		c = 'k';
		c = 'z';
		c = 'A';
		c = 'K';
		c = 'Z';
		c = '0';
		c = '5';
		c = '9';
		c = '好';
		c = '[';
		
		boolean flag = StringUtil.isAlphaNumeric(c);
		System.out.println(c);
		System.out.println(flag);
	}
	
	public void testIsAlphaNumericString() {
		String str = "akzAKZ059";
		str = "how are you 059";
		str = "你好，兄弟";
		
		boolean flag = StringUtil.isAlphaNumericString(str);
		System.out.println(str);
		System.out.println(flag);
	}
}
