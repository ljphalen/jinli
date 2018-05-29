package com.gionee.game.search.index.dao;

import java.lang.reflect.Method;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.SQLException;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.springframework.jdbc.core.simple.ParameterizedRowMapper;

/**
 * 生成数据库表对应的Java实体类的RowMapper
 * 注：sql列名与java实体属性要一一对应，查询sql中列名必须小写，
 * sql列名对应的java实体属性，必须将"-_"两个字符删除，
 * 且将其后面的第一个字母转为大写，比如：“sql列名-->java实体属性”
 *                           “create_time-->createTime”
 *
 */
public class ParameterizedRowMapperFactory {
	private static Log log = LogFactory.getLog(ParameterizedRowMapperFactory.class);
	
	@SuppressWarnings("rawtypes")
	private static final Map<Class, ParameterizedRowMapper> rowMapperCache = new HashMap<Class, ParameterizedRowMapper>();

	/**
	 * 得到java实体类相应的RowMapper
	 * 
	 * @param entityClazz
	 * @return
	 */
	@SuppressWarnings("unchecked")
	public static <T> ParameterizedRowMapper<T> getParameterizedRowMapper(Class<T> entityClazz) {
		if (rowMapperCache.containsKey(entityClazz)) {
			return rowMapperCache.get(entityClazz);
		}
		
		Map<String, DataOperationMethod> methodMap = new HashMap<String, DataOperationMethod>();
		Class<ResultSet> resultSetClazz = ResultSet.class;
		for (Method method : entityClazz.getMethods()) {
			String methodName = method.getName();
			if (methodName.startsWith("set")) {
				String attrName = methodName.substring(3, 4).toLowerCase() + methodName.substring(4); // 属性名
				Class<?> parameterType = method.getParameterTypes()[0];
				try {
					if (parameterType.equals(String.class)) {
						methodMap.put(attrName, new DataOperationMethod(method, resultSetClazz.getMethod("getString", String.class)));
					} else if (parameterType.equals(int.class) || parameterType.equals(Integer.class)) {
						methodMap.put(attrName, new DataOperationMethod(method, resultSetClazz.getMethod("getInt", String.class)));
					} else if (parameterType.equals(long.class) || parameterType.equals(Long.class)) {
						methodMap.put(attrName, new DataOperationMethod(method, resultSetClazz.getMethod("getLong", String.class)));
					} else if (parameterType.equals(float.class) || parameterType.equals(Float.class)) {
						methodMap.put(attrName, new DataOperationMethod(method, resultSetClazz.getMethod("getFloat", String.class)));
					} else if (parameterType.equals(double.class) || parameterType.equals(Double.class)) {
						methodMap.put(attrName, new DataOperationMethod(method, resultSetClazz.getMethod("getDouble", String.class)));
					} else if (parameterType.equals(Date.class)) {
						methodMap.put(attrName, new DataOperationMethod(method, resultSetClazz.getMethod("getTimestamp", String.class)));
					} else {
						methodMap.put(attrName, new DataOperationMethod(method, resultSetClazz.getMethod("getString", String.class)));
					}
				} catch (Exception e) {
					log.error(e, e);
				}
			}
		}
		
		ParameterizedRowMapper<T> rowMapper = createRowMapper(entityClazz, methodMap);
		rowMapperCache.put(entityClazz, rowMapper); // 放入缓存
		
		return rowMapper;
	}

	/**
	 * 创建类相应的RowMapper
	 * 
	 * @param entityClazz
	 * @param dataOperationMethodMap
	 * @return
	 */
	private static <T> ParameterizedRowMapper<T> createRowMapper(final Class<T> entityClazz, final Map<String, DataOperationMethod> dataOperationMethodMap) {
		ParameterizedRowMapper<T> rowMapper = new ParameterizedRowMapper<T>() { // 匿名内部类
			public T mapRow(ResultSet rs, int rowNum) throws SQLException {
				T resource;
				try {
					resource = entityClazz.newInstance();
				} catch (Exception e) {
					log.error("can't instance " + entityClazz.getName(), e);
					return null;
				}
				ResultSetMetaData metaData = rs.getMetaData();
				int columnCount = metaData.getColumnCount();
				for (int i = 1; i <= columnCount; i++) {
					String colName = metaData.getColumnLabel(i);
					String attrName = toAttributeName(colName);
					DataOperationMethod dataOperationMethod = dataOperationMethodMap.get(attrName);
					if (null != dataOperationMethod) {
						Method entitySetMethod = dataOperationMethod.entitySetMethod;
						try {
							Object colValue = dataOperationMethod.resultSetGetMethod.invoke(rs, colName);
							if (null != colValue) {
								entitySetMethod.invoke(resource, colValue);
							}
						} catch (Exception e) {
							log.error(entitySetMethod.getName() + " method error", e);
						}
					} else {
						// log.error("not find attr : \t" + attrName);
					}
				}
				return resource;
			}
			
			/**
			 * sql列名 转换为 实体属性名(去除"-_"字符，且将其后面的第一个字母转换为大写)
			 * 
			 * @param colName
			 * @return
			 */
			private String toAttributeName(String colName) {
				boolean isSeparator = false;
				StringBuilder attrName = new StringBuilder(colName.length());
				for (int i = 0; i < colName.length(); i++) {
					char c = colName.charAt(i);
					if (c == '-' || c == '_') {
						isSeparator = true;
					}
					else {
						if (isSeparator && (c >= 'a' && c <= 'z')) { // 前面字符是分隔符，当前字符是小写字母，则转换为大写字母
							c -= 32; // 转为大写
						}
						attrName.append(c);
						isSeparator = false;
					}
				}
				return attrName.toString();
			}
		};
		return rowMapper;
	}
	
	/**
	 * 数据操作方法
	 *
	 */
	private static class DataOperationMethod {
		private Method entitySetMethod; // 实体set方法
		private Method resultSetGetMethod; // 结果集get方法

		public DataOperationMethod(Method entitySetMethod,
				Method resultSetGetMethod) {
			this.entitySetMethod = entitySetMethod;
			this.resultSetGetMethod = resultSetGetMethod;
		}
	}
}
