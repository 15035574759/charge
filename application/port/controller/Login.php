<?php
namespace app\port\controller;
use	think\Controller;
use	think\Request;
use	think\Db;
use app\port\model\LoginModel;
use think\Session;
use wxBizDataCrypt\wxBizDataCrypt;
class Login extends	Controller	
{	
	public function index()
	{
		// $time = time();
		return $this->fetch('login');
	}

	/**
	 * 调用接口获取登录凭证（code）进而换取用户登录态信息
	 * @return [type] [description]
	 */
	public	function sendCode()				
	{			
		$code = input("param.code");
		//这里要配置你的小程序appid和secret
    	$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wx0d74e7d7020142e0&secret=10b7720b73847774654a192d1e0aa9b5&js_code='.$code.'&grant_type=authorization_code';
    	$data = $this->post_data($url);
    	$arr = json_decode($data,true);
    	//请求失败返回
    	if(isset($arr['errcode']) && (!isset($arr['openid']) || (!isset($arr['session_key'])))){
    		return (array('status'=>0,'msg'=>'获取信息失败'));
    	}

        //存储session数据
    	session('session_key', $arr['session_key']);
    	session('openid', $arr['openid']);

    	$map['openid'] = $arr['openid'];
    	//判断当前系统是否存在该用户，用户自动注册
    	if(!DB::name('public_follow')->where($map)->find()){
            //注册
    		$uid = DB::name('user')->insert(array('reg_time'=>time()));
    		$userId = DB::name('user')->getLastInsID();
    		DB::name('public_follow')->insert(array('openid'=>$arr['openid'], 'uid'=>$userId, 'token'=>'gh_6d3bf5d72981'));

    		session('mid', $uid);
    	}

    	$PHPSESSID = session_id();
    	return (array('status'=>1, 'openid'=>$arr['openid'] , 'PHPSESSID'=>$PHPSESSID));			
	}

	 /**
	  * 修改用户数据 保存用户数据
	  * @return [type] [description]
	  */
    function saveUserInfo(){
    	$encryptedData = input('param.encryptedData');
    	$iv = input('param.iv');
        // return $iv;
    	if(empty($encryptedData) || empty($iv)){
    		$this->ajaxReturn(array('status'=>0,'msg'=>'传递信息不全'));
    	}

    	// include_once "aes/wxBizDataCrypt.php";


        $appid = 'wx0d74e7d7020142e0';  //这里配置你的小程序appid
        $sessionKey = session('session_key');
        return $sessionKey;
        $result = import("wxBizDataCrypt",EXTEND_PATH.'wxBizDataCrypt'); 
        $pc = new \wxBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        return $errCode;
        if ($errCode == 0) {
            $data = json_decode($data, true);
            session('myinfo', $data);

            $save['nickname'] = $data['nickName'];
            $save['sex'] = $data['gender'];
            $save['city'] = $data['city'];
            $save['province'] = $data['province'];
            $save['country'] = $data['country'];
            $save['headimgurl'] = $data['avatarUrl'];
            !empty($data['unionId']) && $save['unionId'] = $data['unionId'];

            $uid = session('mid');
            if(empty($uid)){
            	return (array('status'=>0,'msg'=>'用户ID异常'.$uid));
            }
            return $save;
            $res = D('Common/User')->updateInfo($uid, $save);
            if($res!==false){
            	return (array('status'=>1));
            }else{
            	return (array('status'=>0,'msg'=>'用户信息保存失败'));
            }

        } else {
            return (array('status'=>0,'msg'=>'错误编号：'.$errCode));
        }
    }


	function post_data($url){
		//模拟post请求 
		$curl = curl_init(); // 启动一个CURL会话
	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	    //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
	    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	    // curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	    $tmpInfo = curl_exec($curl); // 执行操作
	    
	    curl_close($curl); // 关闭CURL会话
	    return $tmpInfo; // 返回数据
	}
}
