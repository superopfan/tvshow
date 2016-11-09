<?php

Class ShowAction extends   CommonAction {

	Public function index() {
		import('ORG.Util.Page');
		$count = M('tvshow') -> count();
		$page = new Page($count, 2);
		$limit = $page -> firstRow . ',' . $page -> listRows;
		$this -> shows = D('tvshow') -> limit($limit) -> select();
		$this -> page = $page -> show();
		$this -> display();
	}

	/**
	 * 美剧检索
	 */

	Public function seach() {
		if (isset($_GET['sech']) && isset($_GET['type'])) {

			$type = $_GET['type'];

			if ($type == 1) {
				$where = array('showid' => $this -> _get('sech'));
				$shows = M('tvshow') -> where($where) -> select();
			} else if ($type == 2) {
				$where = array('showname' => array('LIKE', '%' . $this -> _get('sech') . '%'));

				import('ORG.Util.Page');

				$count = M('tvshow') -> where($where) -> count();
				$page = new Page($count, 2);
				$limit = $page -> firstRow . ',' . $page -> listRows;
				$shows = M('tvshow') -> where($where) -> order('showid DESC') -> limit($limit) -> select();
				$this -> page = $page -> show();
			}

			$this -> shows = $shows ? $shows : false;
		}
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
				$this -> style = M('style') -> select();
				$this -> display('add');
			} else if ($_GET['station'] == 'edit') {
				$showid = $_GET['showid'];
				$this -> show = M('tvshow') -> where('showid= ' . $showid) -> find();
				$style = M('style') -> select();
				$type = M('type') -> field('styleid') -> where('showid= ' . $showid) -> select();
				for ($i = 0; $i < count($type); $i++) {
					$typelist[] = $type[$i]['styleid'];
				}
				for ($i = 0; $i < count($style); $i++) {
					if (in_array($style[$i]['styleid'], $typelist)) {
						$style[$i]['isadd'] = 1;
					} else {
						$style[$i]['isadd'] = 0;
					}
				}
				$this -> type = $type;
				$this -> style = $style;
				//  p(	$this ->show);
				$this -> display('edit');
			} else {
				halt('页面不存在');
			}
		}
	}

	Public function editShow() {
		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		$db = M('tvshow');
		$where = array('showid' => $_POST['showid']);
		$field = array('covermax', 'covermini');
		$old = $db -> where($where) -> field($field) -> find();
		if ($db -> where($where) -> save($_POST)) {
			if (!empty($old['covermax']) && ($old['covermax'] != $_POST['covermax'])) {
				@unlink('./Uploads/Cover/' . $old['covermax']);
				@unlink('./Uploads/Cover/' . $old['covermini']);
			}
			$this -> success('修改成功', U('index'));
		} else if ($this -> changeStyle($_POST['showid'], $_POST['type'])) {
			$this -> success('修改成功', U('index'));

		} else {
			$this -> error('修改失败，请重试...');
		}
	}

	/*
	 *新增美剧
	 */
	Public function addShow() {
		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		$tvshow = M('tvshow');

		if ($tvshow -> add($_POST)) {
			$styleid = $_POST['type'];
			$showid = $tvshow -> getLastInsID();
			for ($i = 0; $i < count($styleid); $i++) {
				$type[] = array('showid' => $showid, 'styleid' => $styleid[$i]);
			}
			$typedb = M('type');
			$typedb -> addALL($type);
			$this -> success('增加成功', U('index'));
		} else {
			$this -> error('增加失败，请重试...');
		}
	}

	/*
	 * 删除美剧
	 */
	Public function delShow() {
		if (!$this -> isGet()) {
			halt('页面不存在');
		}
		$showid = $_GET['showid'];
		$where = 'showid = ' . $showid;
		$db = M('tvshow');
		if ($db -> where($where) -> delete()) {
			$this -> success('删除成功', U('index'));
		} else {
			$this -> error('删除失败，请重试...');
		}
	}

	/*
	 *修改类型
	 */
	Public function changeStyle($showid, $typeList) {
		$typedb = M("type");
		if ($typedb -> where('showid = ' . $showid) -> delete()) {
			for ($i = 0; $i < count($typeList); $i++) {
				$arr[] = array('showid' => $showid, 'styleid' => $typeList[$i]);
			}
			return $typedb -> addALL($arr);
		}
	}

	Public function style() {

		$this -> styles = D('style') -> select();

		$this -> display();

	}

	Public function editStyle() {
		if (!$this -> isAjax()) {
			$this -> error('页面不存在');
		}

		$data['styleid'] = $this -> _post('styleid');
		$data['stylename'] = $this -> _post('stylename');

		if ( M('style')  -> save($data)) {
			echo 1;
		} else {
			echo 0;
		}
	}
Public function addStyle() {
		if (!$this -> isAjax()) {
			$this -> error('页面不存在');
		}

	 
		$data['stylename'] = $this -> _post('stylename');

		if ( M('style') -> add($data)) {
			echo 1;
		} else {
			echo 0;
		}
	}
	Public function delStyle() {
		if (!$this -> isGet()) {
			halt('页面不存在');
		}
		$styleid = $_GET['styleid'];
		$where = 'styleid = ' . $styleid;
		$db = M('style');
		if ($db -> where($where) -> delete()) {
			$this -> success('删除成功', U('style'));
		} else {
			$this -> error('删除失败，请重试...');
		}
	}

}
?>