package com.gionee.game.search.index.job;

import java.lang.reflect.Method;
import java.util.List;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.DoubleField;
import org.apache.lucene.document.Field;
import org.apache.lucene.document.FieldType;
import org.apache.lucene.document.FieldType.NumericType;
import org.apache.lucene.document.FloatField;
import org.apache.lucene.document.IntField;
import org.apache.lucene.document.LongField;
import org.apache.lucene.index.FieldInfo.IndexOptions;

/**
 * 索引工作基类
 * 
 */
public abstract class BaseIndexJob {
	protected Log logger = LogFactory.getLog(getClass());

	public static final FieldType STORE_YES_ANALYZED = new FieldType();
	public static final FieldType STORE_YES_ANALYZED_NO = new FieldType();
	public static final FieldType STORE_YES_INDEX_NO = new FieldType();
	public static final FieldType STORE_NO_ANALYZED = new FieldType();
	public static final FieldType STORE_NO_ANALYZED_NO = new FieldType();
	public static final FieldType STORE_YES_INT = IntField.TYPE_STORED;
	public static final FieldType STORE_YES_LONG = LongField.TYPE_STORED;
	public static final FieldType STORE_YES_FLOAT = FloatField.TYPE_STORED;
	public static final FieldType STORE_YES_DOUBLE = DoubleField.TYPE_STORED;

	static {
		STORE_YES_ANALYZED.setStored(true);
		STORE_YES_ANALYZED.setIndexed(true);
		STORE_YES_ANALYZED.setTokenized(true);
		STORE_YES_ANALYZED.setOmitNorms(false);
		STORE_YES_ANALYZED.setIndexOptions(IndexOptions.DOCS_AND_FREQS_AND_POSITIONS);
		STORE_YES_ANALYZED.freeze();

		STORE_YES_ANALYZED_NO.setStored(true);
		STORE_YES_ANALYZED_NO.setIndexed(true);
		STORE_YES_ANALYZED_NO.setTokenized(false);
		STORE_YES_ANALYZED_NO.setOmitNorms(false);
		STORE_YES_ANALYZED_NO.setIndexOptions(IndexOptions.DOCS_AND_FREQS_AND_POSITIONS);
		STORE_YES_ANALYZED_NO.freeze();

		STORE_YES_INDEX_NO.setStored(true);
		STORE_YES_INDEX_NO.setIndexed(false);
		STORE_YES_INDEX_NO.setTokenized(false);
		STORE_YES_INDEX_NO.setOmitNorms(true);
		STORE_YES_INDEX_NO.setIndexOptions(null);
		STORE_YES_INDEX_NO.freeze();

		STORE_NO_ANALYZED.setStored(false);
		STORE_NO_ANALYZED.setIndexed(true);
		STORE_NO_ANALYZED.setTokenized(true);
		STORE_NO_ANALYZED.setOmitNorms(false);
		STORE_NO_ANALYZED.setIndexOptions(IndexOptions.DOCS_AND_FREQS_AND_POSITIONS);
		STORE_NO_ANALYZED.freeze();

		STORE_NO_ANALYZED_NO.setStored(false);
		STORE_NO_ANALYZED_NO.setIndexed(true);
		STORE_NO_ANALYZED_NO.setTokenized(false);
		STORE_NO_ANALYZED_NO.setOmitNorms(false);
		STORE_NO_ANALYZED_NO.setIndexOptions(IndexOptions.DOCS_AND_FREQS_AND_POSITIONS);
		STORE_NO_ANALYZED_NO.freeze();
	}

	/**
	 * 运行方法
	 * 
	 */
	public abstract void execute();

	/**
	 * 创建文档
	 * 
	 * @param fieldList
	 * @param entity
	 * @return 失败:null
	 */
	public Document newDocument(List<MyField> fieldList, Object entity) {
		Document doc = new Document();
		Field field = null;

		for (MyField mf : fieldList) {
			try {
				Object value = mf.getMethod.invoke(entity);
				String fieldValue = (null == value ? "" : value.toString());
				NumericType numericType = mf.type.numericType();
				if (null == numericType) {
					field = new Field(mf.name, fieldValue, mf.type);
				} else if (numericType == FieldType.NumericType.INT) {
					field = new IntField(mf.name, Integer.parseInt(fieldValue),
							mf.type);
				} else if (numericType == FieldType.NumericType.LONG) {
					field = new LongField(mf.name, Long.parseLong(fieldValue),
							mf.type);
				} else if (numericType == FieldType.NumericType.FLOAT) {
					field = new FloatField(mf.name,
							Float.parseFloat(fieldValue), mf.type);
				} else if (numericType == FieldType.NumericType.DOUBLE) {
					field = new DoubleField(mf.name,
							Double.parseDouble(fieldValue), mf.type);
				} else {
					field = new Field(mf.name, fieldValue, mf.type);
				}
				field.setBoost(mf.boost);
				doc.add(field);
			} catch (Exception e) {
				logger.error(e.getMessage() + "===for field name==" + mf.name,
						e);
				return null;
			}
		}

		return doc;
	}

	/**
	 * 自定义lucene域
	 * 
	 */
	protected static class MyField {
		private String name;
		private FieldType type;
		private float boost;
		private Method getMethod;

		@SuppressWarnings("rawtypes")
		public MyField(String name, FieldType type, Class clazz)
				throws SecurityException, NoSuchMethodException {
			this(name, type, 1.0f, clazz);
		}

		@SuppressWarnings("rawtypes")
		public MyField(String name, FieldType type, float boost, Class clazz)
				throws SecurityException, NoSuchMethodException {
			this.name = name;
			this.type = type;
			this.boost = boost;
			this.getMethod = getMethod(name, clazz);
		}
		
		@SuppressWarnings({"unchecked", "rawtypes"})
		private static Method getMethod(String attrName, Class clazz)
				throws SecurityException, NoSuchMethodException {
			String methodName = "get" + attrName.substring(0, 1).toUpperCase()
					+ attrName.substring(1);// 首字母大写
			return clazz.getMethod(methodName);
		}
	}
}
