package com.gionee.game.search.index.dao;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import javax.sql.DataSource;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

import com.gionee.game.search.index.model.Game;

@SuppressWarnings("unchecked")
public class GameDao extends AbstractBaseJdbcDAO {
	private Log log = LogFactory.getLog(getClass());
	
	private static final String CHOOSED_FIELDS = " id,name,resume,label,status,month_downloads,downloads,create_time,online_time,op_time ";
	private static final String TABLES = " game_resource_games ";
	private static final String VALID_RES_CONDITION = " status=1 ";

	public GameDao(DataSource dataSource) {
		super(dataSource);
	}

	/**
	 * 查找最大的更新时间
	 */
	public Date findMaxUpdateTime() {
		String sql = "select max(op_time) from game_resource_games";

		Date date = (Date) this.getNamedParameterJdbcTemplate().queryForObject(
				sql, Collections.EMPTY_MAP, Date.class);

		return date;
	}

	/**
	 * 查找最小的id
	 */
	public long findMinID() {
		String sql = "select min(id) from game_resource_games";
		return this.getNamedParameterJdbcTemplate().queryForLong(sql,
				Collections.EMPTY_MAP);
	}
	
	/**
	 * 查找最大的id
	 */
	public long findMaxID() {
		String sql = "select max(id) from game_resource_games";
		return this.getNamedParameterJdbcTemplate().queryForLong(sql,
				Collections.EMPTY_MAP);
	}
	
	/**
	 * 查找全量索引资源列表(通过id范围查找,含startId,不含endId)
	 * 
	 * @param startId
	 * @param endId
	 * @return
	 */
	public List<Game> findFullIndexRes(long startId, long endId) {
		if (startId > endId) {
			return new ArrayList<Game>();
		}

		// 全量索引
		String validResCondition = !VALID_RES_CONDITION.isEmpty() ? " AND " + VALID_RES_CONDITION : ""; // 有效资源条件
		String sql = "select " + CHOOSED_FIELDS
				     + "from " + TABLES
				     + "where id>=:startId AND id<:endId" + validResCondition;

		Map<String, Object> paramMap = new HashMap<String, Object>();
		paramMap.put("startId", startId);
		paramMap.put("endId", endId);

		log.info("full index sql:\t" + sql);
		return this.getNamedParameterJdbcTemplate().query(
				sql,
				paramMap,
				ParameterizedRowMapperFactory
						.getParameterizedRowMapper(Game.class));
	}
	
	/**
	 * 查找增量索引资源数(不含开始时间，含结束时间)
	 * 
	 * @param beginTime
	 * @param endTime
	 * @return
	 */
	public long findIncrementIndexResNumber(Date beginTime, Date endTime) {
		if (beginTime == null || endTime == null) {
			return 0;
		}

		String sql = "select count(*) "
				   + "from " + TABLES
				   + "where op_time>:beginTime AND op_time<=:endTime ";

		Map<String, Object> paramMap = new HashMap<String, Object>();
		paramMap.put("beginTime", beginTime);
		paramMap.put("endTime", endTime);

		log.info("sql:\t" + sql);
		return this.getNamedParameterJdbcTemplate().queryForLong(sql, paramMap);
	}

	/**
	 * 查找增量索引资源列表(通过更新时间范围翻页查询，不含开始时间，含结束时间)
	 * 
	 * @param beginTime
	 * @param endTime
	 * @param start
	 * @param rows
	 * @return
	 */
	public List<Game> findIncrementIndexRes(Date beginTime, Date endTime,
			long start, long rows) {
		if (beginTime == null || endTime == null) {
			return new ArrayList<Game>();
		}

		// 增量索引
		String sql = "select " + CHOOSED_FIELDS
				+ "from " + TABLES
				+ "where op_time>:beginTime AND op_time<=:endTime "
				+ "ORDER BY op_time LIMIT :start,:rows";

		Map<String, Object> paramMap = new HashMap<String, Object>();
		paramMap.put("beginTime", beginTime);
		paramMap.put("endTime", endTime);
		paramMap.put("start", start);
		paramMap.put("rows", rows);

		log.info("increment index sql:\t" + sql);
		return this.getNamedParameterJdbcTemplate().query(
				sql,
				paramMap,
				ParameterizedRowMapperFactory
						.getParameterizedRowMapper(Game.class));
	}
	
	/**
	 * 查找有效资源数(更新时间<=@endTime)
	 * 
	 * @param endTime
	 * @return
	 */
	public int findValidResNum(Date endTime) {
		if (endTime == null) {
			return -1;
		}
		
		// 查找有效资源数
		String validResCondition = !VALID_RES_CONDITION.isEmpty() ? " AND " + VALID_RES_CONDITION : ""; // 有效资源条件
		String sql = "select count(*) " 
				+ "from " + TABLES
				+ "where op_time<=:endTime" + validResCondition;
		
		Map<String, Object> paramMap = new HashMap<String, Object>();
		paramMap.put("endTime", endTime);

		log.info("sql:\t" + sql);
		return this.getNamedParameterJdbcTemplate().queryForInt(sql, paramMap);
	}
	
	/**
	 * 查找有效资源id集合(更新时间<=@endTime)
	 * 
	 * @param endTime
	 * @return
	 */
	public Set<Long> findValidResIds(Date endTime) {
		Set<Long> idSet = new HashSet<Long>();

		if (endTime == null) {
			return idSet;
		}

		String validResCondition = !VALID_RES_CONDITION.isEmpty() ? " AND " + VALID_RES_CONDITION : ""; // 有效资源条件
		String sql = "select id "
				 + "from " + TABLES
				 + "where op_time<=:endTime" + validResCondition;
		
		Map<String, Object> paramMap = new HashMap<String, Object>();
		paramMap.put("endTime", endTime);

		log.info("sql:\t" + sql);
		List<Long> list = this.getNamedParameterJdbcTemplate().queryForList(
				sql, paramMap, Long.class);
		idSet.addAll(list);
		return idSet;
	}

	/**
	 * 查找资源(通过id)
	 * 
	 * @param id
	 * @return
	 */
	public Game findById(long id) {
		String sql = "select " + CHOOSED_FIELDS
			     + "from " + TABLES
			     + "where id=" + id;

		log.info("sql:\t" + sql);
		return this.getNamedParameterJdbcTemplate().queryForObject(
				sql,
				Collections.EMPTY_MAP,
				ParameterizedRowMapperFactory
						.getParameterizedRowMapper(Game.class));
	}
}
