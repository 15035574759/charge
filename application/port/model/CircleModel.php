<?php
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
		$res = DB::name("circle")->alias("c")->join("bill_inout_class bic","c.class_id=bic.c_id")->select();
		foreach ($res as $key => $value) {
			$res[$key]['friend_length'] = strlen(str_replace(",","",$value['friend_id']));
		}
		return $res;
	}

	/**
	 * 查询当前用户id
	 * @param [type] $openid [用户openid]
	 */
	public function UserOpenid($openid)
	{
		return DB::name("public_follow")->where("openid",$openid)->field("uid")->find();
	}
}