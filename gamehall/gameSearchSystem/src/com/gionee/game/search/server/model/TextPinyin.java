package com.gionee.game.search.server.model;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

/**
 * 文本拼音
 *
 */
public class TextPinyin {
	private String text; // 处理过的文本
	private String fullPinyin = ""; // 全拼
	private String acronymPinYin = ""; // 缩拼（拼音首字母缩写）
	public String getText() {
		return text;
	}
	public void setText(String text) {
		this.text = text;
	}
	public String getFullPinyin() {
		return fullPinyin;
	}
	public void setFullPinyin(String fullPinyin) {
		this.fullPinyin = fullPinyin;
	}
	public String getAcronymPinYin() {
		return acronymPinYin;
	}
	public void setAcronymPinYin(String acronymPinYin) {
		this.acronymPinYin = acronymPinYin;
	}
	
	@Override
	public String toString() {
		return ToStringBuilder.reflectionToString(this,
				ToStringStyle.SHORT_PREFIX_STYLE);
	}
}
