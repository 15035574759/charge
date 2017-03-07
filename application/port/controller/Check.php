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
		if(empty($openid)){
			return array('start'=>0,'msg'=>'获取用户openid失败');
		}
	}
}