<?php

Class SeasonAction extends   CommonAction {

	Public function index() {
		if (!isset($_GET['sid'])) {
			halt('页面不存在');
		}
		$showid = $_GET['sid'];
		$this -> showid = $showid;
		import('ORG.Util.Page');
		$where = array('showid' => $showid);
		$count = M('season') -> where($where) -> count();
		$page = new Page($count, 2);
		$limit = $page -> firstRow . ',' . $page -> listRows;
		$seasons = M('season') -> where($where) -> order('seasonnum ASC') -> limit($limit) -> select();
		$this -> page = $page -> show();
		$this -> seasons = $seasons ? $seasons : false;
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
				$this -> showid = $_GET['showid'];
				$this -> display('add');
			} else if ($_GET['station'] == 'edit') {
				$seasonid = $_GET['seasonid'];
				$this -> season = M('season') -> where('seasonid= ' . $seasonid) -> find();
				$this -> display('edit');
			} else {
				halt('页面不存在');
			}
		}
	}

	Public function editSeason() {
		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		 
		$season = M('season');
		$showid = $season -> where('seasonid = ' . $_POST['seasonid']) -> getField('showid');
		$seasonnum = $season -> where('seasonid = ' . $_POST['seasonid']) -> getField('seasonnum');
		$num_where = array('showid' => $showid, 'seasonnum' => $_POST['seasonnum']);
		if (!$season -> where($num_where) -> find() || $seasonnum == $_POST['seasonnum']) {
			$where = array('seasonid' => $_POST['seasonid']);
			$field = array('covermax', 'covermini');
			$old = $season -> where($where) -> field($field) -> find();
			if ($season -> where($where) -> save($_POST)) {
				if (!empty($old['covermax'])&&($old['covermax']!= $_POST['covermax'])) {
					@unlink('./Uploads/Cover/' . $old['covermax']);
					@unlink('./Uploads/Cover/' . $old['covermini']);
				}
				$this -> success('修改成功', U('index', array('sid' => $showid)));
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
	Public function addSeason() {
		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		$season = M("season");
		$showid = $_POST['showid'];
		$seasonnum = $_POST['seasonnum'];
		$where = array('showid' => $showid, 'seasonnum' => $seasonnum);
		if (!$season -> where($where) -> find()) {
			if ($season -> add($_POST)) {
				$this -> success('修改成功', U('index', array('sid' => $showid)));
			}
		} else {
			$this -> error('修改失败，请重试...');
		}
	}

	/*
	 * 删除美剧
	 */
	Public function delSeason() {
		if (!$this -> isGet()) {
			halt('页面不存在');
		}
		$seasonid = $_GET['seasonid'];
		$showid = $_GET['showid'];
		$where = 'seasonid = ' . $seasonid;
		$db = M('season');
		if ($db -> where($where) -> delete()) {
			$this -> success('删除成功', U('index', array('sid' => $showid)));
		} else {
			$this -> error('删除失败，请重试...');
		}
	}

}
?>