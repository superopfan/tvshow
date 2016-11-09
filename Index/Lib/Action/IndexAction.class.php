<?php
/**
 * 美剧日历控制器
 */
Class IndexAction extends CommonAction {
	/**
	 * 登录页面
	 */
	Public function index() {
		// if (isset($_GET['star'])) {
		// 	$star = $this -> _GET('star');
		// } else {
		// 	$star = time();
		// }
		// // p( $star .'-------'. time() );
		// // p(date("Y-m-d  ",$star).'-------'.date("Y-m-d  ", time()));
		// if (isset($_GET['week'])) {
		// 	$week = $this -> _GET('week');
		// 	$this -> preweek = $week - 1;
		// 	$this -> nextweek = $week + 1;
		// 	$star = date("Y-m-d ", strtotime(($week . '  week'), $star));
		// } else {
		// 	$star = date("Y-m-d  ", $star);
		// 	$this -> preweek = '-1';
		// 	$this -> nextweek = '1';
		// }
		// $result = $this -> getWeek($star);
		// $this -> result = $result ? $result : false;
		$this -> display();
	}

	Public function getWeek($star) {
		$userid = $_SESSION['uid'];
		$end = date("Y-m-d  ", strtotime('1 week', strtotime($star)));
		$sql = 'select * from WatchView where userid = ' . $userid . ' and ( broadcasttime between "' . $star . '" and "' . $end . '") order by broadcasttime';
		$Model = new Model();
		$result = $Model -> query($sql);
		$arrlength = count($result);
		//分割时间
		for ($x = 0; $x < $arrlength; $x++) {
			$result[$x]['data'] = date("d", strtotime($result[$x][broadcasttime]));
			$result[$x]['time'] = date("H:i", strtotime($result[$x][broadcasttime]));
		}
		//转换时间
		foreach ($result as $v => $k) {
			$arr[$k[data]][info][] = $k;
			$arr[$k[data]]['month'] = date("M", strtotime($k[broadcasttime]));
			$arr[$k[data]]['day'] = date("l", strtotime($k[broadcasttime]));
		}
		return $arr;
	}

}
?>