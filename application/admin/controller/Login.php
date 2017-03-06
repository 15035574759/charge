<?php
namespace app\admin\controller;
use	think\Controller;
use	think\Request;
use	think\Db;
use app\admin\model\LoginModel;
use think\Session;
class Login extends	Controller	
{	
	public function index()
	{
		return $this->fetch('login');
	}	

	/**
	 * 调用接口获取登录凭证（code）进而换取用户登录态信息
	 * @return [type] [description]
	 */
	public	function sendCode()				
	{	
		$code = input("param.");
		//这里要配置你的小程序appid和secret
    	$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wx0d74e7d7020142e0&secret=10b7720b73847774654a192d1e0aa9b5&js_code='.$code.'&grant_type=authorization_code';
    	$data = post_data($url);
    	//请求失败返回
    	if((isset($data['errcode']) && $data['errcode']>0) || !isset($data['session_key']) || !isset($data['openid'])){
    		$this->ajaxReturn(array('status'=>0,'msg'=>'获取信息失败'));
    	}

        //存储session数据
    	session('session_key', $data['session_key']);
    	session('openid', $data['openid']);

    	$map['openid'] = $data['openid'];
    	//判断当前系统是否存在该用户，用户自动注册
    	if(!M('public_follow')->where($map)->find()){
            //注册
    		$uid = M('user')->add(array('reg_time'=>NOW_TIME));
    		M('public_follow')->add(array('openid'=>$data['openid'], 'uid'=>$uid, 'token'=>'gh_6d3bf5d72981'));

    		session('mid', $uid);
    	}

    	$PHPSESSID = session_id();
    	$this->ajaxReturn(array('status'=>1, 'openid'=>$data['openid'] , 'PHPSESSID'=>$PHPSESSID));			
	}

	/**
	 * 用户退出
	 */
	 public	function GetQuit()				
	{		
		session(null);						
		return $this->fetch('login');
	}


}
