<?php
namespace app\port\model;
use think\Model;
use think\Db;
class UserModel extends Model
{
	 // 设置当前模型对应的完整数据表名称
    protected $table = 'user';

    /**
	* 根据用户用户ID获取用户信息
	* @param  [post] [description]
	* @return [type] [description]
	* @author [qinlh] [WeChat QinLinHui0706]
	*/
	public function UidUserInfo($uid) {
        $res = DB::name($this->table)->where("uid",$uid)->find();
        if(true == $res) {
            return ['code'=>1, 'data'=>$res, 'msg'=>'查询用户信息成功'];
        } else {
            return ['code'=>0, 'msg'=>'查询用户信息失败'];
        }
	}
}