<?php
/**
 * 用户管理控制器
 */
Class UserAction extends CommonAction {

	/**
	 *  用户列表
	 */
	Public function index() {
		import('ORG.Util.Page');
		$count = M('user') -> count();
		$page = new Page($count, 20);
		$limit = $page -> firstRow . ',' . $page -> listRows;
		$this -> users = D('user') -> limit($limit) -> select();

		$this -> page = $page -> show();
		$this -> display();
	}

	/**
	 * 锁定用户
	 */
	Public function lockUser() {
		$data = array('userid' => $this -> _get('userid', 'intval'), 'lock' => $this -> _get('lock', 'intval'));

		$msg = $data['lock'] ? '锁定' : '解锁';
		if ( M('user') -> save($data)) {
			$this -> success($msg . '成功', $_SERVER['HTTP_REFERER']);
		} else {
			$this -> error($msg . '失败，请重试...');
		}
	}

	/**
	 * 微博用户检索
	 */
	Public function seachUser() {

		if (isset($_GET['sech']) && isset($_GET['type'])) {
			$where = $_GET['type'] ? array('userid' => $this -> _get('sech', 'intval')) : array('username' => array('LIKE', '%' . $this -> _get('sech') . '%'));

			$users = D('user') -> where($where) -> select();
			$this -> users = $users ? $users : false;
		}
		$this -> display();
	}

	/**
	 * 后台管理员列表
	 */
	Public function admin() {
		$userid = $_SESSION['aid'];
		$this -> admin = M('admin') -> where('id <> ' . $userid) -> select();

		$this -> display();
	}

	/**
	 * 添加后台管理员
	 */
	Public function addAdmin() {
		$this -> display();
	}

	/**
	 * 锁定后台管理员
	 */
	Public function lockAdmin() {
		$data = array('id' => $this -> _get('id', 'intval'), 'lock' => $this -> _get('lock', 'intval'));

		$msg = $data['lock'] ? '锁定' : '解锁';
		if ( M('admin') -> save($data)) {
			$this -> success($msg . '成功', U('admin'));
		} else {
			$this -> error($msg . '失败，请重试...');
		}
	}

	/**
	 * 删除后台管理员
	 */
	Public function delAdmin() {
		$id = $this -> _get('id', 'intval');
		if ( M('admin') -> delete($id)) {
			$this -> success('删除成功', U('admin'));
		} else {
			$this -> error('删除失败，请重试...');
		}
	}

	/**
	 * 执行添加管理员操作
	 */
	Public function runAddAdmin() {
		if ($_POST['pwd'] != $_POST['pwded']) {
			$this -> error('两次密码不一致');
		}
		$data = array(
			'username' => $this -> _post('username'), 
			'password' => $this -> _post('pwd', 'md5'), 
			'logintime' => date("Y-m-d h:i:s", time()),
			'loginip' => get_client_ip(),
			'admin' => $this -> _post('admin', 'intval')
		 );
		if ( M('admin') -> where('username = ' . $this -> _post('username')) -> select()) {
			$this -> error('添加失败，请重试...');
		} else if ( M('admin') -> data($data) -> add()) {
			$this -> success('添加成功', U('admin'));
		} else {
			$this -> error('添加失败，请重试...');
		}
	}

	/**
	 * 修改密码视图
	 */
	Public function editPwd() {
		$this -> display();
	}

	/**
	 * 修改密码操作
	 */
	Public function runEditPwd() {
		$db = M('admin');
		$old = $db -> where(array('id' => session('aid'))) -> getField('password');

		if ($old != md5($_POST['old'])) {
			$this -> error('旧密码错误');
		}

		if ($_POST['pwd'] != $_POST['pwded']) {
			$this -> error('两次密码不一致');
		}

		$data = array('id' => session('aid'), 'password' => $this -> _post('pwd', 'md5'));

		if ($db -> save($data)) {
			$this -> success('修改成功', U('Index/copy'));
		} else {
			$this -> error('修改失败，请重试...');
		}
	}

}
?>