package com.gionee.game.search.server.util;

import junit.framework.TestCase;

import com.gionee.game.search.server.model.TextPinyin;

public class PinyinUtilTest extends TestCase {
	@Override
	public void setUp() throws Exception {
		super.setUp();
	}
	
	public void testGetTextPinyin() throws Exception {
		String text = "热血冰球（Ice Rage_Free）4.5.6";
		text = "龍飞凤舞"; // 含繁体字测试
		text = "Real,Drift3 Free4.5。678";
		text = "how are you";
		
		TextPinyin textPinyin = PinyinUtil.getTextPinyin(text);
		System.out.println(text);
		System.out.println(textPinyin);
	}
	
	
	public void testGetPinyin() throws Exception {
		char c = '好';
		c = '乐';
		c = 'a';
		c = ' ';
		c = ',';
		String[] pinyins = PinyinUtil.getPinyin(c);
		if (pinyins != null) {
			for (String  pinyin : pinyins) {
				System.out.println(pinyin);
			}
		}
		else {
			System.out.println("pinyins=" + pinyins);
		}
	}
}
