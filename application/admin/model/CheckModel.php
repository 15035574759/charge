<?php
/**
 * 极客之家 高端PHP - 账单管理模块 Model
 * @copyright  Copyright (c) 2016 QIN TEAM (https://www.qinlh.com)
 * @license    GUN  CheckModel Public License 2.0
 * @version    Id:  Type_model.php 2019-06-20 16:00:00
 */
namespace app\admin\model;

use think\Db;
use think\Model;
use think\Config;

class CheckModel extends Model
{
    protected $table = 'charge';

    /**
     * 获取账单列表
     * @param  [post] [description]
     * @return [type] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function CheckList($page=1, $where=1)
    {
        try {
            $limits = Config('PAGE_COUNT'); // 每页显示条数
            $count = Db::name($this->table)
                    ->alias("c")
                    ->join("bill_user u", "c.user_id=u.uid")
                    ->join("bill_inout_class ic", "c.inout_class=ic.c_id")
                    ->where($where)
                    ->count(); //计算总页面
            $allpage = intval(ceil($count / $limits));
            $lists = DB::name($this->table)
                ->alias("c")
                ->join("bill_user u", "c.user_id=u.uid")
                ->join("bill_inout_class ic", "c.inout_class=ic.c_id")
                ->field("c.*,u.nickname,u.headimgurl,ic.describe")
                ->where($where)
                ->page($page, $limits)
                ->order('a_id desc')
                ->select();
            foreach ($lists as $key => $val) {
                $lists[$key]['time'] = date("Y-m-d", $val['time']);
                $lists[$key]['keep_time'] = date("Y-m-d H:i:s", $val['keep_time']);
                // $lists[$key]['inout_start'] = $val['inout_start'] == 1 ? '支出' : '收入';
            }
            return ['count' => $count, 'allpage' => $allpage, 'lists' => $lists];
        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
    * 删除账单
    * @param  [post] [description]
    * @return [type] [description]
    * @author [qinlh] [WeChat QinLinHui0706]
    */
    public function DelCheck($id){
        try{
            $res = DB::name($this->table)->where("a_id", $id)->delete();
            if(false == $res) {
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            return ['code' => 1, 'data' => '', 'msg' => '删除账单成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}
