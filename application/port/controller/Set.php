<?php
/**
 * 极客之家 高端PHP - 圈子账本 设置模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2017-4-3 16:36:52
 */
namespace app\port\controller;
use	think\Controller;
use	think\Request;
use	think\Db;
use think\Session;
use think\Cache;
use app\port\model\SetModel;
class Set extends Controller	
{	
	/**
	 * 查询用户预算是否开启
	 */
	public function BudgetStart()
	{
		$set = new SetModel();
		$userOpenid = input("param.openid");//用户openid
		return $set->BudgetStart($userOpenid);
	}

	/**
	 * 开启预算
	 */
	public function BudgetOpen()
	{
		$set = new SetModel();
		$userOpenid = input("param.openid");//用户openid
		return $set->BudgetOpen($userOpenid);
	}

	/**
	 * 关闭预算
	 */
	public function BudgetClose()
	{
		$set = new SetModel();
		$userOpenid = input("param.openid");//用户openid
		return $set->BudgetClose($userOpenid);
	}

	/**
	 * 修改用户预算金额
	 * @param [type] openid [用户openid]
	 * @param [type] butgedMoney [用户预算金额]
	 */
	public function BudgetMoneyUpdate()
	{
		$set = new SetModel();
		$userOpenid = input("param.openid");//用户openid
		$butgedMoney = input("param.butgedMoney");//用户预算金额
		return $set->BudgetMoneyUpdate($userOpenid,$butgedMoney);
	}
}