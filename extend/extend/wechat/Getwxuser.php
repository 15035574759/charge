<?php
/**
 * 极客之家 高端PHP - 微信网页授权
 * @copyright  Copyright (c) 2019 QIN TEAM (http://www.qinlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Aes.php 2019-5-7 16:36:52
 */
namespace wechat; 
use think\Config;

class Getwxuser {   

    public $appid; 
    public $appsecret;  
    public function __construct(){ 
        $this->appid = config('appID'); 
        $this->appsecret = config('appsecret');
    }

   /**
    * 1、获取微信用户信息，判断有没有code，有使用code换取access_token，没有去获取code。
    * @return array 微信用户信息数组
    */
    public function get_user_all(){
        if (!isset($_GET['code'])){//没有code，去微信接口获取code码
            $callback = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];//微信服务器回调url，这里是本页url
            $this->get_code($callback);
        } else {//获取code后跳转回来到这里了
            $code = $_GET['code'];
            $data = $this->get_access_token($code);//获取网页授权access_token和用户openid
            $data_all = $this->get_user_info($data['access_token'],$data['openid']);//获取微信用户信息      
            return $data_all;
        }
    }

   /**
    * 2、用户授权并获取code
    * @param string $callback 微信服务器回调链接url
    */
    public function get_code($callback){
        $appid = $this->appid;
        $scope = 'snsapi_userinfo'; //静默授权snsapi_base
        $state = md5(uniqid(rand(), TRUE));//唯一ID标识符绝对不会重复
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($callback) .  '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
        header("Location:$url");
    } 

   /**
    * 3、使用code换取access_token
    * @param string 用于换取access_token的code，微信提供
    * @return array access_token和用户openid数组
    */
    public function get_access_token($code){
        $appid = $this->appid;
        $appsecret = $this->appsecret;    
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
        $user = json_decode(file_get_contents($url));
        if (isset($user->errcode)) {
            echo 'error:' . $user->errcode.'<hr>msg  :' . $user->errmsg;exit;
        }
        $data = json_decode(json_encode($user),true);//返回的json数组转换成array数组
        return $data;
    }

  /**
    * 4、使用access_token获取用户信息
    * @param string access_token
    * @param string 用户的openid
    * @return array 用户信息数组
    */
    public function get_user_info($access_token,$openid){
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $user = json_decode(file_get_contents($url));
        if (isset($user->errcode)) {
            echo 'error:' . $user->errcode.'<hr>msg  :' . $user->errmsg;exit;
        }
        $data = json_decode(json_encode($user),true);//返回的json数组转换成array数组
        return $data;
    }  
}
?>