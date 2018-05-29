package com.gionee.game.search.server.service;

import java.io.UnsupportedEncodingException;
import java.text.SimpleDateFormat;
import java.util.Date;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.apache.log4j.Logger;

import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.resultBeans.BaseBeans;
import com.gionee.game.search.server.cache.SearchCache;
import com.gionee.game.search.server.service.impl.SearchServiceImplBase;
import com.gionee.game.search.server.util.Constants;
import com.gionee.game.search.server.util.SystemProperties;


/**
 * 搜索服务基类
 *
 */
public abstract class SearchServiceBase implements SearchService {
	protected SearchCache searchCache; // 搜索结果缓存器
	
	protected static final Log log = LogFactory.getLog(SearchServiceBase.class);
	
	private static Logger searchLog = Logger.getLogger("searchLog");
	

	/**
	 * 写日志，统计数据时使用
	 * 
	 * @param schParam
	 * @param beans
	 */
	public void writeLog(SchParam schParam, BaseBeans beans) {
		try {
			String logInfo = getLogInfo(schParam, beans);
			searchLog.info(new String(logInfo.getBytes("UTF-8"), System
					.getProperty("file.encoding")));
		} catch (UnsupportedEncodingException e) {
			log.error(e, e);
		}
	}
	
	private String getLogInfo(SchParam schParam, BaseBeans beans) {
		String splitstr = "{]"; // 分隔符

		StringBuilder sb = new StringBuilder();
		sb.append("v1"); // 1.版本号
		sb.append(splitstr);
		sb.append(schParam.getChannelId());// 2.频道号
		sb.append(splitstr);
		sb.append(schParam.getSubChannelId());// 3.子频道号
		sb.append(splitstr);
		sb.append(schParam.getForbid()); // 4.禁词标识(0:非禁词,1:禁词)
		sb.append(splitstr);
		sb.append(schParam.getKeyword());// 5.搜索关键词
		sb.append(splitstr);
		sb.append(beans.getTotalCount());// 6.搜索结果数
		sb.append(splitstr);
		sb.append(schParam.getPageNum());// 7.页码
		sb.append(splitstr);
		sb.append(schParam.getPageSize());// 8.页大小
		sb.append(splitstr);
		sb.append(schParam.getSort());// 9.排序方式
		sb.append(splitstr);
		sb.append(schParam.getSearchType());// 10.搜索类型
		sb.append(splitstr);
		sb.append(schParam.getSearchAction());// 11.搜索动作
		sb.append(splitstr);
		sb.append(beans.getSearchCostTime());// 12.搜索耗时
		sb.append(splitstr);
		sb.append(beans.isCached() ? Constants.CACHED : Constants.NO_CACHED);// 13.是否命中缓存
		sb.append(splitstr);
		sb.append(new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(new Date()));// 14.搜索时间
		sb.append(splitstr);
		sb.append(schParam.getIp());// 15.用户IP地址
		sb.append(splitstr);
		sb.append(schParam.getUa()); // 16.UA
		sb.append(splitstr);
		sb.append(schParam.getUid()); // 17.用户id(用户标识id)
		sb.append(splitstr);
		sb.append(schParam.getAccount()); // 18.用户账号
		sb.append(splitstr);
		sb.append(schParam.getSearchFrom()); // 19.搜索来源渠道
		sb.append(splitstr);
		sb.append(""); // 20.扩展字段(即备注)
		sb.append(splitstr);
		sb.append(SystemProperties.getProperty("serverIp")); // 21.搜索服务器ip
		sb.append(splitstr);
		sb.append("v1"); // 22.版本号

		return sb.toString();
	}
	
	/**
	 * 返回结果集
	 * 
	 * @param schParam
	 * @param searchServiceImpl
	 * @param searchKey
	 * @return
	 */
	protected BaseBeans search(SchParam schParam, SearchServiceImplBase searchServiceImpl,
			String searchKey) {
		int isCached = Constants.NO_CACHED;
		long startTime = System.currentTimeMillis();
		BaseBeans beans = null;
		// 缓存
		if (this.searchCache != null && searchKey != null) {
			beans = (BaseBeans) this.searchCache.findCache(searchKey);
		}
		if (beans == null) {
			beans = searchServiceImpl.search(schParam);
			if (beans != null && this.searchCache != null && searchKey != null) {
				this.searchCache.putCache(searchKey, beans);
			}
		} else {
			isCached = Constants.CACHED;
		}
		beans.setSearchCostTime(System.currentTimeMillis() - startTime);
		beans.setCached(isCached == Constants.CACHED ? true : false);

		// 写日志
		if (schParam.isLog() && (schParam.getKeyword() != null && !schParam.getKeyword().trim().isEmpty())) {
			this.writeLog(schParam, beans);
		}

		return beans;
	}
	
	/**
	 * 返回结果集，不写日志，不缓存
	 * 
	 * @param schParam
	 * @param searchServiceImpl
	 * @param searchKey
	 * @return
	 */
	protected BaseBeans search(SchParam schParam, SearchServiceImplBase searchServiceImpl) {
		long startTime = System.currentTimeMillis();
		BaseBeans beans = searchServiceImpl.search(schParam);
		beans.setSearchCostTime(System.currentTimeMillis() - startTime);
		return beans;
	}
	
	/**
	 * 通过类别ID搜索，返回结果集
	 * 
	 * @param schParam
	 * @param searchServiceImpl
	 * @param searchKey
	 * @return
	 */
	protected BaseBeans searchByType(SchParam schParam,
			SearchServiceImplBase searchServiceImpl, String searchKey) {
		int isCached = Constants.NO_CACHED;
		long startTime = System.currentTimeMillis();
		BaseBeans beans = null;
		// 缓存
		if (this.searchCache != null && searchKey != null) {
			beans = (BaseBeans) this.searchCache.findCache(searchKey);
		}
		if (beans == null) {
			beans = searchServiceImpl.searchByType(schParam);
			if (this.searchCache != null && beans != null && searchKey != null) {
				this.searchCache.putCache(searchKey, beans);
			}
		} else {
			isCached = Constants.CACHED;
		}
		beans.setSearchCostTime(System.currentTimeMillis() - startTime);
		beans.setCached(isCached == Constants.CACHED ? true : false);

		// 写日志
		if (schParam.isLog()) {
			this.writeLog(schParam, beans);
		}
		return beans;
	}
	
	/**
	 * 得到搜索缓存key
	 * 
	 * @param schParam
	 * @return 失败：null
	 */
	protected abstract String getCacheKey(SchParam schParam);
	
	
	public SearchCache getSearchCache() {
		return searchCache;
	}

	public void setSearchCache(SearchCache searchCache) {
		this.searchCache = searchCache;
	}
}
