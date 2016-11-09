<?php
/**
 * 美剧日历控制器
 */
Class EpisodeAction extends  CommonAction {
	/**
	 * 登录页面
	 */
	Public function index() {
		if (isset($_GET['eid'])) {
			$episodeid = $this -> _GET('eid');

		} else {
			$this -> error('页面不存在');
		}
		$Model = new Model();
		$seasonid = M("episode") -> where('episodeid = ' . $episodeid) -> field('seasonid') -> find();
		$sql = 'select episodeid,episodenum from episode where seasonid = ' . $seasonid['seasonid'].' order by episodenum';
		$episodes = $Model -> query($sql);
		$this -> episodes = $episodes;
		$this -> episode = $this -> getEpisode($episodeid);
		$this -> follow = $this -> isFollow($episodeid);
		//p(	$this -> follow);
		//加载评论
		import('ORG.Util.Page');
		$count = M('comment') -> where('episodeid = ' . $episodeid . ' and remove= 0') -> count();
		$page = new Page($count, 2);
		$limit = $page -> firstRow . ',' . $page -> listRows;
		$sql = 'select * from commentView where episodeid = ' . $episodeid . ' and remove= 0 order by commenttime DESC limit ' . $limit;
		$this -> comments = $Model -> query($sql);
		$this -> userid = $_SESSION['uid'];
		$this -> page = $page -> show();
		$this -> display();
	}

	Public function getEpisode($episodeid) {
		$Model = new Model();
		$sql = 'select * from episodeView where episodeid = ' . $episodeid;
		$result = $Model -> query($sql);
		$result = $result[0];
		$arrlength = count($result);
		$result['data'] = date("Y-m-d", strtotime($result[broadcasttime]));
		$result['time'] = date("H:i", strtotime($result[broadcasttime]));
		return $result;
	}

	/*
	 * 查询用户是否关注这个美剧
	 */
	Private function isFollow($episodeid) {
		$userid = $_SESSION['uid'];
		$sql = 'select * from watchView where  episodeid = ' . $episodeid . ' and userid = ' . $userid;
		$Model = new Model();
		$result = $Model -> query($sql);
		if (count($result) > 0)
			return $result[0][watched];
		else
			return 2;
	}

	/*
	 * 评论操作
	 */
	Public function comment() {
		$data['comment'] = trim($this -> _post('comment'));
		$data['episodeid'] = $this -> _post('episodeid');
		$data['userid'] = $_SESSION['uid'];
		$data['commenttime'] = date('Y-m-d H:i:s', time());
		$floor = M("comment") -> where('episodeid = ' . $this -> _post('episodeid')) -> max('floor');
		$data['floor'] = $floor ? $floor + 1 : 1;
		if ( M("comment") -> add($data)) {
			$this -> success('评论成功', U('index', array('eid' => $data['episodeid'])));
		} else {
			$this -> error('评论失败，请重试...');
		}
	}

	/*
	 * 删除评论
	 */
	Public function deleComment() {

		$commentid = trim($this -> _get('commentid'));
	 
	 
		if ( M("comment") ->where('commentid = '.$commentid)-> delete()) {
			$this -> success('删除评论成功', U('index', array('eid' => $this -> _get('episodeid'))));
		} else {
			$this -> error('删除评论失败，请重试...');
		}
	}

	Public function getRate() {
		if (!$this -> isAjax()) {
			halt('页面不存在');
		}
		$userid = $_SESSION['uid'];
		$episodeid = $this -> _post('eid');
		$where = array('episodeid' => $episodeid, 'userid' => $userid);
		$score = M('watch') -> where($where) -> getField('score');

		if ($score) {
			echo $score;
		} else {
			echo 0;
		}
	}

	Public function runRating() {
		if (!$this -> isAjax()) {
			halt('页面不存在');
		}
		$userid = $_SESSION['uid'];
		$episodeid = $this -> _post('eid');
		$score = $this -> _post('rate');

		$where = array('episodeid' => $episodeid, 'userid' => $userid);
		$watchid = M('watch') -> where($where) -> getField('watchid');

		if ($watchid) {

			if ( M('watch') -> where('watchid = ' . $watchid) -> getField('watched')) {

				$data['score'] = $score;
				if ( M('watch') -> where('watchid = ' . $watchid) -> save($data)) {
					echo $score;
				}

			} else {
				echo "您还没有观看这集美剧,不能进行评分";
			}

		} else {
			echo "您还没有关注这部美剧,不能进行评分";
		}
	}

}
?>