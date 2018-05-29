package com.gionee.game.search.server.bean.resultBean;


/**
 * 游戏搜索结果
 *
 */
public class GameBean extends BaseBean {
	private static final long serialVersionUID = -5988403896326265355L;
	
	private String id;
	private String name;
	private String resume;
	private String label;
	private String monthDownloads;
	private String downloads;
	private String createTime;
	private String onlineTime;
	
	// 检索解释
	private String explain;

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

	public String getResume() {
		return resume;
	}

	public void setResume(String resume) {
		this.resume = resume;
	}

	public String getLabel() {
		return label;
	}

	public void setLabel(String label) {
		this.label = label;
	}

	public String getMonthDownloads() {
		return monthDownloads;
	}

	public void setMonthDownloads(String monthDownloads) {
		this.monthDownloads = monthDownloads;
	}

	public String getDownloads() {
		return downloads;
	}

	public void setDownloads(String downloads) {
		this.downloads = downloads;
	}

	public String getCreateTime() {
		return createTime;
	}

	public void setCreateTime(String createTime) {
		this.createTime = createTime;
	}

	public String getOnlineTime() {
		return onlineTime;
	}

	public void setOnlineTime(String onlineTime) {
		this.onlineTime = onlineTime;
	}

	public String getExplain() {
		return explain;
	}

	public void setExplain(String explain) {
		this.explain = explain;
	}
}
