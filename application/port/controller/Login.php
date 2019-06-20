<?php
/**
 * 极客之家 高端PHP - 用户登录
 *
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\port\model\LoginModel;
use think\Session;
use think\Cookie;
use think\Cache;
use think\Config;
use wxBizDataCrypt\wxBizDataCrypt;
class Login extends Controller
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
    public  function sendCode()
    {
        // session('session_key',"1111");
        // Cache::set('name',"111",3600);
        // $sessionKey = Cache::get('name');
        // return  $sessionKey;
        // $PHPSESSID = session_id();
        // return $PHPSESSID;
        $code = input("param.code");
        //这里要配置你的小程序appid和secret
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.Config('APPID').'&secret='.Config('APPSECRET').'&js_code='.$code.'&grant_type=authorization_code';
        $data = file_get_contents($url);
        // p($data);die;
        $arr = json_decode($data,true);
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
        // print_r($res);die;
        //判断当前系统是否存在该用户，用户自动注册
        if(empty($res)){
            // echo 111111;die;
            //注册
            $uid = DB::name('user')->insert(array('headimgurl'=>'https://www.qinlh.com/charge/public/uploads/images/头像.png','nickname'=>'未获取','reg_time'=>time()));
            $userId = DB::name('user')->getLastInsID();

            DB::name("friend")->insert(array("uid"=>$userId,"friend_name"=>"未获取","friend_imgurl"=>"https://www.qinlh.com/charge/public/uploads/images/头像.png","start"=>1,"time"=>time()));//同时添加到好友数据表
            DB::name('public_follow')->insert(array('openid'=>$arr['openid'], 'uid'=>$userId, 'token'=>'gh_6d3bf5d72981'));
            // session('mid', $uid);
            Cache::set('mid',$userId);
        }
        else
        {
            //读取用户uid
            Cache::set('mid',$res['uid']);
        }

        $PHPSESSID = $this->getRandomString(27);
        // return $PHPSESSID;
        return json((array('status'=>1, 'openid'=>$arr['openid'] , 'PHPSESSID'=>$PHPSESSID)));
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

        $appid = Config('APPID');  //这里配置你的小程序appid
        $sessionKey = Cache::get('session_key');//获取会话秘钥

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
                return json(array('status'=>0,'msg'=>'用户ID异常'.$uid));
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
                return json(array('status'=>1,'msg'=>'用户信息修改成功'));
            }else{
                return json(array('status'=>0,'msg'=>'用户信息保存失败'));
            }

        } else {
            return json(array('status'=>0,'msg'=>'错误编号：'.$errCode));
        }
    }

    /**
    * 测试调试使用
    * @param  [post] [description]
    * @return [type] [description]
    * @author [qinlh] [WeChat QinLinHui0706]
    */
    public function test() {
        return json(array('status'=>1,'msg'=>'OK'));
    }
}
