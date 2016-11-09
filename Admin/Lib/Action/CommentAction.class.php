<?php
/**
 * 评论管理控制器
 */
Class CommentAction extends CommonAction {
	/**
	 *  评论列表
	 */
	Public function index() {
		import('ORG.Util.Page');
		$count = M('comment') -> count();
		$page = new Page($count, 5);
		$limit = $page -> firstRow . ',' . $page -> listRows;
		$Model = new Model();
		$sql = 'select * from commentView limit '.$limit;
		$this -> comments = $Model -> query($sql);
		$this -> page = $page -> show();
		//p($this -> comments);
		$this -> display();
	}

	/**
	 * 删除评论
	 */
	Public function removeComment() {
		$data = array('commentid' => $this -> _get('commentid', 'intval'), 'remove' => $this -> _get('remove', 'intval'));
		$msg = $data['remove'] ? '删除' : '解除删除';
		if ( M('comment') -> save($data)) {
			$this -> success($msg . '成功', $_SERVER['HTTP_REFERER']);
		} else {
			$this -> error($msg . '失败，请重试...');
		}
	}

	/**
	 * 评论检索
	 */
	Public function seachComment() {
		if (isset($_GET['sech']) && isset($_GET['type'])) {
			$Model = new Model();
			$type = $_GET['type'];
			$where = '';
			switch ($type) {
				case '1' :
					$where = 'commentid = ' . $this -> _get('sech', 'intval');
					break;
				case '2' :
					$where = 'userid = ' . $this -> _get('sech', 'intval');
					break;
				case '3' :
					$where = 'episodeid = ' . $this -> _get('sech', 'intval');
					break;
				case '4' :
					$where = 'comment like %' . $this -> _get('sech', 'intval') . '%';
					break;
				default :
					break;
			}
			$sql = 'select * from commentView where ' . $where;
			$comments = $Model -> query($sql);
			$this -> comments = $comments ? $comments : false;
		}
		$this -> display();
	}

}
?>