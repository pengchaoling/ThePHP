<?php
/**
 * Author: Lying
 * Data: 2016-11-01
 * description: 系统配置文件
 */
return array(
	'CODE_LEN'				=>	5,				//验证码长度
	'DEFAULT_TIME_ZONE'		=>	'PRC',			//默认北京时间 	
	'SESSION_AUTO_START'	=>	TRUE,			//自动开启session
	'VAR_CONTROLLER'		=>	'c',			//控制器参数名
	'VAR_ACTION'			=>	'a',			//控制器里的方法参数名
	'SAVE_LOG'				=>	TRUE,			//是否保存日志信息
	'ERROR_URL'				=>	'',				//关闭debug错误跳转的url
	'ERROR_MSG'				=>	'对不起，网站出错了！',//错误提示信息
	'AUTO_LOAD_FILE'		=>	array(),		//自动加载用户自定义的文件

	//数据库配置信息
	'DB_CHARSET'			=>	'utf-8',		//数据库编码
	'DB_HOST'				=>	'127.0.0.1',	//数据库主机
	'DB_USER'				=>	'root',			//数据库账号
	'DB_PASSWORD'			=>	'',				//数据库密码
	'DB_DATABASE'			=>	'',				//数据库名
	'DB_PORT'				=>	'3306',			//数据库端口
	'DB_PREFIX'				=>	'',				//数据库表前缀

	//smarty配置
	'SMARTY_ON'				=>	true,			//是否开启smarty	
	'LEFT_DELIMITER'		=>	'{',			//左定界符
	'RIGHT_DELIMITER'		=>	'}',			//右定界符	
	'CACHE_ON'				=>	false,			//是否开启缓存,
	'CACHE_TIME'			=>	60,				//缓存时间	 	
);
?>