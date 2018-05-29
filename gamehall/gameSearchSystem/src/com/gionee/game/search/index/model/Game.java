package com.gionee.game.search.index.model;

import java.util.Date;

/**
 * 游戏资源
 * 
 */
public class Game {
	private long id;
	private String name;
	private String resume;
	private String label;
	private int status;
	private long monthDownloads;
	private long downloads;
	private long createTime;
	private long onlineTime;
	
	private Date opTime;
	private long opTimeLong;
	
	private boolean offline; // 是否下线
	
	
	/***搜索联想词使用字段**************************************/
	private String suggestionName;
	private String suggestionNameFullPinYin;
	private String suggestionNameAcronymPinYin;

	public long getId() {
		return id;
	}

	public void setId(long id) {
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

	public int getStatus() {
		return status;
	}

	public void setStatus(int status) {
		this.status = status;
	}
	
	public long getMonthDownloads() {
		return monthDownloads;
	}

	public void setMonthDownloads(long monthDownloads) {
		this.monthDownloads = monthDownloads;
	}

	public long getDownloads() {
		return downloads;
	}

	public void setDownloads(long downloads) {
		this.downloads = downloads;
	}

	public long getCreateTime() {
		return createTime;
	}

	public void setCreateTime(long createTime) {
		this.createTime = createTime;
	}

	public long getOnlineTime() {
		return onlineTime;
	}

	public void setOnlineTime(long onlineTime) {
		this.onlineTime = onlineTime;
	}

	public Date getOpTime() {
		return opTime;
	}

	public void setOpTime(Date opTime) {
		this.opTime = opTime;
	}

	public long getOpTimeLong() {
		return opTimeLong;
	}

	public void setOpTimeLong(long opTimeLong) {
		this.opTimeLong = opTimeLong;
	}

	public boolean isOffline() {
		return offline;
	}

	public void setOffline(boolean offline) {
		this.offline = offline;
	}

	public String getSuggestionName() {
		return suggestionName;
	}

	public void setSuggestionName(String suggestionName) {
		this.suggestionName = suggestionName;
	}

	public String getSuggestionNameFullPinYin() {
		return suggestionNameFullPinYin;
	}

	public void setSuggestionNameFullPinYin(String suggestionNameFullPinYin) {
		this.suggestionNameFullPinYin = suggestionNameFullPinYin;
	}

	public String getSuggestionNameAcronymPinYin() {
		return suggestionNameAcronymPinYin;
	}

	public void setSuggestionNameAcronymPinYin(String suggestionNameAcronymPinYin) {
		this.suggestionNameAcronymPinYin = suggestionNameAcronymPinYin;
	}
}
