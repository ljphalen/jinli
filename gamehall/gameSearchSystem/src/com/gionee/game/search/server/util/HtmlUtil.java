package com.gionee.game.search.server.util;

import org.htmlcleaner.CleanerProperties;
import org.htmlcleaner.HtmlCleaner;
import org.htmlcleaner.TagNode;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class HtmlUtil {
	private static final Logger log = LoggerFactory.getLogger(HtmlUtil.class);
	
	/**
	 * 转义特殊的html字符
	 * 
	 * @param str
	 * @return 转义后的字符串
	 */
	public static String escapeSpecialHtmlChar(String str) {
		if (str == null || str.length() == 0) {
			return str;
		}

		str = str.replaceAll("&", "&amp;");
		str = str.replaceAll("<", "&lt;");
		str = str.replaceAll(">", "&gt;");
		str = str.replaceAll("'", "&apos;");
		str = str.replaceAll("\"", "&quot;");
		str = str.replaceAll("\\$", "\\$\\$");

		return str;
	}
	
	/**
	 * 抽取html纯文本数据
	 * 
	 * @param html
	 * @return 失败：空串("")
	 */
	public static String extractTextData(String html) {
		try {
			// take default cleaner properties
			CleanerProperties props = new CleanerProperties();

			// customize cleaner's behaviour with property setters
			props.setAllowHtmlInsideAttributes(true);
			props.setOmitComments(true);
			props.setOmitDoctypeDeclaration(true);
			props.setOmitXmlDeclaration(true);
			// props.setTreatUnknownTagsAsContent(true);
			// props.setOmitDeprecatedTags(true);
			props.setPruneTags("script,noscript,link,base,style,meta"); // 设置要删除的标签
			props.setAdvancedXmlEscape(true);
			
			HtmlCleaner htmlCleaner = new HtmlCleaner(props); // 创建一个htmlcleaner实例
			
			// Clean HTML taken from simple string, file, URL, input stream,
			// input source or reader. Result is root node of created
			// tree-like structure. Single cleaner instance may be safely used
			// multiple times.
			TagNode rootNode = htmlCleaner.clean(html);
			
			return rootNode.getText().toString().replace("&nbsp;", " ").trim(); // 返回纯文本数据;
		} catch (Exception e) {
			log.error(e.getMessage(), e);
		}
		
		return "";
	}
}
