package com.gionee.game.search.server.bean.resultBean;

public class PinyinBean extends BaseBean {
	private static final long serialVersionUID = -5369408993945630145L;

	// 拼音
	private String pinyin;

	// 纠错词
	private String keyword;

	// 热度
	private int hot;

	// 描述
	private String description;

	public String getPinyin() {
		return pinyin;
	}

	public void setPinyin(String pinyin) {
		this.pinyin = pinyin;
	}

	public String getKeyword() {
		return keyword;
	}

	public void setKeyword(String keyword) {
		this.keyword = keyword;
	}

	public int getHot() {
		return hot;
	}

	public void setHot(int hot) {
		this.hot = hot;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}
}
