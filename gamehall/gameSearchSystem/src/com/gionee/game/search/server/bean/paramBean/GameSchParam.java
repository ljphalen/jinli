package com.gionee.game.search.server.bean.paramBean;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

public class GameSchParam extends SchParam {
	public static final int SORT_RELEVANCE = 0;// 按相关度降序排列

	public String toString() {
		return ToStringBuilder.reflectionToString(this,
				ToStringStyle.SHORT_PREFIX_STYLE);
	}
}
