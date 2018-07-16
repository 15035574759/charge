<?php
/**
 * 极客之家 高端PHP - 圈子账本 好友模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2017-4-3 16:36:52
 */
namespace app\port\controller;
use	think\Controller;
use	think\Request;
use	think\Db;
use app\port\model\FriendModel;
use think\Session;
use think\Cache;
class Friend extends Controller
{
	/**
	 * 添加好友
	 * @return [name] [用户名]
	 * @return [phone] [电话]
	 * @return [openid] [用户openid]
	 */
	public function friend_add()
	{
		$friend = new FriendModel();
		$openid = input("param.openid");
		$name = input("param.name");
		$phone = input("param.phone");
		return json($friend->friend_add($name,$phone,$openid));
	}

	/**
	 * 我的好友
	 * @return [openid] [用户openid]
	 */
	public function MyFriend()
	{
		$friend = new FriendModel();
		$openid = input("param.openid");
		return json($friend->MyFriend($openid));
	}

}
