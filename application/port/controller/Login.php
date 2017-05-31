<?php
/**
 * 极客之家 高端PHP - 用户登录
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\controller;
use	think\Controller;
use	think\Request;
use	think\Db;
use app\port\model\LoginModel;
use think\Session;
use think\Cookie;
use think\Cache;
use wxBizDataCrypt\wxBizDataCrypt;
class Login extends	Controller	
{	
	public function index()
	{
        // session('session_key','555');
        // $sessionKey = session('session_key');
        // var_dump($sessionKey);die;
		// $time = time();
		return $this->fetch('login');
	}

	/**
	 * 调用接口获取登录凭证（code）进而换取用户登录态信息
	 * @return [type] [description]
	 */
	public	function sendCode()				
	{		
        // session('session_key',"1111");
        // Cache::set('name',"111",3600);
        // $sessionKey = Cache::get('name');
        // return  $sessionKey;
        // $PHPSESSID = session_id();
        // return $PHPSESSID;
		$code = input("param.code");
		//这里要配置你的小程序appid和secret
    	$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wx14fd8a03c8bf2694&secret=eb7992eabb097c63f14e566cdea3837f&js_code='.$code.'&grant_type=authorization_code';
    	$data = file_get_contents($url);
    	$arr = json_decode($data,true);
        // print_r($arr);die;
    	//请求失败返回
    	if(isset($arr['errcode']) && (!isset($arr['openid']) || (!isset($arr['session_key'])))){
    		return (array('code'=>-1,'msg'=>'获取信息失败'));
    	}

        //存储session数据
    	// session('session_key',$arr['session_key']);
        Cache::set('session_key',$arr['session_key']);
        Cache::set('openid', $arr['openid']);
    	// session('openid', $arr['openid']);
        // return $arr['session_key'];
    	$map['openid'] = $arr['openid'];
        $res = DB::name('public_follow')->where($map)->find();
    	//判断当前系统是否存在该用户，用户自动注册
    	if(empty($res)){
            // echo 111;die;
            //注册
    		$uid = DB::name('user')->insert(array('reg_time'=>time()));
    		$userId = DB::name('user')->getLastInsID();

            DB::name("friend")->insert(array("uid"=>$userId,"start"=>1,"time"=>time()));//同时添加到好友数据表
    		DB::name('public_follow')->insert(array('openid'=>$arr['openid'], 'uid'=>$userId, 'token'=>'gh_6d3bf5d72981'));
    		// session('mid', $uid);
            Cache::set('mid',$userId);
    	}

    	$PHPSESSID = $this->getRandomString(27);
        // return $PHPSESSID;
    	return (array('status'=>1, 'openid'=>$arr['openid'] , 'PHPSESSID'=>$PHPSESSID));			
	}

    /**
     * 生成随机session_id
     * @param  [type] $len   [description]
     * @param  [type] $chars [description]
     * @return [type]        [description]
     */
    function getRandomString($len, $chars=null)
    {
        if (is_null($chars)){
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }  
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
            $str .= $chars[mt_rand(0, $lc)];  
        }
        return $str;
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
    		return array('status'=>0,'msg'=>'传递信息不全');
    	}

        $appid = 'wx14fd8a03c8bf2694';  //这里配置你的小程序appid
        $sessionKey = Cache::get('session_key');

        $result = import("wxBizDataCrypt",EXTEND_PATH.'wxBizDataCrypt'); 
        $pc = new \wxBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        // return $errCode;
        if ($errCode == 0) {
            $data = json_decode($data, true);
            // return $data;
            // session('myinfo', $data);
            Cache::set('myinfo',$data);

            $save = array(
                    'nickname' => $data['nickName'],
                    'sex' => $data['gender'],
                    'city' => $data['city'],
                    'province' => $data['province'],
                    'country' => $data['country'],
                    'headimgurl' => $data['avatarUrl']);

            $uid =  Cache::get('mid');
            if(empty($uid)){
            	return array('status'=>0,'msg'=>'用户ID异常'.$uid);
            }
            $res = DB::name('user')->where("uid",$uid)->update($save);
            $friend_id = DB::name('friend')->where("uid",$uid)->find();//修改当前用户好友表数据  为注册用户
            if(isset($friend_id))
            {
                DB::name("friend")->where("uid",$uid)->update(array("start"=>1,"friend_name"=>$save['nickname'],"friend_imgurl"=>$save['headimgurl'],"time"=>time()));
            }
            else
            {
                DB::name("friend")->insert(array("uid"=>$uid,"start"=>1,"friend_name"=>$save['nickname'],"friend_imgurl"=>$save['headimgurl'],"time"=>time()));
            }

            if($res!==false){
            	return array('status'=>1,'msg'=>'用户信息修改成功');
            }else{
            	return array('status'=>0,'msg'=>'用户信息保存失败');
            }

        } else {
            return array('status'=>0,'msg'=>'错误编号：'.$errCode);
        }
    }

    /**
     * 模拟Popst提交
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
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
