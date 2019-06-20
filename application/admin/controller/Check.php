<?php
/**
 * 极客之家 高端PHP - 账单管理模块
 * @copyright  Copyright (c) 2016 QIN TEAM (https://www.qinlh.com)
 * @license    GUN  Check Public License 2.0
 * @version    Id:  Type_model.php 2019-06-20 16:00:00
 */
namespace app\admin\controller;
use think\Db;
use app\admin\model\CheckModel;

class Check extends Base
{ 
	public $check;
    public function _initialize()
    {
        $this->check = new CheckModel();
    }
      
    /**
    * 账单列表
    * @param  [post] [description]
    * @return [type] [description]
    * @author [qinlh] [WeChat QinLinHui0706]
    */
    public function check_list() {
        $page = input('get.page') ? input('get.page') : 1;//当前页
        $nickname = input('get.nickname') ? input('get.nickname') : '';//用户昵称
        $inout_start = input('get.inout_start') ? input('get.inout_start') : '';//收入支出
        $where = [];
        if($inout_start && $inout_start !== '') {
            $where['c.inout_start'] = $inout_start;
        }

        if($nickname && $nickname !== '') {
            $where['u.nickname'] = ['like','%'.$nickname.'%'];
        }
        $data = $this->check->CheckList($page,$where);
        // p($data);
        $count = $data['count'];
        $allpage = $data['allpage'];
        $lists = $data['lists'];
        $this->assign('Nowpage', $page); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('count', $count);
        $this->assign('nickname', $nickname); //用户名
        $this->assign('inout_start', $inout_start); //收入支出
        if(input('get.page')){
            return json($lists);
        }
        return $this->fetch();
    }

    /**
    * 删除账单数据 单条
    * @param  [post] [description]
    * @return [type] [description]
    * @author [qinlh] [WeChat QinLinHui0706]
    */
    public function del_check() {
        $id = input('param.id');
        $flag = $this->check->DelCheck($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}