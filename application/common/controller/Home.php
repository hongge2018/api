<?php
namespace zzcms\common\controller;

use think\Controller;
use think\Request;
use think\Db;

class Home extends Controller
{
    protected $userid;
    protected $username;
    protected $request;

    protected function _initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
        $userinfo = cookie('userinfo');
        if (empty($userinfo)) {
            $this->assign('is_logined', 0);
            //购物车数量显示
            $this->assign('cart_number', 0);
        } else {
            //用户ID
            $this->userid = $userinfo['userid'];
            $this->username = $userinfo['username'];
            $this->assign('is_logined', 1);
            //购物车显示
            $this->assign('cart_number', (int)Db::name('life_goods_cart')->where(['user_id' => $this->userid])->sum('number'));
        }
        $this->request = Request::instance();

    }


}