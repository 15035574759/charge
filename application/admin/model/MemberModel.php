<?php
/**
 * 极客之家 高端PHP - 会员模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\model;
use think\Model;
use think\Db;
class MemberModel extends Model
{
    protected $name='user';

    /*
     * 获取会员列表
     */
    public function getMemberByWhere($map, $Nowpage, $limits){
        return $this->field('bill_user.*')->where($map)->limit($Nowpage,$limits)->order("uid desc")->select();
    }

    /*
     * 添加会员
     */
    public function insertMember($param){
        try{
            $result = $this->allowField(true)->save($param);
            if(false === $result){
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '会员添加成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /*
     * 编辑会员
     */
    public function updateMember($param){
        try{
            $result = $this->allowField(true)->save($param, ['id' => $param['id']]);
            if(false === $result){
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '会员编辑成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /*
     * 删除会员
     */
    public function delMember($id){
        try{
            $res = DB::name("user")->where("uid",$id)->delete();
            if(true == $res)
            {
                //刪除用戶openid表
                $ress = DB::name("public_follow")->where("uid",$id)->delete();
                if(true == $ress){
                  //刪除用戶好友表數據
                  @DB::name("user_friend")->where("u_id",$id)->delete();
                  //刪除用戶賬本數據
                  @DB::name("charge")->where("user_id",$id)->delete();
                }
                else {
                  return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
                }
            }
            else {
              return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            return ['code' => 1, 'data' => '', 'msg' => '删除会员成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
    /*
     * 根据会员id得到会员信息
     */
    public function getOneMember($id){
        return $this->where('id',$id)->find();
    }

    /*
     * 根据$where 获得用户ID
     */
    public function getUserId($where){
        return $this->where($where)->value('id');
    }
}
