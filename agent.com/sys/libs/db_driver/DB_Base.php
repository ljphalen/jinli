<?php
/**
 * 数据库操作基类， 定义数据库操作规范
 * @author jiangxinyu
 *
 */
abstract class DB_Base {
	/**
	 * 数据库连接
	 */
	protected abstract function connect();
	
	/**
	 * 选择数据库
	 */
	protected abstract function selectDb();
	
	/**
	 * 执行sql语句
	 */
	protected abstract function query();
	
	/**
	 * 查询记录的条数
	 */
	protected abstract function numRows();
	
	/**
	 * 获取查询的记录集
	 */
	protected abstract function getArray();
	
	/**
	 * 获取查询的记录， 只获取一行记录
	 */
	protected abstract function fetchRow();
	
	/**
	 * 释放资源
	 */
	protected abstract function freeResult();
	
	/**
	 * 获取查询的字段数
	 */
	protected abstract function numFields();
	
	/**
	 * 获取当前插入语句的自增id
	 */
	protected abstract function insertId();
	
	/**
	 * 返回受影响的行数
	 */
	protected abstract function affectedRows();
	
	/**
	 * 关闭数据库链接
	 */
	protected abstract  function close();
	
	/**
	 * 打印输出错误消息
	 */
	protected abstract function errorMsg();
}
?>