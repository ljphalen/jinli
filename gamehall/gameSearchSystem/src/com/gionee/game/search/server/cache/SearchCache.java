package com.gionee.game.search.server.cache;

/**
 * 搜索缓存
 *
 */
public interface SearchCache {
    public Object findCache(String key);
    
    public boolean putCache(String key,Object obj);
    
    public boolean clearCache();
}
