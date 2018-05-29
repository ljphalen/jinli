<?php
/**
 * 用户FTP上传目录
 * 默认配置
 */
return array(
		/* 数据库配置 */
		'FTPD_HOST'						=>'42.121.237.23',	//生成ftp上传账号的主机
		'FTPD_PORT'						=>21,					//生成ftp上传账号的端口
		'FTPD_PATH'						=>'/data/www/dev/Data/Attachments/dev/ftp/',		//用户宿主目录的前缀，如将用户权限限制在/a/，则生成的用户宿主目录为/a/userid/，要求有写权限，该目录要求使用NFS进行共享，能完成文件迁移		

		'FTPD_UID'						=>'2001',
		'FTPD_GID'						=>'2001',

		'FTPD_DB_TABLE'					=>'users',				//pureftpd权限数据库用户表
		
		//pureftpd权限数据库dsn
		'FTPD_DB_CONFIG' => array(
				'db_type'  => 'mysql',
				'db_user'  => 'root',
				'db_pwd'   => 'root',
				'db_host'  => '127.0.0.1',
				'db_port'  => '3306',
				'db_name'  => 'pureftpd'
		),
		
		//定时清空创建的FTP账号，默认定时清空 2 天以前的账号，计划任务与差分包生成合并在一起
		'CLEANUP_FTP_ACCOUNT_DAY'		=>2,
);
