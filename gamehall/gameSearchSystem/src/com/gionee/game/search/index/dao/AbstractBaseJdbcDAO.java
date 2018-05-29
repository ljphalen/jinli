package com.gionee.game.search.index.dao;

import javax.sql.DataSource;

import org.springframework.jdbc.core.namedparam.NamedParameterJdbcTemplate;

abstract public class AbstractBaseJdbcDAO {

	private NamedParameterJdbcTemplate namedParameterJdbcTemplate;

	// private SimpleJdbcTemplate simpleJdbcTemplate;

	public AbstractBaseJdbcDAO(DataSource dataSource) {
		this.namedParameterJdbcTemplate = new NamedParameterJdbcTemplate(
				dataSource);
		// this.simpleJdbcTemplate = new SimpleJdbcTemplate(dataSource);
	}

	public NamedParameterJdbcTemplate getNamedParameterJdbcTemplate() {
		return namedParameterJdbcTemplate;
	}

	// public SimpleJdbcTemplate getSimpleJdbcTemplate() {
	// return simpleJdbcTemplate;
	// }

	/**
	 * 统计批量更新成功的记录数
	 * 
	 * @param updateStatus
	 *            ，记录更新状态，1:更新成功, 0:更新失败
	 * @return 更新成功的记录数
	 */
	protected int statUpdateSuccRecordNum(int[] updateStatus) {
		int num = 0;
		for (int status : updateStatus) {
			if (1 == status) {
				num++;
			}
		}
		return num;
	}
}
