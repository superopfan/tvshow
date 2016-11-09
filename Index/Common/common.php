<?php

/**
 * 格式化打印数组
 */
function p($arr) {
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
	die();
}

/*
 * 查询用户是否关注这个美剧
 */
function isFollow($showid) {
	$userid = $_SESSION['uid'];
	$sql = 'select * from watchView where showid = ' . $showid . ' and userid = ' . $userid;
	$Model = new Model();
	$result = $Model -> query($sql);
	if (count($result) > 0)
		return 1;
	else
		return 0;
}

/**
 * 异位或加密字符串
 * @param  [String]  $value [需要加密的字符串]
 * @param  [integer] $type  [加密解密（0：加密，1：解密）]
 * @return [String]         [加密或解密后的字符串]
 */
function encryption($value, $type = 0) {
	$key = md5(C('ENCTYPTION_KEY'));
	if (!$type) {
		return str_replace('=', '', base64_encode($value ^ $key));
	}
	$value = base64_decode($value);
	return $value ^ $key;
}

/**
 * 格式化时间
 * @param  [type] $time [要格式化的时间戳]
 * @return [type]       [description]
 */
function time_format($time) {
	//当前时间
	$now = time();
	//今天零时零分零秒
	$today = strtotime(date('y-m-d', $now));
	//传递时间与当前时秒相差的秒数
	$diff = $now - $time;
	$str = '';
	switch ($time) {
		case $diff < 60 :
			$str = $diff . '秒前';
			break;
		case $diff < 3600 :
			$str = floor($diff / 60) . '分钟前';
			break;
		case $diff < (3600 * 8) :
			$str = floor($diff / 3600) . '小时前';
			break;
		case $time > $today :
			$str = '今天&nbsp;&nbsp;' . date('H:i', $time);
			break;
		default :
			$str = date('Y-m-d H:i:s', $time);
	}
	return $str;
}
?>