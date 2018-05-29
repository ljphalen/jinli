package com.gionee.game.search.server.util;

import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * 系统属性配置文件管理类
 *
 */
public class SystemProperties {
	private static final Log logger = LogFactory.getLog(SystemProperties.class);
	private static Properties properties = new Properties();
	static {
		InputStream in = null;
		try {
			in = SystemProperties.class
					.getResourceAsStream("/system.properties");
			properties.load(in);
		} catch (IOException e) {
			logger.error(e, e);
		} finally {
			try {
				if (in != null) {
					in.close();
				}
			} catch (IOException e) {
				logger.error(e, e);
			}
		}
	}

	/**
	 * 得到键值
	 * 
	 * @param key
	 * @return
	 */
	public static String getProperty(String key) {
		return properties.getProperty(key);
	}
	
	public static void main(String[] args) {
		System.out.println(SystemProperties.getProperty("serverIp"));
	}
}
