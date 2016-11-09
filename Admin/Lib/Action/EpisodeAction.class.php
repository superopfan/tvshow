<?php

Class EpisodeAction extends   CommonAction {

	Public function index() {
		if (!isset($_GET['sid'])) {
			halt('页面不存在');
		}
		$seasonid = $_GET['sid'];
		$this -> seasonid = $seasonid;
		import('ORG.Util.Page');
		$where = array('seasonid' => $seasonid);
		$count = M('episode') -> where($where) -> count();
		$page = new Page($count, 2);
		$limit = $page -> firstRow . ',' . $page -> listRows;
		$episodes = M('episode') -> where($where) -> order('episodenum ASC') -> limit($limit) -> select();
		$this -> page = $page -> show();
		$this -> episodes = $episodes ? $episodes : false;
		$this -> display();
	}

	/*
	 *编辑页面
	 */
	Public function manage() {
		if (!$this -> isGet()) {
			halt('页面不存在');
		} else {
			if ($_GET['station'] == 'add') {
				$this -> seasonid = $_GET['seasonid'];
				$this -> display('add');
			} else if ($_GET['station'] == 'edit') {
				$episodeid = $_GET['episodeid'];
				$episode = M('episode') -> where('episodeid= ' . $episodeid) -> find();
				$episode['broadcasttime'] = date('Y-m-d', strtotime($episode['broadcasttime'])) . "T" . date('H:i:s', strtotime($episode['broadcasttime']));
				$this -> episode = $episode;
				$this -> display('edit');
			} else {
				halt('页面不存在');
			}
		}
	}

	Public function editEpisode() {
		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		$episode = M('episode');
		$seasonid = $episode -> where('episodeid = ' . $_POST['episodeid']) -> getField('seasonid');
		$episodenum = $episode -> where('episodeid = ' . $_POST['episodeid']) -> getField('episodenum');
		$num_where = array('showid' => $showid, 'seasonnum' => $_POST['seasonnum']);
		if (!$episode -> where($num_where) -> find() || $seasonnum == $_POST['seasonnum']) {
			$where = array('episodeid' => $_POST['episodeid']);
			if ($episode -> where($where) -> save($_POST)) {
				$this -> success('修改成功', U('index', array('sid' => $seasonid)));
			} else {
				$this -> error('修改失败，请重试...');
			}
		} else {
			$this -> error('修改失败，请重试...');
		}
	}

	/*
	 *新增季
	 */
	Public function addEpisode() {
		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		$episode = M("episode");
		$seasonid = $_POST['seasonid'];
		$episodenum = $_POST['episodenum'];
		$where = array('seasonid' => $seasonid, 'episodenum' => $episodenum);
		if (!$episode -> where($where) -> find()) {
			if ($episode -> add($_POST)) {
				$this -> success('修改成功', U('index', array('sid' => $seasonid)));
			}
		} else {
			$this -> error('修改失败，请重试...');
		}
	}

	/*
	 * 删除美剧
	 */
	Public function delEpisode() {
		if (!$this -> isGet()) {
			halt('页面不存在');
		}
		$seasonid = $_GET['seasonid'];
		$episodeid = $_GET['episodeid'];
		$where = 'episodeid = ' . $episodeid;
		$db = M('episode');
		if ($db -> where($where) -> delete()) {
			$this -> success('删除成功', U('index', array('sid' => $seasonid)));
		} else {
			$this -> error('删除失败，请重试...');
		}
	}

}
?>