package com.gionee.game.search.server.util;

import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.paramBean.SchParamConstants;


public class CacheKeyUtil extends SearchUtil {
//	/**
//	 * 得到搜索缓存key
//	 * 
//	 * @param param
//	 * @return 失败：null
//	 */
//	public static String getCacheKey(SchParam param) {
//		try {
//			if (param.getKeyword() != null) {
//				// 关键词忽略大小写
//				String keyword = param.getKeyword();
//				param.setKeyword(keyword.toLowerCase());
//				String md5 = StringUtil.md5(param.toString());
//				param.setKeyword(keyword);
//				return md5;
//			}
//			else {
//				return StringUtil.md5(param.toString());
//			}
//		} catch (Exception e) {
//			logger.warn(e.getMessage(), e);
//			return null;
//		}
//	}
	
	/**
	 * 得到搜索缓存key
	 * 
	 * @param schParam
	 * @return 失败：null
	 */
	public static String getCacheKey(SchParam schParam) {
		try {
			if (schParam.getPageNum() * schParam.getPageSize() > 300
					|| schParam.isDebug()) { // 不缓存
				return null;
			}

			SchParam clone = schParam.clone(); // 克隆一份

			// 关键词忽略大小写
			if (clone.getKeyword() != null && !clone.getKeyword().isEmpty()) {
				clone.setKeyword(clone.getKeyword().toLowerCase());
			}

			// 忽略如下不影响搜索结果的参数
			clone.setLog(false);
			clone.setSearchAction(SchParamConstants.SEARCH_ACTION_UNKNOWN);
			clone.setSearchFrom(SchParamConstants.SEARCH_FROM_UNKNOWN);
			clone.setIp("");
			clone.setUa("");
			clone.setUid("");
			clone.setAccount("");

			return StringUtil.md5(clone.toString());
		} catch (Exception e) {
			logger.warn(e.getMessage(), e);
			return null;
		}
	}

	public static void main(String[] args) {
	}
}
