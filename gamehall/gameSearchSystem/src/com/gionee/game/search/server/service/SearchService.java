package com.gionee.game.search.server.service;

import com.gionee.game.search.server.bean.otherBean.IndexBean;
import com.gionee.game.search.server.bean.paramBean.SchParam;
import com.gionee.game.search.server.bean.resultBeans.BaseBeans;

public interface SearchService {
	/****搜索相关**************************************************************/
    /**
     * 关键词搜索
     * 
     * @param schParam
     * @return
     */
    public BaseBeans search(SchParam schParam);
    
    /**
     * id搜索
     * 
     * @param schParam
     * @return
     */
    public BaseBeans searchById(SchParam schParam);
    
    /**
     * 类型搜索
     * 
     * @param schParam
     * @return
     */
    public BaseBeans searchByType(SchParam schParam);
    
    
    
    
    /****索引相关**************************************************************/
    /**
	 * 同步索引(与数据库)
	 * 
	 */
	public void syncIndex();
	
	/**
	 * 重建索引
	 * 
	 */
	public void recreateIndex();
	
	/**
	 * 得到索引路径
	 * 
	 * @return
	 */
	public String getIndexPath();
	
	/**
	 * 得到索引信息
	 * 
	 * @return
	 */
	public IndexBean getIndexBean();
}
