package com.gionee.game.search.server.listener;

import javax.servlet.ServletContextEvent;
import javax.servlet.ServletContextListener;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

import com.gionee.game.search.server.util.Constants;

public class MyServletContextListener implements ServletContextListener {
	private static final Log log = LogFactory.getLog(MyServletContextListener.class);
	
	public void contextInitialized(ServletContextEvent arg0) {
	}
	
	public void contextDestroyed(ServletContextEvent arg0) {
		try {
			Constants.jcsAdminBean.clearAllRegions(); // 清除所有缓存区
		} catch (Exception e) {
			log.error(e.getMessage(), e);
		}
	}
}
