<?php
/**
 * 极客之家 高端PHP - 账单模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\controller;
use	think\Controller;
use	think\Request;
use	think\Db;
use think\Session;
use think\Cache;
use app\port\model\CheckModel;
use app\port\model\SetModel;
class Check extends	Controller
{

	public $set;
	public $check;
    public function __construct()
    {
		$this->set = new SetModel();
		$this->check = new CheckModel();
	}

	/**
	 * 查询账单分类
	 * @return [type] [description]
	 */
	public function inoutClass()
	{
		$data['ExpendData'] = DB::name('inout_class')->where("start",1)->select();
		$data['IncomeData'] = DB::name('inout_class')->where("start",2)->select();
		return json($data);
	}

	/**
	 * 记账
	 * @return [type] [description]
	 */
	public function charge()
	{
		$openid = input("param.openid");
		$data = input("param.formData");
		$dataArr = json_decode($data,true);
		$dataArr['time'] = strtotime($dataArr['time']);
		$res = $this->check->setCharge($dataArr, $openid);
		return json($res);
	}

	/**
	 * 获取当前时间
	 */
	public function GetTime()
	{
		return date("Y-m-d");
	}

	/**
	 * 对时间进行格式化
	 */
	public function TimeJson()
	{
		$time = time();//当天时间
		$timeDate = date("Y-m-d");
		$getdate = strtotime(input("param.getdate"));//选择时间
		if($getdate > $time)
		{
			return json(array("start"=>0,"date"=>$timeDate,"msg"=>"不能选择未来日期"));
		}
		else
		{
			return json(array("start"=>1,"date"=>$timeDate));
		}
	}

	/**
	 * 查询当前用户所有账单
	 * @return [type] openid [用户openid]
	 */
	public function UserCheck()
	{
		$userOpenid = input("param.openid");//用户openid
		if(empty($userOpenid))
		{
				return json(['code'=>-1,'msg'=>'参数错误']);
		}
		$lastid = input("param.lastid");//分页ID
		$limit = input("param.limit");//分页每页显示数据
		return json($this->check->UserCheck($userOpenid,$lastid,$limit));
	}

	/**
	 * 查询单挑数据
	 */
	public function CheckFind()
	{
		$a_id = input("param.a_id");//用户openid
		$data = DB::name("charge")->alias("c")->join("bill_inout_class i","c.inout_class=i.c_id")->where("a_id",$a_id)->find();
		$data['time'] = date("Y-m-d H:i",$data['time']);
		return json($data);
	}

	/**
	 * 删除账单
	 * @return [type] a_id [数据ID]
	 */
	public function CheckDel()
	{
		$a_id = input("param.a_id");//数据ID
		$res = DB::name("charge")->where("a_id",$a_id)->delete();
		if($res)
		{
			return json(array("start"=>1,"msg"=>"删除数据成功"));
		}
		else
		{
			return json(array("start"=>0,"msg"=>"删除数据失败"));
		}
	}

	/**
	 * 本月收入与支出数据
	 * @return [type] openid [用户openid]
	 */
	public function ThisIncomOut()
	{
		$openid = input("param.openid");//用户openid
		$time = input("param.time") ? input("param.time") : date('Y年m月');
		return json($this->check->ThisIncomOut($openid, $time));
	}


	/**
	* 查询预算是否开启
	* @param  [post] [description]
	* @return [type] [description]
	* @author [qinlh] [WeChat QinLinHui0706]
	*/
	public function BudgetMoney()
	{
		$openid = input("param.openid");//用户openid
		return json($this->check->BudgetMoney($openid));
	}

	/**
	* 查询本年度每个月的数据
	* @param  [post] [description]
	* @return [type] [description]
	* @author [qinlh] [WeChat QinLinHui0706]
	*/
	public function ShowYearMoneyData() {
		$openid = input("param.openid");//用户openid
		return json_encode($this->check->ShowYearMoneyData($openid));
	}
}
