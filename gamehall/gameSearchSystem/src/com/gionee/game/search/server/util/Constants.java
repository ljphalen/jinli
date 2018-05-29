package com.gionee.game.search.server.util;

import org.apache.jcs.admin.JCSAdminBean;
import org.apache.lucene.util.Version;

public class Constants {
	public static Version LUCENE_CURRENT_VERSION = Version.LUCENE_4_10_4; // lucene当前使用版本
	public static final int NO_CACHED = 0;
	public static final int CACHED = 1;
	
	public static JCSAdminBean jcsAdminBean = new JCSAdminBean(); // jcs缓存管理bean
}
