package com.gionee.game.search.server.bean.paramBean;

public class SchParamConstants {
	// 搜索行为
	public static final int SEARCH_ACTION_UNKNOWN = 0; // 未知
	public static final int SEARCH_ACTION_INITIATIVE = 1; // 主动搜索
	public static final int SEARCH_ACTION_PASSIVITY = 2; // 被动搜索
	
	// 搜索来源渠道
	public static final int SEARCH_FROM_UNKNOWN = 0; // 未知
	public static final int SEARCH_FROM_WEB = 1; // web
	public static final int SEARCH_FROM_WAP = 2; // wap(H5)
	public static final int SEARCH_FROM_ANDROID_CLIENT = 3; // 安卓客户端
	
	// 搜索词性质
	public static final int FORBID_YES = 1; // 禁词
	public static final int FORBID_NO = 0; // 非禁词
	
	// 是否高亮搜索词
	public static final int HIGHLIGHT_YES = 1; // 高亮
	public static final int HIGHLIGHT_NO = 0; // 不高亮
	
	// 是否要记录搜索日志
	public static final int LOG_YES = 1; // 记录
	public static final int LOG_NO = 0; // 不记录

	// 产品频道号
	public static final int CHANNEL_GAME = 1; // 游戏
	public static final int CHANNEL_GAME_RESOURCE = 1; // 游戏资源
	public static final int CHANNEL_GAME_SUGGESTION = 99; // 游戏建议词

	// 搜索高亮标签
	public static final String HIGHLIGHT_BEGIN = "<font color='red'>"; // 高亮开始标记符
	public static final String HIGHLIGHT_END = "</font>"; // 高亮结束标记符
}
