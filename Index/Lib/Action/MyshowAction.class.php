<?php
/**
 * 美剧日历控制器
 */
Class MyshowAction extends CommonAction {

	Public function index() {
		if (isset($_GET['search'])) {
			$this -> result = $this -> getSearchShow($this->_GET('search'));
		} else if (isset($_GET['styleid'])) {
			$this -> result = $this -> getShowByStyleid($this->_GET('styleid'));
		} else if ($_GET['action'] == 'myshow') {

			$this -> result = $this -> getMyShow();

		} else if ($_GET['action'] == 'allshow') {

			$this -> result = $this -> getAllShow();

		}

		$this -> display();
	}

	Private function getShowByStyleid($styleid) {

		$stylename = M("style") -> where('styleid = ' . $styleid) -> field('stylename') -> find();
		$sql = 'select * from styleView where styleid = ' . $styleid;
		$Model = new Model();
		$shows = $Model -> query($sql);
		$result[0]['title'] = $stylename['stylename'];
		$result[0]['shows'] = $shows;
		return $result;

	}

	Private function getMyShow() {
		$userid = $_SESSION['uid'];
		$sql = 'select showid from seasonView where userid = ' . $userid . ' group by showid';
		$Model = new Model();
		$showid = $Model -> query($sql);
		for ($i = 0; $i < count($showid); $i++) {
			$showids[] = $showid[$i]['showid'];
		}
		$where['showid'] = array('in', $showids);
		$shows = M('tvshow') -> where($where) -> field('showid, showname,covermax') -> select();
		$result[0]['title'] = '我的美剧';
		$result[0]['shows'] = $shows;
		return $result;
	}

	Private function getSearchShow($key) {

		$where = array('showname' => array('LIKE', '%' . $key . '%'));

		$shows = M('tvshow') -> where($where) -> field('showid, showname,covermax') -> order('showid DESC') -> select();

		$result[0]['title'] = '搜索 - ' . $key . ' - 结果';
		$result[0]['shows'] = $shows;

		return $result;

	}

	Private function getAllShow() {
		$result = M("style") -> select();
		$sql = 'select * from styleView ';
		$Model = new Model();
		$shows = $Model -> query($sql);
		for ($i = 0; $i < count($result); $i++) {
			$result[$i]['title'] = $result[$i]['stylename'];
			for ($x = 0; $x < 8; $x++) {
				if ($result[$i]['styleid'] == $shows[$x]['styleid']) {
					$result[$i]['shows'][] = $shows[$x];
				}
			}
		}
		return $result;
	}

}
?>