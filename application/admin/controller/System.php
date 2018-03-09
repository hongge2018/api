<?php

// +----------------------------------------------------------------------
// | ZZCMS 管理员
// +----------------------------------------------------------------------
namespace zzcms\admin\controller;

use zzcms\common\controller\Admin;
use \think\Loader;
use \think\Db;


class System extends Admin
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    // 网站基本设置
    public function index()
    {
        $model = Loader::model('admin/System');
        if ($this->request->isPost()) {
            $post = $this->request->param();
            if (empty($post)) {
                $this->error('修改信息错误');
            } else {
                if (false === $model->editData($post)) {
                    $this->error('修改信息错误');
                } else {
                    $this->success('修改成功');
                }
            }
        } else {
            $this->assign(Db::name('system')->find());
            return $this->fetch();
        }
    }

    // 管理员列表
    public function admin_lists()
    {
        $this->assign('adminlist', Db::name('admin')->select());
        return $this->fetch();
    }

    /**
     * 修改用户信息
     */
    public function admin_edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $model = Loader::model('admin/Admin');
            if (false === $model->editData($post)) {
                $this->error('修改失败：' . $model->getError());
            } else {
                $this->success('修改成功');
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $id = $this->adminid;
            }
            if ($id < 1) {
                $this->error('ID非法或者该账号不允许修改');
            }
            $this->assign(Db::name('admin')->find($id));
            return $this->fetch();
        }
    }


    /**
     * 修改用户信息
     */
    public function userinfo_edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $model = Loader::model('admin/Admin');
            if (false === $model->editData($post)) {
                $this->error('修改失败：' . $model->getError());
            } else {
                $this->success('修改成功');
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $id = $this->adminid;
            }
            if ($id < 1) {
                $this->error('ID非法或者该账号不允许修改');
            }
            $this->assign(Db::name('admin')->where(['userid' => $id])->find());
            return $this->fetch();
        }
    }


    /**
     * 修改用户信息
     */
    public function password()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $userid = intval($post ['userid']);
            // 判断是不是有userid存在
            if ($userid < 1) {
                $this->error('请指明要修改信息的用户');
            }
            if ($post ['new_password'] != $post ['new_pwdconfirm']) {
                $this->error('两次输入密码不一样');
            }
            // 查询旧密码输入是否正确
            $info = Db::name('admin')->where(['userid' => $userid])->find();
            if (empty ($info)) {
                $this->error('用户不存在');
            }
            if ($info['password'] != md5($post['old_password'])) {
                $this->error('原始密码错误');
            }
            if (false === Db::name('admin')->where(['userid' => $userid])->setField(['password' => md5(trim($post['new_password']))])) {
                $this->error('修改失败');
            } else {
                $this->success('修改成功');
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $id = $this->adminid;
            }
            if ($id < 1) {
                $this->error('ID非法或者该账号不允许修改');
            }
            $this->assign(Db::name('admin')->find($id));
            return $this->fetch();
        }
    }


}
