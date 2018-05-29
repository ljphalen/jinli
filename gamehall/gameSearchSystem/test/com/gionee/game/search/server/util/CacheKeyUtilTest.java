package com.gionee.game.search.server.util;

import junit.framework.TestCase;

import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.paramBean.SchParamConstants;

public class CacheKeyUtilTest extends TestCase {
	@Override
	public void setUp() throws Exception {
		super.setUp();
	}
	
	public void testGetCacheKey() throws Exception {
		SchParam param = new SchParam();
		
		// 关键词大小写不敏感
		param.setKeyword("斗地主aaAaAA");
		
		// 变化参数
		param.setKeyword("欢乐斗地主");
		param.setSearchId(123456 + "");
		param.setPageNum(3);
		param.setPageSize(20);
		param.setChannelId(4);
		param.setSubChannelId(3);
		param.setSearchType(3);
		param.setSort(3);
		param.setHighLight(true);
		param.setForbid(SchParamConstants.FORBID_YES);
		
		// 忽略参数
		param.setLog(true);
		param.setSearchAction(SchParamConstants.SEARCH_ACTION_PASSIVITY);
		param.setSearchFrom(SchParamConstants.SEARCH_FROM_ANDROID_CLIENT);
		param.setIp("192.168.3.99");
		param.setUa("sdfasdfa242");
		param.setUid("34gads3456");
		param.setAccount("sdfas992342934");
		
		// debug参数
//		param.setDebug(true);
		
		String cacheKey = CacheKeyUtil.getCacheKey(param);
		System.out.println(cacheKey);
	}
}
