package com.gionee.game.search.server.service;

import com.gionee.game.search.server.bean.otherBean.IndexBean;
import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.paramBean.SchParamConstants;
import com.gionee.game.search.server.bean.resultBeans.BaseBeans;
import com.gionee.game.search.server.service.impl.GameSearchServiceImpl;
import com.gionee.game.search.server.service.impl.SearchServiceImplBase;
import com.gionee.game.search.server.util.CacheKeyUtil;
import com.gionee.game.search.server.util.SpringContextUtil;

public class GameSearchService extends SearchServiceBase {
	/**
     * 关键词搜索
     * 
     * @param schParam
     * @return
     */
	public BaseBeans search(SchParam schParam) {
		schParam.setChannelId(SchParamConstants.CHANNEL_GAME); // 设置频道号
		schParam.setSubChannelId(SchParamConstants.CHANNEL_GAME_RESOURCE); // 设置子频道号
		String searchKey = getCacheKey(schParam);// 得到搜索结果缓存key
		SearchServiceImplBase searchServiceImpl = (SearchServiceImplBase) SpringContextUtil
				.getBean("gameSearchServiceImpl");
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
		GameSearchServiceImpl impl = (GameSearchServiceImpl) SpringContextUtil
				.getBean("gameSearchServiceImpl");
		impl.syncIndex();
	}
	
	/**
	 * 重建索引 
	 * 
	 */
	public void recreateIndex() {
		GameSearchServiceImpl impl = (GameSearchServiceImpl) SpringContextUtil
				.getBean("gameSearchServiceImpl");
		impl.recreateIndex();
	}

	/**
	 * 得到索引路径
	 * 
	 * @return
	 */
	public String getIndexPath() {
		GameSearchServiceImpl impl = (GameSearchServiceImpl) SpringContextUtil
				.getBean("gameSearchServiceImpl");
		return impl.getIndexPath();
	}

	/**
	 * 得到索引信息
	 * 
	 * @return
	 */
	public IndexBean getIndexBean() {
		GameSearchServiceImpl impl = (GameSearchServiceImpl) SpringContextUtil
				.getBean("gameSearchServiceImpl");
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
		return CacheKeyUtil.getCacheKey(schParam);
	}
}
