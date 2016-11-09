<?php
/**
 * 美剧日历控制器
 */
Class SeasonAction extends CommonAction {
	/**
	 * 登录页面
	 */
	Public function index() {
		if (isset($_GET['sid'])) {
			$showid = $this->_GET('sid');
			$this -> showid = $showid;
			if (isset($_GET['seasonnum'])) {
				$seasonnum = $this->_GET('seasonnum');
			} else {
				$seasonnum = 1;
			}
			$this -> seasonnum = $seasonnum;
		} else {
			$this -> error('页面不存在');
		}
		$userid = $_SESSION['uid'];
		$this -> follow = $this -> isFollow($showid);
		$this -> seasoninfo = $this -> loadFolSea($showid, $seasonnum);
		$this -> showinfo = $this -> getShowInfo($showid);
		$this -> style = $this -> getStyle($showid);
	 
		
		$this -> display();
	}

	/*
	 * 查询season
	 */
	Public function loadFolSea($showid, $seasonnum) {
		$userid = $_SESSION['uid'];
		$season = M("season");
		$where['showid'] = $showid;
		$where['seasonnum'] = $seasonnum;
		if ($this -> isFollow($showid)) {
			$sql = 'select * from seasonView where showid = ' . $showid . ' and seasonnum = ' . $seasonnum . ' and userid = ' . $userid;
			$Model = new Model();
			$result = $Model -> query($sql);
		} else {
			$seasonid = $season -> where($where) -> field('seasonid') -> find();
			$episode = M("episode");
			$swhere['seasonid'] = $seasonid['seasonid'];
			$result = $episode -> where($swhere) -> select();
		}
		$arrlength = count($result);
		for ($x = 0; $x < $arrlength; $x++) {
			$result[$x]['broadcasttime'] = date('Y-m-d', strtotime($result[$x][broadcasttime]));
		}
		return $result;
	}

	/*
	 * 得到剧信息
	 */
	Private function getShowInfo($showid) {
		$season = M("season");
		$where = 'showid = ' . $showid;
		$seasonCount = $season -> where($where) -> group('showid') -> count('showid');
		$Model = new Model();
		$sql = 'select * from tvshow where showid = ' . $showid;
		$showinfo = $Model -> query($sql);
	    $sql = 'select AVGscore from showView where showid = ' . $showid;
		$AVGscore = $Model -> query($sql);
		$showinfo[0][AVGscore] = number_format($AVGscore[0][AVGscore], 2);
		$showinfo[0][seasoncount] = $seasonCount;
		return $showinfo;
	}

	/*
	 * 查询用户是否关注这个美剧
	 */
	Private function isFollow($showid) {
		$userid = $_SESSION['uid'];
		$sql = 'select * from watchView where showid = ' . $showid . ' and userid = ' . $userid;
		$Model = new Model();
		$result = $Model -> query($sql);
		if (count($result) > 0)
			return 1;
		else
			return 0;
	}

	Private function getStyle($showid) {

		$sql = 'select stylename from styleView where showid = ' . $showid;
		$Model = new Model();
		$result = $Model -> query($sql);
		return $result;

	}

	

}
?>