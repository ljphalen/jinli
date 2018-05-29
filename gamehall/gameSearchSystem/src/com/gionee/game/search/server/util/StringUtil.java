package com.gionee.game.search.server.util;

import java.io.UnsupportedEncodingException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import org.apache.commons.codec.digest.DigestUtils;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.analysis.util.CharArraySet;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class StringUtil {
	private static final Logger log = LoggerFactory.getLogger(StringUtil.class);
	
	private static StandardAnalyzer standardAnalyzer = new StandardAnalyzer((CharArraySet) null); // 不含任何停止词
	
	private static String chn = "０１２３４５６７８９ａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺ";
	private static String en = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	private static HashMap<Character, Character> chnToEnMap = null;
	static {
		chnToEnMap = new HashMap<Character, Character>();
		for (int i = 0; i < en.length(); i++) {
			chnToEnMap.put(chn.charAt(i), en.charAt(i));
		}
	}

	/**
	 * 计算字符串md5值
	 * 
	 * @param str
	 * @return md5
	 */
	public static String md5(String str) {
		if (str == null) {
			return null;
		}

		try {
			return DigestUtils.md5Hex(str.getBytes("utf-8")).toLowerCase();
		} catch (UnsupportedEncodingException e) {
			log.error(e.getMessage(), e);
		}

		return null;
	}

	/**
	 * 中文字母数字转换成英文字母数字
	 * 
	 * @param word
	 * @return
	 */
	public static String chnToEn(String word) {
		if (null == word || word.isEmpty()) {
			return word;
		}

		StringBuilder sb = new StringBuilder();
		for (int i = 0; i < word.length(); i++) {
			char c = word.charAt(i);
			if (null != chnToEnMap.get(c)) {
				sb.append(chnToEnMap.get(c));
			} else {
				sb.append(c);
			}
		}
		return sb.toString();
	}

	// ****************************
	// Get minimum of three values
	// ****************************
	private static int Minimum(int a, int b, int c) {
		int mi;

		mi = a;
		if (b < mi) {
			mi = b;
		}
		if (c < mi) {
			mi = c;
		}
		return mi;

	}

	// *****************************
	// Compute Levenshtein distance
	// *****************************
	public static int LD(String s, String t) {
		int d[][]; // matrix
		int n; // length of s
		int m; // length of t
		int i; // iterates through s
		int j; // iterates through t
		char s_i; // ith character of s
		char t_j; // jth character of t
		int cost; // cost

		// Step 1

		n = s.length();
		m = t.length();
		if (n == 0) {
			return m;
		}
		if (m == 0) {
			return n;
		}
		d = new int[n + 1][m + 1];

		// Step 2

		for (i = 0; i <= n; i++) {
			d[i][0] = i;
		}

		for (j = 0; j <= m; j++) {
			d[0][j] = j;
		}

		// Step 3

		for (i = 1; i <= n; i++) {

			s_i = s.charAt(i - 1);

			// Step 4

			for (j = 1; j <= m; j++) {

				t_j = t.charAt(j - 1);

				// Step 5

				if (s_i == t_j) {
					cost = 0;
				} else {
					cost = 1;
				}

				// Step 6

				d[i][j] = Minimum(d[i - 1][j] + 1, d[i][j - 1] + 1,
						d[i - 1][j - 1] + cost);

			}

		}

		// Step 7

		return d[n][m];

	}

	public static int ListLD(List<String> s, List<String> t) {
		int d[][]; // matrix
		int n; // length of s
		int m; // length of t
		int i; // iterates through s
		int j; // iterates through t
		String s_i; // ith character of s
		String t_j; // jth character of t
		int cost; // cost

		// Step 1

		n = s.size();
		m = t.size();
		if (n == 0) {
			return m;
		}
		if (m == 0) {
			return n;
		}
		d = new int[n + 1][m + 1];

		// Step 2

		for (i = 0; i <= n; i++) {
			d[i][0] = i;
		}

		for (j = 0; j <= m; j++) {
			d[0][j] = j;
		}

		// Step 3

		for (i = 1; i <= n; i++) {

			s_i = s.get(i - 1);

			// Step 4

			for (j = 1; j <= m; j++) {

				t_j = t.get(j - 1);

				// Step 5

				if (s_i.equals(t_j)) {
					cost = 0;
				} else {
					cost = 1;
				}

				// Step 6

				d[i][j] = Minimum(d[i - 1][j] + 1, d[i][j - 1] + 1,
						d[i - 1][j - 1] + cost);

			}

		}

		// Step 7

		return d[n][m];

	}

	public static String checkNull(String str) {
		if (null == str) {
			return "";
		}
		return str;
	}

	public static String checkNumber(String str) {
		if (null == str || "".equals(str) || "null".equals(str)) {
			return "";
		} else {
			return str;
		}
	}

	public static String toUppercase(String str) {
		StringBuilder sb = new StringBuilder();
		for (int i = 0; i < str.length(); i++) {
			char c = str.charAt(i);
			if (c >= 'a' && c <= 'z') {
				c -= 32;
			}
			sb.append(c);
		}
		return sb.toString();
	}

	/**
	 * 检测是否为字母
	 * 
	 * @param c
	 * @return 是:true,否:false
	 */
	public static boolean isAlphabetic(char c) {
		return ((c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z'));
	}
	
	/**
	 * 检测是否为字母数字
	 * 
	 * @param c
	 * @return 是:true,否:false
	 */
	public static boolean isAlphaNumeric(char c) {
		return ((c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z') || (c >= '0' && c <= '9'));
	}
	
	/**
	 * 检测是否为字母数字字符串
	 * 
	 * @param str
	 * @return 是:true,否:false
	 */
	public static boolean isAlphaNumericString(String str) {
		if (str == null || str.isEmpty()) {
			return false;
		}

		for (int i = 0; i < str.length(); i++) {
			if (!isAlphaNumeric(str.charAt(i))) {
				return false;
			}
		}

		return true;
	}

	/**
	 * 检测是否为字母字符串
	 * 
	 * @param str
	 * @return 是:true,否:false
	 */
	public static boolean isAlphabeticString(String str) {
		if (str == null || str.isEmpty()) {
			return false;
		}

		for (int i = 0; i < str.length(); i++) {
			if (!isAlphabetic(str.charAt(i))) {
				return false;
			}
		}

		return true;
	}

	/**
	 * 是否为数字
	 * 
	 * @param c
	 * @return 是:true,否:false
	 */
	public static boolean isDigit(char c) {
		return ((c >= '0' && c <= '9'));
	}

	/**
	 * 判断是否是纯英文
	 * 
	 * @param word
	 * @return
	 */
	public static Boolean isEnglish(String word) {
		int charcount = 0;
		int i = 0;
		while (i < word.length()) {
			char c = word.charAt(i);
			if ((c <= 122 && c >= 97) || (c <= 90 && c >= 65)
					|| (c <= 65370 && c >= 65345) || (c <= 65338 && c >= 65313)) { // 中英文a-z
				charcount++;
				i++;
			} else if (c < 128 || (c <= 65305 && c >= 65296) || c == 65285
					|| (c <= 65295 && c >= 65291)) { // 中英文数字和中英文标点符号
				i++;
			} else {
				return false;
			}
		}
		if (charcount == 0) {
			return false;
		}
		return true;
	}

	/**
	 * 不区大小写replace
	 * 
	 * @param source
	 *            源串
	 * @param oldStr
	 *            被替换的子串
	 * @param newStr
	 *            替换的子串
	 * @return 替换之后的源串
	 */
	public static String replaceIgnoreCase(String source, String oldStr,
			String newStr) {
		if (source == null || source.length() == 0 || oldStr == null
				|| oldStr.length() == 0 || newStr == null) {
			return source;
		}

		String sourceLower = source.toLowerCase();
		String oldStrLower = oldStr.toLowerCase();

		if (sourceLower.indexOf(oldStrLower) == -1) {
			return source;
		}

		int oldLen = oldStrLower.length();
		String subStr = null;
		Set<String> occurOldStrSet = new HashSet<String>();
		int i = 0;
		while ((i = sourceLower.indexOf(oldStrLower, i)) != -1) {
			subStr = source.substring(i, i + oldLen);
			if (!occurOldStrSet.contains(subStr)) {
				occurOldStrSet.add(subStr);
			}
			i = i + oldLen;
		}
		for (String old : occurOldStrSet) {
			source = source.replace(old, newStr);
		}

		return source;
	}

	/**
	 * 用英文空格分开字母和数字
	 * 
	 * @param str
	 * @return 分开后的结果
	 */
	public static String breakAlphabetDigit(String str) {
		if (str == null || str.length() <= 1) {
			return str;
		}

		int len = str.length();
		StringBuilder sb = new StringBuilder(len + 5);
		sb.append(str.charAt(0));
		for (int i = 1; i < len; i++) {
			char c = str.charAt(i);
			char lastC = str.charAt(i - 1);
			if ((isAlphabetic(c) && isDigit(lastC)) || isDigit(c)
					&& isAlphabetic(lastC)) {
				sb.append(' ').append(c);
			} else {
				sb.append(c);
			}
		}

		return sb.toString();
	}
	
	/**
	 * 检测是否为全汉字字符串
	 * 
	 * @param str
	 * @return 注：null和"", 返回false
	 */
	public static boolean isChineseStr(String str) {
		if (str == null || str.length() == 0) {
			return false;
		}
		
		//[19968-40869]
		return str.matches("[\\u4E00-\\u9FA5]+");
	}

	/**
	 * 检测是否为汉字字符
	 * 
	 * @param c
	 * @return
	 */
	public static boolean isChineseChar(char c) {
		return c >= 19968 && c <= 40869;
	}
	
	/**
	 * 删除字符串首尾空白符，且把内部多个连续的空白符替换为一个空格，然后返回结果。
	 * 
	 * @param str
	 * @return 
	 */
	public static String normalizeSpace(String str) {
		if (str == null || str.isEmpty()) {
			return str;
		}
		
		return str.trim().replaceAll("\\s+", " ");
	}
	
	/**
	 * 去除重复值(值间用英文逗号分隔)
	 * 
	 * @param values(值间用英文逗号分隔)
	 * @return 去重后的值(值间用英文逗号分隔)
	 */
	public static String ridRepetition(String values) {
		if (values == null || values.trim().isEmpty()) {
			return values;
		}
		
		Set<String> set = new HashSet<String>();
		StringBuilder sb = new StringBuilder();
		String[] valueArr = values.split(",");
		for (String value : valueArr) {
			value = value.trim();
			if (!value.isEmpty()) {
				if (set.add(value)) {
					sb.append(value).append(',');
				}
			}
		}
		if (sb.length() > 0) {
			sb.setLength(sb.length() - 1);
		}
		return sb.toString();
	}
	
	/**
	 * 移除文本中的符号，且转换成小写字母
	 * 
	 * @param text
	 * @return
	 */
	public static String removeSymbolAndToLowerCase(String text) {
		if (text == null || text.isEmpty()) {
			return text;
		}
		
		StringBuilder tmpText = new StringBuilder();
		List<String> wordList = SearchUtil.getTokenList(text, standardAnalyzer); // 分词
		for (String word : wordList) {
			tmpText.append(word);
		}
		String newText = tmpText.toString().replace("_", "").replace(".", "");
		return newText;
	}
}
