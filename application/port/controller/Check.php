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
class Check extends	Controller	
{

	/**
	 * 查询账单分类
	 * @return [type] [description]
	 */
	public function inoutClass()
	{
		$data['ExpendData'] = DB::name('inout_class')->where("start",1)->select();
		$data['IncomeData'] = DB::name('inout_class')->where("start",2)->select();
		return $data;
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
		// p($dataArr);
		$user_id = DB::name("public_follow")->alias("f")->field("f.uid")->join("bill_user u","f.uid=u.uid")->where("f.openid",$openid)->find();
		$dataArr['user_id'] = $user_id['uid'];
		if(empty($openid)){
			return array('start'=>0,'msg'=>'获取用户openid失败');
		}
		$table = "bill_charge";
		$dataAdd = $this->filter($dataArr,$table);

		//入库操作
		$res = DB::name("charge")->insert($dataAdd);
		if($res)
		{
			return array("status"=>1,"msg"=>"添加成功");
		}
		else
		{
			return array("status"=>0,"msg"=>"添加失败");
		}
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
			return array("start"=>0,"date"=>$timeDate,"msg"=>"不能选择未来日期");
		}
		else
		{
			return array("start"=>1,"date"=>$timeDate);
		}
	}
	
	/**
	 * 字段过滤
	 * @param  [type] $dataArr 需要添加的数据
	 * @param  [type] $table   表
	 * @return [type] array    数组
	 */
	public function filter($dataArr,$table)
	{
		$res = Db::query("select COLUMN_NAME from information_schema.COLUMNS where table_name = '$table'");
		foreach ($res as $key => $value) {
			$fields[$value['COLUMN_NAME']] = $value['COLUMN_NAME'];
		}
		foreach ($dataArr as $key => $val) {
              if(!in_array($key,$fields)){
                  unset($dataArr[$key]);
              }
          }

          return $dataArr;
	}

	/**
	 * 查询当前用户所有账单
	 * @return [type] openid [用户openid]
	 */
	public function UserCheck()
	{
		$check = new CheckModel();
		$userOpenid = input("param.openid");//用户openid
		return $check->UserCheck($userOpenid);
	}

	/**
	 * 查询单挑数据
	 */
	public function CheckFind()
	{
		$a_id = input("param.a_id");//用户openid
		$data = DB::name("charge")->alias("c")->join("bill_inout_class i","c.inout_class=i.c_id")->where("a_id",$a_id)->find();
		$data['time'] = date("Y-m-d H:i",$data['time']);
		return $data;
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
			return array("start"=>1,"msg"=>"删除数据成功");
		}
		else
		{
			return array("start"=>0,"msg"=>"删除数据失败");
		}
	}

	/**
	 * 本月收入与支出数据
	 * @return [type] openid [用户openid]
	 */
	public function ThisIncomOut()
	{
		$check = new CheckModel();
		$openid = input("param.openid");//用户openid
		return $check->ThisIncomOut($openid);
	}

	
	public function BudgetMoney()
	{
		$check = new CheckModel();
		$openid = input("param.openid");//用户openid
		return $check->BudgetMoney($openid);
	}
}