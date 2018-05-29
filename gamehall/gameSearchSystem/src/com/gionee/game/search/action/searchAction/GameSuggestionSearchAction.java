package com.gionee.game.search.action.searchAction;

import net.sf.json.JSONObject;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.resultBeans.GameSuggestionBeans;
import com.gionee.game.search.server.service.GameSuggestionSearchService;

@SuppressWarnings("serial")
public class GameSuggestionSearchAction extends SearchActionBase {
	// 搜索服务
	private GameSuggestionSearchService gameSuggestionSearchService;

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
			SchParam schParam = new SchParam();
			super.setSchParam(schParam);

			// 搜索
			GameSuggestionBeans beans = (GameSuggestionBeans) gameSuggestionSearchService
					.search(schParam);

			// 输出json结果
			JSONObject jsonObj = JSONObject.fromObject(beans);
			writeJsonOut(jsonObj.toString());
		} catch (Exception e) {
			logger.error(e, e);
		}
	}

	public GameSuggestionSearchService getGameSuggestionSearchService() {
		return gameSuggestionSearchService;
	}

	public void setGameSuggestionSearchService(
			GameSuggestionSearchService gameSuggestionSearchService) {
		this.gameSuggestionSearchService = gameSuggestionSearchService;
	}

	@Override
	public String toString() {
		return ToStringBuilder.reflectionToString(this,
				ToStringStyle.SHORT_PREFIX_STYLE);
	}
}
