<?php
/**
 * 账号设置控制器
 */
Class UserSettingAction extends CommonAction {

	/**
	 * 用户基本信息设置视图
	 */
	Public function index() {

		$userid = $_SESSION['uid'];
		$this -> user = M("user") -> where('userid = ' . $userid) -> field('username,sex,facemax,facemini') -> find();
		//p($this->user);
		$this -> display();

	}

	/**
	 * 修改用户基本信息
	 */
	Public function editBase() {

		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		$db = M('user');
		$where = array('userid' => $_SESSION['uid']);
		if ($db -> where($where) -> save($_POST)) {
			session('username', $_POST['username']);
			$this -> success('修改成功', U('index'));
		} else {
			$this -> error('修改失败，请重试...');
		}

	}

	/**
	 * 异步验证昵称是否已存在
	 */
	Public function checkUname() {
		if (!$this -> isAjax()) {
			halt('页面不存在');
		}
		$username = $this -> _post('username');
		$where = array('username' => $username);
		if ( M('user') -> where($where) -> getField('userid')) {
			echo 'false';
		} else {
			echo 'true';
		}
	}

	/**
	 * 修改用户头像
	 */
	Public function editFace() {

		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		$db = M('user');
		$where = array('userid' => $_SESSION['uid']);
		$field = array('facemax', 'facemini');
		$old = $db -> where($where) -> field($field) -> find();
		if ($db -> where($where) -> save($_POST)) {
			if (!empty($old['facemax'])) {
				@unlink('./Uploads/Face/' . $old['facemax']);
				@unlink('./Uploads/Face/' . $old['facemini']);
			}
			session('facemini', $_POST['facemini']);
			$this -> success('修改成功', U('index'));
		} else {
			$this -> error('修改失败，请重试...');
		}
	}

	/**
	 * 修改密码
	 */
	Public function editPsw() {
		if (!$this -> isPost()) {
			halt('页面不存在');
		}

		$db = M('user');
		//验证旧密码
		$where = array('userid' => session('uid'));

		$old = $db -> where($where) -> getField('password');

		if ($this -> _post('old', 'md5') != $old) {

			$this -> error('旧密码错误');
		}

		if ($this -> _post('pwd') != $this -> _post('pwded')) {

			$this -> error('两次密码不一致');

		}

		$newPwd = $this -> _post('pwd', 'md5');
		$data = array('userid' => session('uid'), 'password' => $newPwd);

		if ($db -> save($data)) {
			$this -> success('修改成功', U('Login/index'));
		} else {
			$this -> error('修改失败，请重试...');
		}
	}

}
?>