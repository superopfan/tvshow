<?php
/**
 * 共用控制器
 */
Class CommonAction extends Action {
	/**
	 *  自动运行的方法
	 */
	Public function _initialize() {
		//处理自动登录
		if (isset($_COOKIE['auto']) && !isset($_SESSION['uid'])) {
			$value = explode('|', encryption($_COOKIE['auto'], 1));
			$ip = get_client_ip();
			//本次登录IP与上一次登录IP一致时
			if ($ip == $value[1]) {
				$account = $value[0];
				$where = array('account' => $account);
				$user = M('user')->where($where)->field(array('userid', 'lock','username','facemini'))->find();
				//检索出用户信息并且该用户没有被锁定时，保存登录状态
				if ($user && !$user['lock']) {
					session('uid', $user['userid']);
					session('username', $user['username']);
					session('facemini', $user['facemini']);
				}
			}
		}
		//判断用户是否已登录
		if (!isset($_SESSION['uid'])) {
			redirect(U('Login/index'));
		}
		$userid = $_SESSION['uid'];	 
	}

	/**
	 *  标记是否已看
	 */
	Public function watch() {
		if (!$this -> isAjax()) {
			$this -> error('页面不存在');
		}
		$userid = $_SESSION['uid'];
		$episodeid = $this -> _post('eid');
		$iswatch = $this -> _post('iswatch');
		$where = array('userid' => $userid, 'episodeid' => $episodeid);
		 $broadcasttime = M('episode') -> where('episodeid = '.	$episodeid) -> field('broadcasttime') -> find();
		if (strtotime($broadcasttime['broadcasttime']) > time()) {
			echo 0;
		} else {
			$watched = ($iswatch + 1) % 2;
			if ( M('watch') -> where($where) -> save(array('watched' => $watched, 'score' => 0))) {
				echo 1;
			} else {
				echo 0;
			}
		}
	}

	/**
	 *  标记是否关注
	 */

	Public function follow() {
		if (!$this -> isAjax()) {
			$this -> error('页面不存在');
		}
		$userid = $_SESSION['uid'];
		$showid = $this -> _post('sid');
		$isfollow = $this -> _post('isfollow');
		$sql = 'select episodeid from episodeView where showid = ' . $showid;
		$Model = new Model();
		$episodeids = $Model -> query($sql);
		$arrlength = count($episodeids);
		for ($x = 0; $x < $arrlength; $x++) {
			$result[$x]['userid'] = $userid;
			$result[$x]['episodeid'] = $episodeids[$x]['episodeid'];
		}
		$watchdb = M("watch");		 
		if ($isfollow == isFollow($showid) && $isfollow == 0 && $watchdb -> addAll($result)) {
			echo 1;
		} elseif ($isfollow == isFollow($showid) && $isfollow == 1) {
			for ($x = 0; $x < $arrlength; $x++) {
				$result[$x] = $episodeids[$x]['episodeid'];
			}
			$where['episodeid'] = array('in', $result);
			$where['userid'] = $userid;
			if ($watchdb -> where($where) -> delete()) {
				echo 1;
			} else {
				echo 0;
			}
		} else {
			echo 0;
		}
	}

	/**
	 * 退出登录
	 */
	Public function loginOut() {
		session_start();
		session_unset();
		session_destroy();
		redirect(U('Login/index'));
	}

	/**
	 * 头像上传
	 */
	Public function uploadFace() {							
		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		$upload = $this -> _upload('Face', '80,40', '80,40');
		echo json_encode($upload);
	}

	/**
	 * 图片上传处理
	 * @param  [String] $path   [保存文件夹名称]
	 * @param  [String] $width  [缩略图宽度多个用，号分隔]
	 * @param  [String] $height [缩略图高度多个用，号分隔(要与宽度一一对应)]
	 * @return [Array]         [图片上传信息]
	 */
	Private function _upload($path, $width, $height) {
		import('ORG.Net.UploadFile');
		//引入ThinkPHP文件上传类
		$obj = new UploadFile();
		//实例化上传类
		$obj -> maxSize = C('UPLOAD_MAX_SIZE');
		//配置文件  图片最大上传大小
		$obj -> savePath = C('UPLOAD_PATH') . $path . '/';
		//图片保存路径
		$obj -> saveRule = 'uniqid';
		//保存文件名
		$obj -> uploadReplace = true;
		//覆盖同名文件
		$obj -> allowExts = C('UPLOAD_EXTS');
		//允许上传文件的后缀名
		$obj -> thumb = true;
		//生成缩略图
		$obj -> thumbMaxWidth = $width;
		//缩略图宽度
		$obj -> thumbMaxHeight = $height;
		//缩略图高度
		$obj -> thumbPrefix = 'max_,mini_';
		//缩略图前缀名
		$obj -> thumbPath = $obj -> savePath . date('Y_m') . '/';
		//缩略图保存图径
		$obj -> thumbRemoveOrigin = true;
		//删除原图
		$obj -> autoSub = true;
		//使用子目录保存文件
		$obj -> subType = 'date';
		//使用日期为子目录名称
		$obj -> dateFormat = 'Y_m';
		//使用 年_月 形式
		if (!$obj -> upload()) {
			return array('status' => 0, 'msg' => $obj -> getErrorMsg());
		} else {
			$info = $obj -> getUploadFileInfo();
			$pic = explode('/', $info[0]['savename']);
			return array('status' => 1, 'path' => array('max' => $pic[0] . '/max_' . $pic[1], 'mini' => $pic[0] . '/mini_' . $pic[1]));
		}
	}
}
?>