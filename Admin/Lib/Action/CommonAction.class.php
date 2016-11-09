<?php
/**
 * 公共控制器
 */
Class CommonAction extends Action {

	/**
	 * 判断用户是否已登录
	 */
	Public function _initialize() {
		if (!isset($_SESSION['aid']) || !isset($_SESSION['aname'])) {
			redirect(U('Login/index'));
		}
	}

	/**
	 * 头像上传
	 */
	Public function uploadCover() {
		if (!$this -> isPost()) {
			halt('页面不存在');
		}
		if ($_POST['coverstyle'] == 'season')
			$upload = $this -> _upload('Cover', '299,157', '168,88');
		else
			$upload = $this -> _upload('Cover', '1280,128', '720,72');
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
			return array('status' => 1, 'max' => $info, 'path' => array('max' => $pic[0] . '/max_' . $pic[1], 'mini' => $pic[0] . '/mini_' . $pic[1]));
		}
	}

}
?>