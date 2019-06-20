<?php
/**
 * 极客之家 高端PHP - 设置模块 Model
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\model;
use think\Model;
use think\Db;
use think\Config;
use think\Cache;

class SetModel extends Model
{
	protected $name = 'porttest';

	/**
	 * 查询用户预算是否开启
	 * @param [type] $openid [用户openid]
	 */
	public function BudgetStart($openid)
	{
		return DB::name("public_follow")->where("openid",$openid)->field("butged,butged_start")->find();
	}

	/**
	 * 开启预算
	 * @param [type] $openid [用户openid]
	 */
	public function BudgetOpen($openid)
	{
		$res = DB::name("public_follow")->where("openid",$openid)->update(['butged_start'=>1]);
		if($res == true)
		{
			return array("code"=>1,"data"=>"","msg"=>"开启成功");
		}
		else
		{
			return array("code"=>-1,"data"=>"","msg"=>"开启失败");
		}
	}

	/**
	 * 关闭预算
	 * @param [type] $openid [用户openid]
	 */
	public function BudgetClose($openid)
	{
		$res = DB::name("public_follow")->where("openid",$openid)->update(['butged_start'=>0]);
		if($res == true)
		{
			return array("code"=>1,"data"=>"","msg"=>"关闭成功");
		}
		else
		{
			return array("code"=>-1,"data"=>"","msg"=>"关闭失败");
		}
	}

	/**
	 * 修改用户预算金额
	 * @param [type] $openid [用户openid]
	 * @param [type] $butgedMoney [用户预算金额]
	 */
	public function BudgetMoneyUpdate($openid,$butgedMoney)
	{
		$res = DB::name("public_follow")->where("openid",$openid)->update(['butged'=>$butgedMoney]);
		if($res == true)
		{
			return array("code"=>1,"data"=>"","msg"=>"修改成功");
		}
		else
		{
			return array("code"=>-1,"data"=>"","msg"=>"修改失败");
		}
	}

	/**
	* 获取小程序全局唯一后台接口调用凭据（access_token）
	* @param  [post] [description]
	* @return [type] [description]
	* @author [qinlh] [WeChat QinLinHui0706]
	*/
	public function getAccessToken() {
		$AccessToken = Cache::get('AccessToken');
		if($AccessToken == false) {
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.Config('APPID').'&secret='.Config('APPSECRET');
			$ReturnJson = file_get_contents($url);
			$ReturnArray = json_decode($ReturnJson, true);
			if(isset($ReturnArray['errcode'])) {
				return array("code"=>$ReturnArray['errcode'], "msg"=>$ReturnArray['errmsg']);
			}
			Cache::set('AccessToken', $ReturnArray['access_token'], 6000); //缓存小于两个小时
			$AccessToken = $ReturnArray['access_token'];
			return ['code'=>1, 'access_token'=>$ReturnArray['access_token']];
		} else {
			return ['code'=>1, 'access_token'=>$AccessToken];
		}
	}

	/**
     * 模拟Popst提交
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public function setPostData($url, $data){
        //模拟post请求
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作

        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }
}