<?php
/**
 * 极客之家 高端PHP - 用户登录
 *
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\controller;
use	think\Controller;
use	think\Request;
use	think\Db;
use app\port\model\LoginModel;
use think\Session;
use think\Cache;
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
	 */
	public function UserCheck()
	{

		$userOpenid = input("param.openid");//用户openid
		$uid = $this->UserId($userOpenid);
		$start = date('Y-m-01 00:00:00');
		$end = date('Y-m-d H:i:s');


		//本月总金额
		// $MonthAmount = DB::query("SELECT sum(`money`) as `money`,`time`,`user_id` FROM `bill_charge` WHERE `user_id` = '$uid' AND `time` >= unix_timestamp('".$start."') AND `time` <= unix_timestamp('$end')");
		// $MonthAmount = $MonthAmount[0]['money'];

		//本月收入
		$MonthIncome = DB::query("SELECT sum(`money`) as `money`,`time` FROM `bill_charge` WHERE `user_id` = '$uid' AND `inout_start`=1 AND `time` >= unix_timestamp('".$start."') AND `time` <= unix_timestamp('$end')");
		$MonthIncome = $MonthIncome[0]['money'];
		if($MonthIncome == null){$MonthIncome = 0;}

		//本月支出
		$MonthExpend = DB::query("SELECT sum(`money`) as `money`,`time` FROM `bill_charge` WHERE `user_id` = '$uid' AND `inout_start`=2 AND `time` >= unix_timestamp('".$start."') AND `time` <= unix_timestamp('$end')");
		if(empty($MonthExpend) || !isset($MonthExpend)){$MonthExpend = 0;}
		$MonthExpend = $MonthExpend[0]['money'];
		if($MonthExpend == null){$MonthExpend = 0;}

		//本月预算余额
		$MonthBalance = $MonthIncome - $MonthExpend;
		// echo $MonthBalance;die;
		
		$lastid = input("param.lastid");//分页数据ID
		if($lastid == 1){exit;}//没有数据了
		$where = "a_id > 0";
		if($lastid > 0)
		{
			$where .= " and a_id < $lastid";
		}

		$limit = input("param.limit");//分页每页显示数据
		// echo $lastid;die;
		//支出与收入数据
		$TimeDataArr = DB::query("select time,a_id from bill_charge where $where group by time order by a_id desc LIMIT $limit");
		if(!isset($TimeDataArr))
		{
			return array("start"=>1,"data"=>$TimeDataArr,"MonthBalance"=>$MonthBalance,"MonthIncome"=>$MonthIncome,"MonthExpend"=>$MonthExpend);
		}
		//查询每日收入与支出总金额
		foreach ($TimeDataArr as $key => $val) {
			$TimeDataArr[$key]['time'] = date("Y-m-d",$val['time']);
			//查询日收入总金额
			$IncomeTimeDataArr = DB::query("select time,inout_start,sum(money) as money from bill_charge where inout_start=1 and time=".$val['time']." group by time");
			foreach ($IncomeTimeDataArr as $kk => $vv) {
				$TimeDataArr[$key]['IncomeTimeDataArrSum'] = $vv['money'];
			}
			//查出日支出总金额
			$ExpendTimeDataArr = DB::query("select time,inout_start,sum(money) as money from bill_charge where inout_start=2 and time=".$val['time']." group by time");
			foreach ($ExpendTimeDataArr as $kk => $vv) {
				$TimeDataArr[$key]['ExpendTimeDataArrSum'] = $vv['money'];
			}
			$TimeDataArr[$key]['array'] = DB::name("charge")->alias("c")->join("bill_inout_class i","c.inout_class=i.c_id")->where("c.time",$val['time'])->select();
		}
		// print_r($TimeDataArr);die;
		if(isset($TimeDataArr) || !empty($TimeDataArr))
		{
			return array("start"=>1,"data"=>$TimeDataArr,"MonthBalance"=>$MonthBalance,"MonthIncome"=>$MonthIncome,"MonthExpend"=>$MonthExpend);
		}
		else
		{
			return array("start"=>"0","msg"=>"获取数据失败","MonthBalance"=>0,"MonthIncome"=>0,"MonthExpend"=>0);
		}
	}

	/**
	 * 获取用户ID
	 * @param [type] $openid [description]
	 */
	public function UserId($userOpenid)
	{
		$user_id = DB::name("public_follow")->alias("f")->field("f.uid")->join("bill_user u","f.uid=u.uid")->where("f.openid",$userOpenid)->find();
		return $user_id['uid'];
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
}