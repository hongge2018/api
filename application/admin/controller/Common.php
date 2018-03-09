<?php

// +----------------------------------------------------------------------
// | ZZCMS 后台登录页面
// +----------------------------------------------------------------------
namespace zzcms\admin\controller;

use zzcms\common\controller\Admin;
use \think\Db;
use \think\Loader;


class Common extends \think\Controller
{

    protected function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 登录
     * @return mixed
     */
    public function login()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $data = Loader::model('admin/Admin')->getOne(['username' => $post['username']]);
            if (empty($data)) {
                $this->error('用户名不存在');
            }
            //把数据转换为数组
            if ($data['password'] != md5($post['password'])) {
                $this->error('您输入的密码不正确');
            }
            unset($data['password']);
            \think\Session::set('admininfo', $data);
            $this->success('登录成功');
        } else {
            return $this->fetch();
        }
    }

    /**
     * 退出成功
     */
    public function logout()
    {
        // 清除session（当前作用域）
        session('adminid', null);
        cookie('admininfo', null);
        $this->redirect('common/login');
    }

}
