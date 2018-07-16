<?php
/**
 * 极客之家 高端PHP - 好友模块 Model
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\model;
use think\Model;
use think\Db;

class FriendModel extends Model
{
	protected $name = '';


	/**
	 * 添加好友
	 * @return [name] [用户名]
	 * @return [phone] [电话]
	 * @return [openid] [用户openid]
	 */
	public function friend_add($name,$phone,$openid)
	{
		$uid = $this->UserOpenid($openid);
		if(false === $uid)
		{
			return array("code"=>-1,"data"=>"","msg"=>"获取用户信息失败");
		}

			//判断手机号是否注册 首先获取单钱用户所有好友id  根据id查询好友手机号是否添加
			$FriendPhoneId = DB::name("user_friend")->where("u_id",$uid)->select();

			if($FriendPhoneId != array())
			{
				foreach ($FriendPhoneId as $key => $val) {//首先获取单钱用户所有好友id
					$num[] = $val['f_id'];
					$FriendPhoneId = implode(",",$num);
				}

				// echo $FriendPhoneId;die;
				$FriendPhoneAdd = DB::name("friend")->where("f_id","in",$FriendPhoneId)->where("friend_phone",$phone)->find();

				if($FriendPhoneAdd)
				{
					return array("code"=>3,"data"=>"","msg"=>"该手机号已经添加");
				}
			}

				$data = array(
						"friend_name"=>$name,
						"friend_phone"=>$phone,
						"friend_imgurl"=>"https://h5php.xingyuanauto.com/charge/public/uploads/images/img/tx".rand(1,10).".jpg",
						"time"=>time(),
						"uid"=> 0,
						"start"=>"0"
					);
				//开始入库操作
				DB::name("friend")->insert($data);
				$AddId = Db::name('friend')->getLastInsID();
				$res = DB::name("user_friend")->insert(["u_id"=>$uid,"f_id"=>$AddId]);
			if($res)
			{
				return array("code"=>1,"data"=>"","msg"=>"添加成功");
			}
			else
			{
				return array("code"=>-2,"data"=>"","msg"=>"添加失败");
			}
	}

	/**
	 * 查询当前用户id
	 * @param [type] $openid [用户openid]
	 */
	public function UserOpenid($openid)
	{
		$data =  DB::name("public_follow")->where("openid",$openid)->field("uid")->find();
		return $data['uid'];
	}

	/**
	 * 我的好友
	 * @return [openid] [用户openid]
	 */
	public function MyFriend($openid)
	{
		$uid = $this->UserOpenid($openid);
		//查询我的好友
		$data = DB::name("user_friend")->alias("uf")->join("bill_friend bf","uf.f_id=bf.f_id")->order("bf.f_id desc")->where("u_id",$uid)->select();
		// print_r($data);die;
		foreach ($data as $key => $val) {

			$data[$key]['friend_name'] = mb_strlen($val['friend_name'], 'utf-8') > 5 ? mb_substr($val['friend_name'], 0, 5, 'utf-8').'....' : $val['friend_name'];

			//查询好友待支付金额
			$payMoney = DB::query("SELECT sum(`sum_money`) AS `sum_money` FROM `bill_circle_friend_assoct` WHERE friend_id = ".$val['f_id']." AND `pay_start` = 0");
			foreach ($payMoney as $kk => $vv) {
				$data[$key]['pay_money'] = $vv['sum_money'];
			}

			//查询好友累计消费金额
			$totalMoney = DB::query("SELECT sum(`sum_money`) AS `total_money` FROM `bill_circle_friend_assoct` WHERE `friend_id` = ".$val['f_id']." AND `pay_start` = 1");
			foreach ($totalMoney as $kkk => $vvv) {
				$data[$key]['total_money'] = $vvv['total_money'];
			}
		}

		// print_r($data);die;
		return $data;
	}
}
