<?php
/**
 * 极客之家 高端PHP - 圈子账本
 *
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
}