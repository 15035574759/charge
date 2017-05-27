<?php
/**
 * 极客之家 高端PHP - 圈子账本
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2017-4-3 16:36:52
 */
namespace app\port\controller;
use	think\Controller;
use	think\Request;
use	think\Db;
use app\port\model\CircleModel;
use think\Session;
use think\Cache;
class Circle extends Controller	
{	
	/**
	 * 查询圈子分类名称
	 * @return [type] [description]
	 */
	public function circleAdd_class()
	{
		$circle = new CircleModel();
		return $circle->circleAdd_class();
	}

	/**
	 * 查询当前用户下所有圈子数据
	 * @return [type] [description]
	 */
	public function circleUser()
	{
		$openid = input("param.openid");
		$circle = new CircleModel();
		return $circle->circleUser($openid);
	}

	/**
	 * 查询当前用户下所有圈子数据
	 * @return [type] [description]
	 */
	public function circleDel()
	{
		$cir_id = input("param.cir_id");
		$circle = new CircleModel();
		return $circle->circleDel($cir_id);
	}

	/**
	 * 查询当前圈子对应标题
	 * @return [type] [description]
	 */
	public function CircleName()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		return $circle->CircleName($cir_id,$openid);
	}

	/**
	 * 添加圈子账本
	 * @param  [type] $openid [用户openid]
	 * @return [type]         [description]
	 */
	public function GetAddCircle()
	{
		$circle = new CircleModel();
		$openid = input("param.openid");
		// echo $openid;die;
		$circle_name = input("param.circle");
		$c_id = input("param.c_id");
		return $circle->GetAddCircle($openid,$circle_name,$c_id);
	}

	/**
	 * 查询当前圈子下好友列表
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]
	 */
	public function CircleFriend()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		return $circle->CircleFriend($cir_id,$openid);
	}

	/**
	 * 获取当前圈子全部好友 包括好友消费信息
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid] 
	 */
	public function CircleFriendAll()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		return $circle->CircleFriendAll($cir_id,$openid);
	}

	/**
	 * 获取当前圈子下我的消费金额 以及圈子下账单列表
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid] 
	 */
	public function CircleMyconsume()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		return $circle->CircleMyconsume($cir_id,$openid);
	}

	/**
	 * 获取当前用户所有好友
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid] 
	 */
	public function UserMyFriend()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		return $circle->UserMyFriend($cir_id,$openid);
	}

	/**
	 * 获取当前圈子下所有好友
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid] 
	 */
	// public function CircleMyFriend()
	// {
	// 	$circle = new CircleModel();
	// 	$cir_id = input("param.cir_id");
	// 	$openid = input("param.openid");
	// 	return $circle->CircleMyFriend($cir_id,$openid);
	// }

	/**
	 * 给当前圈子添加好友
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid] 
	 * @param [type] $f_id   [好友id] 
	 */
	function AddFriend()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		$f_id = input("param.f_id");
		$res = $circle->AddFriend($cir_id,$openid,$f_id);
		if($res)
		{
			return array("msg"=>"1","data"=>"添加成功");
		}
		else
		{
			return array("msg"=>"-1","data"=>"添加失败");
		}
	}

	/**
	 * 查询记账图片
	 */
	public function ChargeGetimg()
	{
		return DB::name("inout_class")->where("start",4)->select();
	}

	/**
	 * 选择付款人
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]  
	 */
	public function GetPayer()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		return $circle->GetPayer($cir_id,$openid);
	}

	/**
	 * 根据选择付款人 展示付款人信息
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]  
	 * @param [type] $f_id [用户选择的付款人id]  
	 */
	public function GetShowpayer()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		$f_id = input("param.f_id");
		return $circle->GetShowpayer($cir_id,$openid,$f_id);
	}

	/**
	 * 如果未选择付款人  默认选择管理员
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]  
	 */
	public function GetShowpayerEmpty()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		return $circle->GetShowpayerEmpty($cir_id,$openid);
	} 

	/**
	 * 开始账单  记账
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $c_id [记账分类]  
	 * @param [type] $f_id [付款人]  
	 * @param [type] $PayMoney [总支出]  
	 * @param [type] $remark [备注]  
	 */
	public function AddCircle()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");//圈子id
		$openid = input("param.openid");//圈子id
		$c_id = input("param.c_id");//记账分类
		$f_id = input("param.f_id");//付款人
		$uid = $circle->UserOpenid($openid);
		$f_id = $f_id == 'undefined' ? $uid : $f_id;
		$PaySumMoney = input("param.PaySumMoney");//总支出
		$remark = input("param.remark");//备注
		$friend_id = input("param.friend_id");//好友ID
		$sum_money = input("param.sum_money");//參與人消費今個
		$data = array(
			"cir_id"=>$cir_id,
			"class_id"=>$c_id,
			"total"=>$PaySumMoney,
			"remark"=>$remark,
			"payer"=>$f_id,
			"start"=>0,
			"circle_time"=>time()
			);
		$data1 = array(
				"cir_id"=>$cir_id,
				"friend_id"=>$friend_id,	
				"sum_money"=>$sum_money,	
			);
		return $circle->AddCircle($data,$data1,$f_id,$PaySumMoney);
	}

	/**
	 * 获取当前圈子账单列表
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]  
	 */
	public function CircleList()
	{
		$circle = new CircleModel();
		$cir_id = input("param.cir_id");
		$openid = input("param.openid");
		return $circle->CircleList($cir_id,$openid);
	}

	/**
	 * 获取当前圈子账单查询明细数据
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]  
	 */
	public function CircleDetails()
	{
		$circle = new CircleModel();
		$cbl_id = input("param.cbl_id");
		$openid = input("param.openid");
		return $circle->CircleDetails($cbl_id);
	} 

	/**
	 * 修改用户账单支付状态
	 * @param [type] $cbl_id [账单ID]
	 * @param [type] $f_id [参与人ID]  
	 */
	public function UpdateCirclePayment()
	{
		$circle = new CircleModel();
		$cbl_id = input("param.cbl_id");
		$f_id = input("param.f_id");
		return $circle->UpdateCirclePayment($cbl_id,$f_id);
	}

	/**
	 * 删除用户账单数据
	 * @param [type] $cbl_id [账单ID]
	 * @param [type] $f_id [参与人ID]  
	 */
	public function DeleteCirclePayment()
	{
		$circle = new CircleModel();
		$cbl_id = input("param.cbl_id");
		return $circle->DeleteCirclePayment($cbl_id);
	}
}