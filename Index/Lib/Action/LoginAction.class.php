<?php
require_once 'saetv2.ex.class.php';

/**
 * 注册与登录控制器
 */
Class LoginAction extends Action
{
    /**
     * 登录页面
     */
    Public function index()
    {
        // phpinfo();

        $this->display('index3');
    }

    /**
     * 登录表单处理
     */
    Public function login()
    {
        if (!$this->isPost()) {
            halt('页面不存在');
        }
        //提取表单内容
        $account = $this->_post('account');
        $password = $this->_post('pwd', 'md5');
        $where = array('account' => $account);
        $user = M('user')->where($where)->find();
        if (!$user || $user['password'] != $password) {
            $this->error('用户名或者密码不正确');
        }
        if ($user['lock']) {
            $this->error('用户被锁定');
        }
        //处理下一次自动登录
        if (isset($_POST['auto'])) {
            $account = $user['account'];
            $ip = get_client_ip();
            $value = $account . '|' . $ip;
            $value = encryption($value);
            @setcookie('auto', $value, C('AUTO_LOGIN_TIME'), '/');
        }
        //登录成功写入SESSION并且跳转到首页
        session('uid', $user['userid']);
        session('username', $user['username']);
        session('facemini', $user['facemini']);
        header('Content-Type:text/html;Charset=UTF-8');
        redirect(__APP__, 1, '登录成功，正在为您跳转...');
    }

    /**
     * 注册页面
     */
    Public function register()
    {
        $this->display();
    }

    /**
     * 注册表单处理
     */
    Public function runRegis()
    {
        if (!$this->isPost()) {
            halt('页面不存在');
        }
        if ($_SESSION['verify'] != md5($this->_POST('verify'))) {
            $this->error('验证码错误');
        }
        if ($_POST['pwd'] != $this->_POST('pwded')) {
            $this->error('两次密码不一致');
        }
        //提取POST数据
        $data = array(
            'account' => $this->_post('account'),
            'password' => $this->_post('pwd', 'md5'),
            'registime' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
            'userinfo' => array('username' => $this->_post('uname'))
        );
        $id = D('user')->add($data);
        if ($id) {
            //插入数据成功后把用户ID写SESSION
            session('uid', $id);

            //跳转至首页
            header('Content-Type:text/html;Charset=UTF-8');
            redirect(__APP__, 3, '注册成功，正在为您跳转...');
        } else {
            $this->error('注册失败，请重试...');
        }
    }

    /**
     * 获取验证码
     */
    Public function verify()
    {
        ob_clean();
        import('ORG.Util.Image');
        Image::buildImageVerify(4, 1, 'png');
    }

    /**
     * 异步验证账号是否已存在
     */
    Public function checkAccount()
    {
        if (!$this->isAjax()) {
            halt('页面不存在');
        }
        $account = $this->_post('account');
        $where = array('account' => $account);

        if (M('user')->where($where)->getField('userid')) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * 异步验证昵称是否已存在
     */
    Public function checkUname()
    {
        if (!$this->isAjax()) {
            halt('页面不存在');
        }
        $username = $this->_post('uname');
        $where = array('username' => $username);
        if (M('user')->where($where)->getField('userid')) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * 异步验证验证码
     */
    Public function checkVerify()
    {
        if (!$this->isAjax()) {
            halt('页面不存在');
        }
        $verify = $this->_post('verify');
        if ($_SESSION['verify'] != md5($verify)) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    Public function wblogin()
    {
        $WB_KEY = '1342846886';
        $WB_SEC = '3fd4bc2c690818b6ffda8f262bda1c4d';
        $o = new SaeTOAuthv2($WB_KEY, $WB_SEC);
        $WB_CALLBACK_URL = 'http://www.superopfan.cn/index.php/Login/callback';
        $oauth = $o->getAuthorizeURL($WB_CALLBACK_URL);
        header('Location:' . $oauth);
    }

    Public function callback()
    {
        $WB_AKEY = '1342846886';
        $WB_SKEY = '3fd4bc2c690818b6ffda8f262bda1c4d';
// $code=$_GET['code'];
// $keys['code']=$code;$WB_CALLBACK_URL = 'http://www.superopfan.cn/index.php/Login/callback';
//  $keys['redirect_uri']=$callback;
//  $o=new SaeTOAuthv2($WB_KEY,$WB_SEC);
//  	$auth= $o->getAccessToken( $keys);
//  	var_dump($auth);
        $WB_CALLBACK_URL = 'http://www.superopfan.cn/index.php/Login/callback';
        $o = new SaeTOAuthV2($WB_AKEY, $WB_SKEY);
        if (isset($_REQUEST['code'])) {
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = $WB_CALLBACK_URL;
            try {
                $token = $o->getAccessToken($keys);
            } catch (OAuthException $e) {
            }
        }
        if ($token) {
            $_SESSION['token'] = $token;
            setcookie('weibojs_' . $o->client_id, http_build_query($token));
            ?>授权完成,<a href="http://www.superopfan.cn/index.php/Login/info">进入你的微博列表页面</a><br/><?php
        } else {
            ?>授权失败。<?php
        }
    }

    Public function info()
    {
        $WB_AKEY = '1342846886';
        $WB_SKEY = '3fd4bc2c690818b6ffda8f262bda1c4d';
        $c = new SaeTClientV2($WB_AKEY, $WB_SKEY, $_SESSION['token']['access_token']);
        $ms = $c->home_timeline(); // done
        $uid_get = $c->get_uid();
        $uid = $uid_get['uid'];
        $user_message = $c->show_user_by_id($uid);//根据ID获取用户等基本信息
        p($user_message);
    }
}

?>