<?php
return array(

	//数据库配置
	'DB_HOST' => '127.0.0.1',	//数据库服务器地址
	'DB_USER' => 'root',	//数据库连接用户名
	'DB_PWD' => '12345678',	//数据库连接密码
	'DB_NAME' => 'show', //使用数据库名称
	'DB_PREFIX' => '',	//数据库表前缀

	//自定义模版潜换
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__' => __ROOT__ . '/Admin/Tpl/Public',
		),
			'URL_MODEL' => 1,

	'TOKEN_ON' => false,	//关闭令牌功能

	//用于异位或加密的KEY
	'ENCTYPTION_KEY' => 'www.tvshowtime.com',
	//自动登录保存时间
	'AUTO_LOGIN_TIME' => time() + 3600 * 24 * 7,	//一个星期

	//图片上传
	'UPLOAD_MAX_SIZE' => 2000000,	//最大上传大小
	'UPLOAD_PATH' => './Uploads/',	//文件上传保存路径
	'UPLOAD_EXTS' => array('jpg', 'jpeg', 'gif', 'png'),	//允许上传文件的后缀
);
?>