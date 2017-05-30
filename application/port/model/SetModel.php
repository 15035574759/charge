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
}