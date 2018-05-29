package com.gionee.game.search.server.service;

import com.gionee.game.search.server.bean.otherBean.IndexBean;
import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.paramBean.SchParamConstants;
import com.gionee.game.search.server.bean.resultBeans.BaseBeans;
import com.gionee.game.search.server.service.impl.GameSuggestionSearchServiceImpl;
import com.gionee.game.search.server.service.impl.SearchServiceImplBase;
import com.gionee.game.search.server.util.SpringContextUtil;

public class GameSuggestionSearchService extends SearchServiceBase {
	/**
     * 关键词搜索
     * 
     * @param schParam
     * @return
     */
	public BaseBeans search(SchParam schParam) {
		schParam.setChannelId(SchParamConstants.CHANNEL_GAME); // 设置频道号
		schParam.setSubChannelId(SchParamConstants.CHANNEL_GAME_SUGGESTION); // 设置子频道号
		schParam.setLog(false); // 不记日志
		String searchKey = getCacheKey(schParam); // 得到搜索结果缓存key
		SearchServiceImplBase searchServiceImpl = (SearchServiceImplBase) SpringContextUtil
				.getBean("gameSuggestionSearchServiceImpl");
		return super.search(schParam, searchServiceImpl, searchKey);
	}

	/**
     * id搜索
     * 
     * @param schParam
     * @return
     */
	public BaseBeans searchById(SchParam schParam) {
		return null;
	}

	/**
     * 类型搜索
     * 
     * @param schParam
     * @return
     */
	public BaseBeans searchByType(SchParam schParam) {
		return null;
	}

	/**
	 * 同步索引(与数据库)
	 * 
	 */
	public void syncIndex() {
	}
	
	/** 
	 * 重建索引
	 * 
	 */
	public void recreateIndex() {
	}

	/**
	 * 得到索引路径
	 * 
	 * @return
	 */
	public String getIndexPath() {
		GameSuggestionSearchServiceImpl impl = (GameSuggestionSearchServiceImpl) SpringContextUtil
				.getBean("gameSuggestionSearchServiceImpl");
		return impl.getIndexPath();
	}

	/**
	 * 得到索引信息
	 * 
	 * @return
	 */
	public IndexBean getIndexBean() {
		GameSuggestionSearchServiceImpl impl = (GameSuggestionSearchServiceImpl) SpringContextUtil
				.getBean("gameSuggestionSearchServiceImpl");
		return impl.getIndexBean();
	}

	/**
	 * 得到搜索缓存key
	 * 
	 * @param schParam
	 * @return 失败：null
	 */
	@Override
	protected String getCacheKey(SchParam schParam) {
		try {
			if (schParam.getKeyword() == null
					|| schParam.getKeyword().isEmpty() || schParam.isDebug()) { // 不缓存
				return null;
			}

			StringBuilder cacheKey = new StringBuilder();

			// 关键词忽略大小写
			cacheKey.append(schParam.getKeyword().toLowerCase()).append("_")
					.append(schParam.getPageSize()).append("_")
					.append(schParam.isHighLight());

			return cacheKey.length() <= 100 ? cacheKey.toString() : null;
		} catch (Exception e) {
			log.warn(e.getMessage(), e);
			return null;
		}
	}
}
