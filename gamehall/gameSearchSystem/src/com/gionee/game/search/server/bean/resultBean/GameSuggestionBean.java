package com.gionee.game.search.server.bean.resultBean;

public class GameSuggestionBean extends BaseBean {
	private static final long serialVersionUID = -5879880557461913854L;

	// 游戏id
	private String id;
	
	// 游戏名称
	private String name;
	
	// 热度量
	private String num;

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getNum() {
		return num;
	}

	public void setNum(String num) {
		this.num = num;
	}
}
