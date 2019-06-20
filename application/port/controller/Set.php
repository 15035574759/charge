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
use think\Config;
use app\port\model\SetModel;
use app\port\model\UserModel;
class Set extends Controller
{
    public $set;
    public $user;
    public function __construct()
    {
        $this->set = new SetModel();
        $this->user = new UserModel();
	}
	
	/**
	 * 查询用户预算是否开启
	 */
	public function BudgetStart()
	{
		$userOpenid = input("param.openid");//用户openid
		return json($this->set->BudgetStart($userOpenid));
	}

	/**
	 * 开启预算
	 */
	public function BudgetOpen()
	{
		$userOpenid = input("param.openid");//用户openid
		return json($this->set->BudgetOpen($userOpenid));
	}

	/**
	 * 关闭预算
	 */
	public function BudgetClose()
	{
		$userOpenid = input("param.openid");//用户openid
		return json($this->set->BudgetClose($userOpenid));
	}

	/**
	 * 修改用户预算金额
	 * @param [type] openid [用户openid]
	 * @param [type] butgedMoney [用户预算金额]
	 */
	public function BudgetMoneyUpdate()
	{
		$userOpenid = input("param.openid");//用户openid
		$butgedMoney = input("param.butgedMoney");//用户预算金额
		return json($this->set->BudgetMoneyUpdate($userOpenid,$butgedMoney));
	}

	public function test() {
		return json(array('status'=>1,'msg'=>'OK'));
	} 
}
