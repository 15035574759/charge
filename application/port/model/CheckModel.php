<?php
/**
 * 极客之家 高端PHP - 账单Model
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2017-4-3 16:36:52
 */
namespace app\port\model;
use think\Model;
use think\Db;

class CheckModel extends Model
{
	protected $name = '';

	/**
	 * 查询当前用户所有账单
	 * @return [type] openid [用户openid]
	 */
	public function UserCheck($userOpenid,$lastid,$limit)
	{
		$uid = $this->UserOpenid($userOpenid);
		// $uid = 2;
		if(!isset($uid) || empty($uid))
		{
			return array("code"=>-1,"data"=>"","msg"=>"获取用户ID失败");
		}
		// $start = date('Y-m-01 00:00:00');
		// $end = date('Y-m-d H:i:s');
		$start = date('Y-m-01 H:i:s', strtotime(date("Y-m-d")));
		$end = date('Y-m-d 23:29:59', strtotime("$start +1 month -1 day"));

		//本月总金额
		// $MonthAmount = DB::query("SELECT sum(`money`) as `money`,`time`,`user_id` FROM `bill_charge` WHERE `user_id` = '$uid' AND `time` >= unix_timestamp('".$start."') AND `time` <= unix_timestamp('$end')");
		// $MonthAmount = $MonthAmount[0]['money'];

		//本月收入
		$MonthIncome = DB::query("
							SELECT
								sum(`money`) AS `money`,
								`time`
							FROM
								`bill_charge`
							WHERE
								`user_id` = '$uid'
							AND `inout_start` = 1
							AND `time` >= unix_timestamp('".$start."')
							AND `time` <= unix_timestamp('$end')
							ORDER BY
								`a_id` DESC
						");
		$MonthIncome = $MonthIncome[0]['money'];
		if($MonthIncome == null){$MonthIncome = 0;}

		//本月支出
		$MonthExpend = DB::query("
							SELECT
								sum(`money`) AS `money`,
								`time`
							FROM
								`bill_charge`
							WHERE
								`user_id` = '$uid'
							AND `inout_start` = 2
							AND `time` >= unix_timestamp('".$start."')
							AND `time` <= unix_timestamp('$end')
							ORDER BY
								`a_id` DESC
						");
		if(empty($MonthExpend) || !isset($MonthExpend)){$MonthExpend = 0;}
		$MonthExpend = $MonthExpend[0]['money'];
		if($MonthExpend == null){$MonthExpend = 0;}

		//本月预算余额
		$MonthBalance = $MonthIncome - $MonthExpend;
		// echo $MonthBalance;die;
		

		if($lastid == 1){exit;}//没有数据了
		$where = "a_id > 0 and user_id = ".$uid."";
		if($lastid > 0)
		{
			$where .= " and a_id < $lastid";
		}

		
		// echo $lastid;die;
		//支出与收入数据
		$TimeDataArr = DB::query("
						SELECT
							time,
							a_id
						FROM
							bill_charge
						WHERE
							$where
						GROUP BY
							time
						ORDER BY
							a_id DESC
						LIMIT $limit
					");
		// print_r($TimeDataArr);die;
		if(!isset($TimeDataArr))
		{
			return array(
					"start"=>1,
					"data"=>$TimeDataArr,
					"MonthBalance"=>$MonthBalance,
					"MonthIncome"=>$MonthIncome,
					"MonthExpend"=>$MonthExpend
				);
		}
		
		//查询当前用户收入与支出数据条数
		$TimeDataArrCount = count(DB::query("
						SELECT
							time,
							a_id
						FROM
							bill_charge
						WHERE
							$where
						GROUP BY
							time
						ORDER BY
							time DESC
					"));	 
		//查询每日收入与支出总金额
		foreach ($TimeDataArr as $key => $val) {
			$TimeDataArr[$key]['time'] = date("Y-m-d",$val['time']);
			//查询日收入总金额
			$IncomeTimeDataArr = DB::query("
									SELECT
										time,
										inout_start,
										sum(money) AS money
									FROM
										bill_charge
									WHERE
										user_id = ".$uid."
									AND inout_start = 1
									AND time = ".$val['time']."
									GROUP BY
										time
							");
				foreach ($IncomeTimeDataArr as $kk => $vv) {
					$TimeDataArr[$key]['IncomeTimeDataArrSum'] = $vv['money'];
				}
				//查出日支出总金额
				$ExpendTimeDataArr = DB::query("
										SELECT
											time,
											inout_start,
											sum(money) AS money
										FROM
											bill_charge
										WHERE
											user_id = ".$uid."
										AND inout_start = 2
										AND time = ".$val['time']."
										GROUP BY
											time
									");
				foreach ($ExpendTimeDataArr as $kk => $vv) {
					$TimeDataArr[$key]['ExpendTimeDataArrSum'] = $vv['money'];
				}
				$TimeDataArr[$key]['array'] = DB::name("charge")
											->alias("c")
											->join("bill_inout_class i","c.inout_class=i.c_id")
											->where("c.time",$val['time'])
											->where("c.user_id",$uid)
											->order("c.a_id desc")
											->select();
			}
		// print_r($TimeDataArr);die;
		if($TimeDataArr != array())
		{
			return array(
				"start"=>1,
				"data"=>$TimeDataArr,
				"MonthBalance"=>$MonthBalance,
				"MonthIncome"=>$MonthIncome,
				"MonthExpend"=>$MonthExpend,
				"TimeDataArrCount"=>$TimeDataArrCount
			);
		}
		else
		{
			return array(
				"start"=>0,
				"msg"=>"获取数据失败",
				"MonthBalance"=>0,
				"MonthIncome"=>0,
				"MonthExpend"=>0
			);
		}
	}

	/**
	 * 获取用户ID
	 * @param [type] $openid [description]
	 */
	public function UserOpenid($userOpenid)
	{
		$user_id = DB::name("public_follow")
							->alias("f")->field("f.uid")
							->join("bill_user u","f.uid=u.uid")
							->where("f.openid",$userOpenid)
							->find();
		return $user_id['uid'];
	}


	/**
	 * 本月收入与支出数据
	 * @return [type] openid [用户openid]
	 * @return [type] inout_start [1收入   2支出]
	 */
	public function ThisIncomOut($openid)
	{
		$uid = $this->UserOpenid($openid);
		$BeginDate = date('Y-m-01 H:i:s', strtotime(date("Y-m-d")));
		$end = date('Y-m-d 23:29:59', strtotime("$BeginDate +1 month -1 day"));

		//查询收入总金额 inout_start=1
		$IncomeTotalMoney = DB::query("
							SELECT
								sum(`money`) AS `money`
							FROM
								`bill_charge`
							WHERE
								`inout_start` = 1
							AND `time` >= unix_timestamp('".$BeginDate."')
							AND `time` <= unix_timestamp('".$end."')
							AND `user_id` = ".$uid."
						");
		// print_r($TotalMoney);die;
		//查询收入数据 包括概率 inout_start=1
		$IncomeData = DB::query("
							SELECT
								ic.`a_id`,
								SUM(`money`) AS `money`,
								ic.`inout_class`,
								bc.`inout_url`,
								bc.`describe`,
								bc.`color`,
								ic.`inout_start`
							FROM
								bill_charge AS ic
							INNER JOIN `bill_inout_class` AS bc ON ic.inout_class = bc.c_id
							WHERE
								`inout_start` = 1
							AND `time` >= unix_timestamp('".$BeginDate."')
							AND `time` <= unix_timestamp('".$end."')
							AND `user_id` = ".$uid."
							GROUP BY
								`inout_class`
							ORDER BY
							`a_id` DESC
					");
			$IncomeMonerArray = [];
			$IncomeColorArray = [];
			//计算概率 保留两位小数
			foreach ($IncomeData as $key => $val) 
			{
				$IncomeData[$key]['probability'] = ROUND($val['money'] / $IncomeTotalMoney[0]['money'] * 100,2)."%";
				$IncomeMonerArray[] = $val['money'];//收入金额
				$IncomeColorArray[] = $val['color'];//收入颜色
			}
	
		//查询支出总金额 inout_start=2
		$ExpendTotalMoney = DB::query("
							SELECT
								sum(`money`) AS `money`
							FROM
								`bill_charge`
							WHERE
								`inout_start` = 2
							AND `time` >= unix_timestamp('".$BeginDate."')
							AND `time` <= unix_timestamp('".$end."')
							AND `user_id` = ".$uid."
						");

		// print_r($TotalMoney);die;
		//查询支出数据 包括概率 inout_start=2
		$ExpendData = DB::query("
						SELECT
							ic.`a_id`,
							SUM(`money`) AS `money`,
							ic.`inout_class`,
							bc.`inout_url`,
							bc.`describe`,
							bc.`color`,
							ic.`inout_start`
						FROM
							bill_charge AS ic
						INNER JOIN `bill_inout_class` AS bc ON ic.inout_class = bc.c_id
						WHERE
							`inout_start` = 2
						AND `time` >= unix_timestamp('".$BeginDate."')
						AND `time` <= unix_timestamp('".$end."')
						AND `user_id` = ".$uid."
						GROUP BY
							`inout_class`
						ORDER BY
							`a_id` DESC
					");
			$ExpendMonerArray = [];
			$ExpendColorArray = [];
			// print_r($ExpendData);die;
			//计算概率 保留两位小数
			foreach ($ExpendData as $key => $val) 
			{
				$ExpendData[$key]['probability'] = ROUND($val['money'] / $ExpendTotalMoney[0]['money'] * 100,2)."%";
				$ExpendMonerArray[] = $val['money'];//支出金额
				$ExpendColorArray[] = $val['color'];//支出颜色
			}

		$ExpendTotalMoney = $ExpendTotalMoney[0]['money'];//支出总金额
		$IncomeTotalMoney = $IncomeTotalMoney[0]['money'];//收入总金额
		
		// print_r($ExpendColorArray);die;
		
		//返回数据
		return array(
				"IncomeData" => $IncomeData,
				"ExpendData" => $ExpendData,
				"IncomeMonerArray" => $IncomeMonerArray,//收入数据数组
				"IncomeColorArray" => $IncomeColorArray,//收入颜色值
				"ExpendMonerArray" => $ExpendMonerArray,//支出数据数组
				"ExpendColorArray" => $ExpendColorArray,//支出颜色值
				"ExpendTotalMoney" => $ExpendTotalMoney,//支出总金额
				"IncomeTotalMoney" => $IncomeTotalMoney,//收入总金额
			);
	}
	
	/**
	 * 查询预算是否开启 
	 * @return [type] openid [用户openid]
	 */
	public function BudgetMoney($openid)
	{
		$BeginDate = date('Y-m-01 H:i:s', strtotime(date("Y-m-d")));
		$end = date('Y-m-d 23:29:59', strtotime("$BeginDate +1 month -1 day"));

		$uid = $this->UserOpenid($openid);
		$res = DB::name("public_follow")->where("openid",$openid)->field("butged,butged_start")->find();
		if($res['butged_start'] == 1)
		{
			//查询本月消费金额
			$ResidueMoney = DB::query("
							SELECT
								sum(`money`) AS `money`
							FROM
								`bill_charge`
							WHERE
								`inout_start` = 2
							AND `time` >= unix_timestamp('".$BeginDate."')
							AND `time` <= unix_timestamp('".$end."')
							AND `user_id` = ".$uid."
						");
			$ResidueMoney = $res['butged'] - $ResidueMoney[0]['money'];//计算最终预算支出余额
			return array("code"=>1,"data"=>$ResidueMoney,"msg"=>"预算金额已开启");
		}
		else
		{
			return array("code"=>0,"data"=>"","msg"=>"预算金额已关闭");
		}
		
	}

}