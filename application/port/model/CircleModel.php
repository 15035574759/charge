<?php
/**
 * 极客之家 高端PHP - 圈子账本 Model
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2017-4-3 16:36:52
 */
namespace app\port\model;
use think\Model;
use think\Db;

class CircleModel extends Model
{
	protected $name = '';

	/**
	 * 查询圈子分类
	 * @return [type] [description]
	 */
	public function circleAdd_class()
	{
		return  DB::name('inout_class')->where("start",3)->select();
	}

	/**
	 * 查询当前用户下圈子账单
	 * @param  [type] $openid [description]
	 * @return [type]         [description]
	 */
	public function circleUser($openid)
	{
		$uid = $this->UserOpenid($openid);
		// $user_id = DB::name("friend")->where("uid",$uid)->field("f_id")->find();
		// echo $uid;die;
		$res = DB::name("circle")->alias("c")->where("c.user_id",$uid)->join("bill_inout_class bic","c.class_id=bic.c_id")->order("c.cir_id desc")->select();
		// print_r($res);die;
		foreach ($res as $key => $value) {
			//$res[$key]['friend_length'] = strlen(str_replace(",","",$value['friend_id']));
			$friendArr = explode(",",$value['friend_id']);//统计所有好友长度
			$res[$key]['friend_length'] = count($friendArr);
		}
		return $res;
	}

	/**
	 * 删除当前用户下圈子
	 * @param  [type] $c_id [圈子ID]
	 * @return [type] [description]
	 */
	public function circleDel($cir_id)
	{
		//1. 先删除圈子表 2.再删除圈子账单表 3. 删除圈子账单关联表
		$res1 = DB::name("circle")->where("cir_id",$cir_id)->delete();
		if(false === $res1)
		{
			return array("code"=>"-1","data"=>"","msg"=>"删除圈子失败哦");
		}
		$res2 = DB::name("circle_bill")->where("cir_id",$cir_id)->delete();
		if(false === $res2)
		{
			return array("code"=>"-1","data"=>"","msg"=>"删除圈子账单失败哦");
		}
		$res3 = DB::name("circle_friend_assoct")->where("cir_id",$cir_id)->delete();
		if(false === $res3)
		{
			return array("code"=>"-1","data"=>"","msg"=>"删除圈子账单关联表失败哦");
		}
		return array("code"=>1,"data"=>"","msg"=>"删除成功");
	}

	/**
	 * 查询圈子名称  做标题使用
	 * @param  [type] $cir_id [圈子ID]
	 * @param  [type] $openid [用户openid]
	 * @return [type]         [description]
	 */
	public function CircleName($cir_id,$openid)
	{
		$uid = $this->UserOpenid($openid);
		$res = DB::name("circle")->where("cir_id",$cir_id)->where("user_id",$uid)->field("circle_name,cir_id")->find();
		// print_r($res);die;
		return $res;
	}

	/**
	 * 添加圈子账本
	 * @param  [type] $openid [用户openid]
	 * @return [type]         [description]
	 */
	public function GetAddCircle($openid,$circle_name,$c_id)
	{
		$uid = $this->UserOpenid($openid);
		// 根据管理员ID 查询好友所属id
		// $FriendUId = DB::name("friend")->where("uid",$uid)->field("f_id")->find();
		$data = array(
				'circle_name' =>  $circle_name,
				'friend_id' =>  $uid,
				'user_id' =>  $uid,
				'time' =>  time(),
				'class_id' =>  $c_id
			);

		//先判断当前用户是否添加该名称圈子
		$arr = DB::name("circle")->where(['user_id'=>$uid,'circle_name'=>$circle_name])->find();
		if($arr)
		{
			return array("code"=>-1,"data"=>"","msg"=>"该名称已经添加");
		}

		$res = DB::name("circle")->insert($data);
		if(false === $res)
		{
			return array("code"=>1,"data"=>"","msg"=>"添加失败");
		}
		else
		{
			return array("code"=>-1,"data"=>"","msg"=>"添加成功");
		}
	}


	/**
	 * 查询圈子好友 信息
	 * @param  [type] $cir_id [圈子ID]
	 * @param  [type] $openid [用户openid]
	 * @return [type]         [description]
	 */
	public function CircleFriend($cir_id,$openid)
	{
		$uid = $this->UserOpenid($openid);
		$res = DB::name("circle")->where("cir_id",$cir_id)->where("user_id",$uid)->field("friend_id,cir_id")->select();
		foreach ($res as $key => $val) {
			// $friend_id = substr($val['friend_id'],0,5);
			$strlen = substr_count($val['friend_id'],",");
			if($strlen >= 3)
			{
				$n = 0;//提取第4个逗号之前的数据
				for($i = 1;$i <= 3;$i++) {
					$n = strpos($val['friend_id'], ',', $n);
					$i != 3 && $n++;
				}
				// echo $n;
				$friend_id = substr($val['friend_id'],0,$n);
				$arr = DB::name("friend")->where("f_id","in",$friend_id)->select();
			}
			else
			{
				$arr = DB::name("friend")->where("f_id","in",$val['friend_id'])->select();
			}
			$friendArr = explode(",",$val['friend_id']);//统计所有好友长度
			$friend_length = count($friendArr);
		}
		// print_r($arr);die;
		return array('arr'=>$arr,'friend_length'=>$friend_length,"cir_id"=>$cir_id);
	}

	/**
	 * 查询当前圈子好友消费金额
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]
	 */
	public function CircleFriendAll($cir_id,$openid)
	{
		$uid = $this->UserOpenid($openid);

		$res = DB::name("circle")->where("cir_id",$cir_id)->where("user_id",$uid)->field("friend_id,cir_id")->find();
		// foreach ($res as $key => $val) {
			$arr = DB::name("friend")->where("f_id","in",$res['friend_id'])->select();
		// }
		// print_r($arr);
		foreach ($arr as $key => $val) {
			$consume = DB::query("SELECT sum(sum_money) as sum_money,cir_id,pay_start,friend_id FROM bill_circle_friend_assoct WHERE cir_id = '$cir_id' AND pay_start = 1 AND friend_id = ".$val['f_id']."");
			$pay = DB::query("SELECT sum(sum_money) as sum_money,cir_id,pay_start,friend_id FROM bill_circle_friend_assoct WHERE cir_id = '$cir_id' AND pay_start = 0 AND friend_id = ".$val['f_id']."");
			if($val['uid'] == $uid)
			{
				$arr[$key]['start'] = -1;//管理员
			}

			$arr[$key]['consume'] = 0;
			foreach ($consume as $kk => $vv) {
				if(!isset($vv['sum_money']))
				{
					$arr[$key]['consume_money'] = 0;
				}
				else
				{
					$arr[$key]['consume_money'] = $vv['sum_money'];
				}
			}

			foreach ($pay as $kk => $vv) {
				if(!isset($vv['sum_money']))
				{
					$arr[$key]['pay_money'] = 0;
				}
				else
				{
					$arr[$key]['pay_money'] = $vv['sum_money'];
				}
			}

		}
		// print_r($arr);die;
		return $arr;
	}

	/**
	 * 获取当前圈子下我的消费金额  以及我的账单列表
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [用户openid]
	 */
	public function CircleMyconsume($cir_id,$openid)
	{
		$uid = $this->UserOpenid($openid);
		$f_id = DB::name("friend")->where("uid",$uid)->field("f_id")->find();

		$gatherMoney = 0;
		$payMoney = 0;
		$MyconsumeMoney = 0;
		$AllconsumeMoney = 0;

		//查询当前圈子需要收取金额
		// $gatherMoneydata = DB::name("circle_bill")->where("cir_id",$cir_id)->where("payer",$uid)->field("cbl_id")->select();
		// $gatherMoneystr = implode(",",array_column($gatherMoneydata,'cbl_id'));
		// $gatherMoney = DB::name("circle_friend_assoct")->where("cbl_id","in",$gatherMoneystr)->where("pay_start",0)->sum('sum_money');

		//查询当前圈子需要付款金额
		$payMoney = DB::name("circle_friend_assoct")->where(['cir_id'=>$cir_id,'friend_id'=>$f_id['f_id'],'pay_start'=>0])->sum('sum_money');

		//查询我的消费金额
		$MyconsumeMoney = DB::name("circle_friend_assoct")->where(['friend_id'=>$f_id['f_id'],'pay_start'=>1,'cir_id'=>$cir_id])->sum("sum_money");

		//查询全员消费金额
		$AllconsumeMoney = DB::name("circle_friend_assoct")->where(['cir_id'=>$cir_id])->sum("sum_money");

		//查询圈子账单列表
		$billData = DB::name("circle_bill")->alias("b")->join("bill_friend f","b.payer=f.f_id")->join("bill_inout_class i","b.class_id=i.c_id")->where('b.cir_id',$cir_id)->select();
		//查询几个人消费
		foreach ($billData as $key => $val) {
			$billData[$key]['staffnum'] = DB::name("circle_friend_assoct")->where("cbl_id",$val['cbl_id'])->count();
			$billData[$key]['circle_time'] = $this->format_date($val['circle_time']);
		}

		return array('payMoney'=>$payMoney,'MyconsumeMoney'=>$MyconsumeMoney,'AllconsumeMoney'=>$AllconsumeMoney,'billData'=>$billData);
	}


	/**
	 * 获取当前用户下所有好友
	 * @param [type] $cir_id [圈子id]
	 * @param [type] $openid [用户openid]
	 */
	public function UserMyFriend($cir_id,$openid)
	{
		$uid = $this->UserOpenid($openid);
		//查询当前用户好友ID
		$user_id = DB::name("friend")->where("uid",$uid)->field("f_id")->find();
		// p($user_id);
		//查询当前用户下所有好友
		$MyFriend = DB::name("user_friend")->alias("uf")->join("bill_friend bf","uf.f_id=bf.f_id")->where("uf.u_id",$uid)->field("bf.f_id")->select();
		foreach ($MyFriend as $key => $val) {
			$MyFriends[] = $val['f_id'];
		}
		// p($MyFriends);
		//查询当前圈子下已添加的好友
		$CircleFriend = DB::name("circle")->where("cir_id",$cir_id)->field("friend_id")->find();
		$CircleFriend = explode(",",$CircleFriend['friend_id']);
		//删除管理员
		foreach($CircleFriend as $key => $val){
			if($val == $user_id['f_id']){
				unset($CircleFriend[$key]);
			}
		}

		$NotAddFriend = array_diff($MyFriends,$CircleFriend);//筛选未添加的好友

		$CircleFriends = array_filter($NotAddFriend);//去除空数组
		// p($CircleFriends);
		if($CircleFriends == array()){
			return array("code"=>-1,"data"=>"","msg"=>"当前没有好友,请到好友管理添加好友");
		}
		else
		{
			$CircleFriendstr = implode(",",$CircleFriends);
			// foreach ($CircleFriends as $key => $val){
				// $num[] = $val['f_id'];
				// $CircleFriendstr = join(",",$num);
			// }
		}

		return DB::name("friend")->where("f_id","in",$CircleFriendstr)->select();
	}

	/**
	 * 圈子添加好友
	 * @param [type] $cir_id [圈子id]
	 * @param [type] $openid [用户openid]
	 * @param [type] $f_id   [好友id]
	 */
	function AddFriend($cir_id,$openid,$f_id)
	{
		$uid = $this->UserOpenid($openid);
		$strting = DB::name("circle")->where(["cir_id"=>$cir_id,"user_id"=>$uid])->field("friend_id")->find();
		// print_r($res);die;
		$str = $this->insertToStr($strting['friend_id'],0,$f_id.",");//添加字符串
		//修改好友字符串
		$res = DB::name("circle")->where(["cir_id"=>$cir_id,"user_id"=>$uid])->update(['friend_id'=>$str]);
		return $res;
	}


	/**
	 * 获取当前圈子下所有好友
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]
	 */
	// public function CircleMyFriend($cir_id,$openid)
	// {
	// 	$uid = $this->UserOpenid($openid);
	// }

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
	 * 选择付款人
	 * @param [type] $cir_id [description]
	 * @param [type] $openid [description]
	 */
	public function GetPayer($cir_id,$openid)
	{
		$uid = $this->UserOpenid($openid);
		$Payer = DB::name("circle")->where(['cir_id'=>$cir_id,'user_id'=>$uid])->field("friend_id")->find();
		return DB::name("friend")->where("f_id","in",$Payer['friend_id'])->select();
	}

	/**
	 * 根据选择付款人 展示付款人信息
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]
	 * @param [type] $f_id [用户选择的付款人id]
	 */
	public function GetShowpayer($cir_id,$openid,$f_id)
	{
		$uid = $this->UserOpenid($openid);
		//查询付款人数据
		$CountPayCount = DB::name("friend")->where("f_id","in",$f_id)->count();//付款人数量
		$GetpayerData = DB::name("friend")->where("f_id","in",$f_id)->select();//付款人数据

		//查询参与人数据
		$arr1 = explode(",",$f_id);
		$arr2 = DB::name("circle")->where(["cir_id"=>$cir_id,"user_id"=>$uid])->field("friend_id")->find();
		$arr2 = explode(",",$arr2['friend_id']);
		$str = implode(",",array_diff($arr2,$arr1));
		$DataAllParticipant = DB::name("friend")->where("f_id","in",$str)->select();//参与人数据
		// p($DataAllParticipant);
		$DataAllParticipantNum = DB::name("friend")->where("f_id","in",$str)->count();//参与人数量

		//查询当前账单所有用户数量
		$CircleAlluserNum = DB::name("circle")->where(['cir_id'=>$cir_id,'user_id'=>$uid])->field("friend_id")->find();
		$CircleAlluserNumArr = explode(",",$CircleAlluserNum['friend_id']);
		$CircleAlluserNum = count($CircleAlluserNumArr);

		return array(
				"CountPayCount"=>$CountPayCount,//支付人数量
				"GetpayerData"=>$GetpayerData,//支付人数据
				"DataAllParticipant"=>$DataAllParticipant,//参与人数据
				"DataAllParticipantNum"=>$DataAllParticipantNum,//参与人数量
				"DataAllParticipantID"=>$str,//参与人 id
				"CircleAlluserNum"=>$CircleAlluserNum//所有人数量
			);
	}

	/**
	 * 如果未选择付款人  默认选择管理员
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]
	 */
	public function GetShowpayerEmpty($cir_id,$openid)
	{
		//查询默认管理员
		$uid = $this->UserOpenid($openid);
		// echo $cir_id;die;
		$AdminData = DB::name("friend")->where("uid",$uid)->field("f_id,friend_name,friend_imgurl")->select();

		//查询默认管理员ID
		$f_id = $AdminData[0]['f_id'];
		$DataAllStr = DB::name("circle")->where(["cir_id"=>$cir_id,"user_id"=>$uid])->field("friend_id")->find();
		$DataAllArr = explode(",",$DataAllStr['friend_id']);
		// print_r($DataAllStr);die;
		foreach ($DataAllArr as $key => $val) {
			if($f_id == $val)
			{
				unset($DataAllArr[$key]);
			}
		}
		$str = implode(",",$DataAllArr);
		// echo $str;die;
		$DataAllParticipant = DB::name("friend")->where("f_id","in",$str)->select();//参与人数据
		$DataAllParticipantNum = DB::name("friend")->where("f_id","in",$str)->count();//参与人数量

		//查询当前账单所有用户数量
		$CircleAlluserNum = DB::name("circle")->where(['cir_id'=>$cir_id,'user_id'=>$uid])->field("friend_id")->find();
		$CircleAlluserNumArr = explode(",",$CircleAlluserNum['friend_id']);
		$CircleAlluserNum = count($CircleAlluserNumArr);
		// print_r($CircleAlluserNumArr);die;
		return array(
			"AdminData"=>$AdminData,
			"DataAllParticipant"=>$DataAllParticipant,//参与人数据
			"DataAllParticipantNum"=>$DataAllParticipantNum,//参与人数量
			"DataAllParticipantID"=>$str,//参与人 id
			"CircleAlluserNum"=>$CircleAlluserNum//所有人数量
		);
	}

	/**
	 * 开始账单  记账
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $c_id [记账分类]
	 * @param [type] $f_id [付款人]
	 * @param [type] $PayMoney [总支出]
	 * @param [type] $remark [备注]
	 */
	public function AddCircle($data,$data1,$f_id,$PaySumMoney)
	{
		DB::name("circle_bill")->insert($data);
		$AddId = Db::name('circle_bill')->getLastInsID();
		$data1['cbl_id'] = $AddId;
		$friend_id = explode(",",$data1['friend_id']);
		$FidArr = explode(",",$f_id);
		// print_r($FidArr);die;
		foreach ($friend_id as $key => $val) {
			$data1['friend_id'] = $friend_id[$key];
			$result = DB::name("circle_friend_assoct")->insert($data1);//先添加未付款好友入库
		}
		foreach ($FidArr as $kk => $f_id) {//再添加已付款好友入库
			// echo $FidArr[$kk];
			$FidArrData = array(
					'cbl_id' => $AddId,
					'cir_id' => $data1['cir_id'],
					'friend_id' => $FidArr[$kk],
					'sum_money' => $data1['sum_money'],
					'pay_start' => 1,//已支付
				);
			$result = DB::name("circle_friend_assoct")->insert($FidArrData);
		}
		if(false === $result)
		{
			return array("msg"=>-1,"data"=>"添加失败");
		}
		else
		{
			return array("msg"=>1,"data"=>"添加成功");
		}
	}

	/**
	 * 获取当前圈子账单列表
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]
	 */
	public function CircleList($cir_id,$openid)
	{
		$data = DB::name("circle_bill")->alias("ab")->join("bill_inout_class ic","ab.class_id=ic.c_id")->order("ab.cbl_id desc")->where("cir_id",$cir_id)->select();//账单数据
		//查询几个人消费
		$consume = DB::name("circle")->where("cir_id",$cir_id)->field("friend_id")->find();
		$str = explode(",",$consume['friend_id']);
		$ConsumeLength = count($str);//统计所有参加消费数量
		foreach ($data as $key => $val) {
			$data[$key]['circle_time'] = $this->format_date($val['circle_time']);//时间
			$data[$key]['consume_num'] = $ConsumeLength - count(explode(",",$val['payer']));//消费人数量

			//支付人
			$consume_name = DB::name("friend")->where("f_id","in",$val['payer'])->select();
			$strnum = "";
			$num = array();
			foreach ($consume_name as $k => $v) {
				$num[] = $v['friend_name'];
				$strnum = join(",",$num);
			}
			$data[$key]["consume_name"] = mb_strlen($strnum, 'utf-8') > 9 ? mb_substr($strnum, 0, 9, 'utf-8').'....' : $strnum;//参与人  例：张三，王五，李四
		}
		// print_r($data);die;
		return $data;
	}

	 /**
	 * 获取当前圈子账单明细数据
	 * @param [type] $cir_id [圈子ID]
	 * @param [type] $openid [openid]
	 */
	public function CircleDetails($cbl_id)
	{
		//1. 查询所属圈子
		$CircleName = DB::name("circle_bill")
						->alias("b")
						->join("bill_circle c","b.cir_id=c.cir_id")
						->join("bill_inout_class bc","c.class_id=bc.c_id")
						->where("b.cbl_id",$cbl_id)
						->find();
		$CircleName['PayerNum'] = count(explode(",",$CircleName['payer']));//支付人员个数

		//支付人数据
		$CircleName['PayerData'] = DB::name("friend")->where("f_id","in",$CircleName['payer'])->field("friend_name,friend_imgurl,f_id")->select();
		foreach ($CircleName['PayerData'] as $key => $val) {
			$CircleName['PayerData'][$key]['payment'] = round($CircleName['total'] / $CircleName['PayerNum'],2);
			$CircleName['PayerData'][$key]['friend_name'] = mb_strlen($val['friend_name'], 'utf-8') > 5 ? mb_substr($val['friend_name'], 0, 5, 'utf-8').'....' : $val['friend_name'];
		}
		// print_r($CircleName);die;

		// //查询参与人个数
		$FriendSum = count(explode(",",$CircleName['friend_id']));//总人数
		// $CircleName['ParticipantNum'] = $FriendSum - $CircleName['PayerNum'];//参与人 = 好友总数 - 支付人数

		//查询参与人数据
		$arr1 = explode(",",$CircleName['payer']);
		$arr2 = explode(",",$CircleName['friend_id']);
		$str = implode(",",array_diff($arr2,$arr1));
		$CircleName['DataAllParticipant'] = DB::name("friend")->where("f_id","in",$str)->select();//参与人数据

		$CircleName['DataAllParticipantNum'] = DB::name("friend")->where("f_id","in",$str)->count();//参与人数量

		//查询参与人消费金额
		foreach ($CircleName['DataAllParticipant'] as $key => $val) {
			$CircleName['DataAllParticipant'][$key]['payer'] = round($CircleName['total'] / $FriendSum,2);
			$CircleName['DataAllParticipant'][$key]['friend_name'] = mb_strlen($val['friend_name'], 'utf-8') > 5 ? mb_substr($val['friend_name'], 0, 5, 'utf-8').'....' : $val['friend_name'];
			$CircleName['DataAllParticipant'][$key]['pay_start'] = DB::name("circle_friend_assoct")->where(["cbl_id"=>$cbl_id,"friend_id"=>$val['f_id']])->field("pay_start")->find()['pay_start'];//支付状态
		}

		//查询记录人
		$CircleName['UserName'] = DB::name("circle")->alias("c")->join("bill_friend b","c.user_id=b.f_id")->where("c.cir_id",$CircleName['cir_id'])->field("c.user_id,b.friend_name,c.time")->find();
		$CircleName['UserName']['time'] = date("Y-m-d H:i:s",$CircleName['UserName']['time']);
		// print_r($CircleName);die;
		return $CircleName;
	}

	/**
	 * 修改用户账单支付状态
	 * @param [type] $cbl_id [账单ID]
	 * @param [type] $f_id [参与人ID]
	 */
	function UpdateCirclePayment($cbl_id,$f_id)
	{
		$res = DB::name("circle_friend_assoct")->where(['cbl_id'=>$cbl_id,'friend_id'=>$f_id])->update(['pay_start'=>1]);
		if(false === $res)
		{
			return array("code"=>-1,"data"=>"","msg"=>"付款失败");
		}
		else
		{
			return array("code"=>1,"data"=>"","msg"=>"付款成功");
		}
	}

	/**
	 * 删除用户账单数据
	 * @param [type] $cbl_id [账单ID]
	 * @param [type] $f_id [参与人ID]
	 */
	function DeleteCirclePayment($cbl_id)
	{
		DB::name("circle_friend_assoct")->where('cbl_id',$cbl_id)->delete();
		$res = DB::name("circle_bill")->where('cbl_id',$cbl_id)->delete();
		if(false === $res)
		{
			return array("code"=>-1,"data"=>"","msg"=>"删除失败");
		}
		else
		{
			return array("code"=>1,"data"=>"","msg"=>"删除成功");
		}
	}

	/**
      * 时间处理 【几分钟前、几小时前、几天前】
      * @param  [type] $time [time]
      * @return [type]           [description]
      */
	function format_date($time)
	{
	    $t = time()-$time;
	    $f = array(
	        '31536000'=>'年',
	        '2592000'=>'个月',
	        '604800'=>'星期',
	        '86400'=>'天',
	        '3600'=>'小时',
	        '60'=>'分钟',
	        '1'=>'秒'
	    );
	    foreach ($f as $k=>$v)    {
	        if (0 !=$c=floor($t/(int)$k)) {
	            return $c.$v.'前';
	        }
	    }
	}

	/**
	 * 指定位置插入字符串
	 * @param $str  原字符串
	 * @param $i    插入位置
	 * @param $substr 插入字符串
	 * @return string 处理后的字符串
	 */
	public function insertToStr($str, $i, $substr){
	    //指定插入位置前的字符串
	    $startstr="";
	    for($j=0; $j<$i; $j++){
	        $startstr .= $str[$j];
	    }

	    //指定插入位置后的字符串
	    $laststr="";
	    for ($j=$i; $j<strlen($str); $j++){
	        $laststr .= $str[$j];
	    }

	    //将插入位置前，要插入的，插入位置后三个字符串拼接起来
	    $str = $startstr . $substr . $laststr;

	    //返回结果
	    return $str;
}

	/**
	 * 二维数组合并并求和
	 * @param [type] $arr [description]
	 */
	public function ArrayMrge($arr){
		$newarr = array();
		if($arr){
			$i = 0;
			foreach($arr as $key=>$val){
				if($i==0){
					$m = $key;
					$newarr[$m] = array();
				}
				$i++;
				if($val){
					foreach($val as $vk=>$vv){
						$ife = array_key_exists($vk,$newarr[$m]); //检测键名是否存在
						if($ife){
							$newarr[$m][$vk] += $vv;
						}else{
							$newarr[$m][$vk] = $vv;
						}
					}
				}
			}
		}
		return $newarr;
	}
}
