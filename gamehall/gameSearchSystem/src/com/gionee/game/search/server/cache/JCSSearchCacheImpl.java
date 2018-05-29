package com.gionee.game.search.server.cache;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.apache.jcs.JCS;
import org.apache.jcs.access.exception.CacheException;

public class JCSSearchCacheImpl implements SearchCache {
    private static final Log log = LogFactory.getLog(JCSSearchCacheImpl.class);
    private JCS cache;

    public JCSSearchCacheImpl(String cachename) {
        try {
            cache = JCS.getInstance(cachename);
        } catch (CacheException e) {
            log.error(e, e);
        }
    }

    public Object findCache(String key) {
        if(null != cache){
            return cache.get(key);
        }
        return null;
    }

    public boolean putCache(String key, Object obj) {
        try {
            cache.put(key, obj);
            return true;
        } catch (CacheException e) {
            log.error(e, e);
        }
        return false;
    }

    public boolean clearCache() {
        try {
            cache.clear();
            return true;
        } catch (CacheException e) {
            log.error(e, e);
        }
        return false;
    }
}
