package com.gionee.game.search.server.service.impl;

import com.gionee.game.search.server.service.SearchService;
import com.gionee.game.search.server.util.Page;
import com.gionee.game.search.server.util.SearchUtil;


/**
 * 搜索服务实现基类
 *
 */
public abstract class SearchServiceImplBase implements SearchService {
    /**
     * 根据总记录数，当前页码，每页数返回起始行和结尾行，实现分页
     * 
     * @param totalCount
     * @param pageNum
     * @param pageSize
     * @return
     */
    protected Page getPage(int totalCount, int pageNum, int pageSize) {
        int startIndex = 0;
        int endIndex = 0;
        int maxCount = 0; // 最大结果数
        int maxPageNum = 0; // 最大页码

        maxCount = Math.min(totalCount, SearchUtil.SEARCH_TOP_COUNT);
        maxPageNum = (int) Math.ceil((double) maxCount / pageSize);
        
        // 确定当前页
        pageNum = Math.min(pageNum, maxPageNum);
        
        startIndex = Math.max((pageNum - 1) * pageSize, 0);
        endIndex = Math.min(pageNum * pageSize, maxCount);
        return new Page(startIndex, endIndex);
    }
}
