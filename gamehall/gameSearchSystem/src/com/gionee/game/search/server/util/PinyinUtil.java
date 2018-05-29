package com.gionee.game.search.server.util;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.HashMap;
import java.util.Map;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

import com.gionee.game.search.server.model.TextPinyin;

public class PinyinUtil {
	private static final Log logger = LogFactory.getLog(PinyinUtil.class);
	
	private static Map<Character, String[]> pinyinTable = loadPinyinTable(); // 汉字拼音对照表
	
	/**
	 * 得到汉字拼音
	 * 
	 * @param c
	 * @return
	 */
	public static String[] getPinyin(char c) {
		return pinyinTable.get(c);
	}
	
	/**
	 * 得到文本拼音
	 * 去除文本中的符号且转换成小写字母，再转换成相应的拼音
	 * 例如：输入：热血冰球（Ice Rage_Free）4.5.6
	 *     输出：TextPinyin[text=热血冰球iceragefree456,fullPinyin=rexuebingqiuiceragefree456,acronymPinYin=rxbqiceragefree456]
	 * 
	 * @param text
	 * @return 
	 */
	public static TextPinyin getTextPinyin(String text) {
		TextPinyin textPinyin = new TextPinyin();
		textPinyin.setText(text);
		
		if (null == text || text.trim().isEmpty()) {
			return textPinyin;
		}
		
		// 去除文本中的符号且转换成小写字母，得到新文本
		String newText = StringUtil.removeSymbolAndToLowerCase(text);
		
		// 生成文本的拼音
		String simpleText = SearchUtil.traditionalToSimple(newText); // 繁体转简体
		StringBuilder fullPinyin = new StringBuilder();
		StringBuilder acronymPinYin = new StringBuilder();
		try {
			for (int i = 0; i < simpleText.length(); i++) {
				char c = simpleText.charAt(i);
				String[] pys = pinyinTable.get(c);
				if (null != pys) { // 汉字
					String py = pys[0]; // 取第一个拼音
					fullPinyin.append(py);
					acronymPinYin.append(py.charAt(0)); // 首字母
				} else { // 非汉字
					fullPinyin.append(c);
					acronymPinYin.append(c);
				}
			}
		} catch (Exception e) {
			logger.error(e, e);
		}
		
		// 设置值
		textPinyin.setText(newText);
		textPinyin.setFullPinyin(fullPinyin.toString());
		textPinyin.setAcronymPinYin(acronymPinYin.toString());
		
		return textPinyin;
	}
	
	/**
	 * 装载汉字拼音对照表
	 * 
	 */
	private static Map<Character, String[]> loadPinyinTable() {
		try {
			Map<Character, String[]> pyTable = new HashMap<Character, String[]>();
			InputStream is = Constants.class.getResourceAsStream("/pinyin.txt");
			BufferedReader reader = new BufferedReader(new InputStreamReader(
					is, "UTF-8"));
			String line = reader.readLine();
			while (line != null) {
				String[] arr = line.trim().split("\\s+");
				if (arr.length > 1) {
					String[] pinyins = new String[arr.length - 1];
					for (int i = 1; i < arr.length; i++) {
						pinyins[i - 1] = arr[i].toLowerCase();
					}
					pyTable.put(arr[0].charAt(0), pinyins);
				} else {
					logger.error("error line：" + line);
				}
				line = reader.readLine(); // 读取下一行
			}
			reader.close();
			logger.info("pinyin.txt load complete, chinese number:" + pyTable.size());
			
			return pyTable;
		} catch (IOException e) {
			logger.error("pinyin.txt not found", e);
		}
		
		return null;
	}
}
