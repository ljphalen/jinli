package com.gionee.game.search.action.searchAction;

import net.sf.json.JSONObject;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

import com.gionee.game.search.server.bean.paramBean.GameSchParam;
import com.gionee.game.search.server.bean.resultBeans.GameBeans;
import com.gionee.game.search.server.service.GameSearchService;

@SuppressWarnings("serial")
public class GameSearchAction extends SearchActionBase {
	// 搜索服务
	private GameSearchService gameSearchService;

	@Override
	public String execute() throws Exception {
		return SUCCESS;
	}

	/**
	 * 搜索
	 * 
	 */
	public void search() {
		try {
			// 设置搜索参数
			GameSchParam gameSchParam = new GameSchParam();
			super.setSchParam(gameSchParam);
			
			// 搜索
			GameBeans beans = (GameBeans) gameSearchService.search(gameSchParam);
			
			// 设置结果回传参数
			beans.setPageNum(pageNum);
			beans.setPageSize(pageSize);

			// 输出json结果
			JSONObject jsonObj = JSONObject.fromObject(beans);
			writeJsonOut(jsonObj.toString());
		} catch (Exception e) {
			logger.error(e, e);
		}
	}

	public GameSearchService getGameSearchService() {
		return gameSearchService;
	}

	public void setGameSearchService(GameSearchService gameSearchService) {
		this.gameSearchService = gameSearchService;
	}

	@Override
	public String toString() {
		return ToStringBuilder.reflectionToString(this,
				ToStringStyle.SHORT_PREFIX_STYLE);
	}
}
