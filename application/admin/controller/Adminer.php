<?php

namespace zzcms\admin\controller;

use zzcms\common\controller\Admin;
use \think\Loader;
use \think\Db;

class Adminer extends Admin
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    // 网站基本设置
    public function index()
    {
        return $this->fetch();
    }

    // 管理员列表
    public function adminList()
    {
        $lists = Db::name('admin')->select();
        $rulelist = createCache('auth_group');

        foreach ($lists as $k => $item) {
            $item['rulename'] = isset($rulelist[$item['roleid']]) ? $rulelist[$item['roleid']]['title'] : '无';
            $lists[$k] = $item;
        }

        $this->assign('adminlist', $lists);
        return $this->fetch();
    }

    /**
     * 修改用户信息
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $model = Loader::model('admin/Admin');
            if (false === $model->editData($post)) {
                $this->error('修改信息错误');
            } else {
                $this->success('修改成功');
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $id = $this->admin_id;
            }
            if ($id < 1) {
                $this->error('ID非法或者该账号不允许修改');
            }
            $this->assign($this->model->find($id));
            $rulelist = Db::name('auth_group')->select();
            $this->assign('rulelist', $rulelist);
            return $this->fetch();
        }
    }


    /**
     * 修改用户信息
     */
    public function adminPublish()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            //验证  唯一规则： 表名，字段名，排除主键值，主键名
            $validate = new \think\Validate([
                ['username', 'require|unique:admin,username,0,userid', '用户名不能为空|用户名已存在'],
                ['realname', 'require', '真实姓名不能为空'],
                ['password', 'require', '密码不能为空'],
            ]);
            //验证数据合法性
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            $post['password'] = md5($post['password']);
            $model = Loader::model('admin/Admin');
            if (false === $model->addData($post)) {
                $this->error('添加失败：' . $model->getError());
            } else {
                $this->success('添加成功');
            }
        } else {
            $rulelist = Db::name('auth_group')->select();
            $this->assign('rulelist', $rulelist);
            return $this->fetch();
        }
    }


    /**
     * 修改用户信息
     */
    public function adminAdd()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            //验证  唯一规则： 表名，字段名，排除主键值，主键名
            $validate = new \think\Validate([
                ['username', 'require|unique:admin,username,0,userid', '用户名不能为空|用户名已存在'],
                ['realname', 'require', '真实姓名不能为空'],
                ['password', 'require', '密码不能为空'],
            ]);
            //验证数据合法性
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            $post['password'] = md5($post['password']);


            $model = Loader::model('admin/Admin');
            if (false === $model->addData($post)) {
                $this->error('添加失败：' . $model->getError());
            } else {
                $this->success('添加成功');
            }
        } else {
            $rulelist = Db::name('auth_group')->select();
            $this->assign('rulelist', $rulelist);
            return $this->fetch();
        }
    }

    /**
     * 修改用户信息
     */
    public function adminEdit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $model = Loader::model('admin/Admin');
            //验证  唯一规则： 表名，字段名，排除主键值，主键名
            $validate = new \think\Validate([
                ['realname', 'require', '真实姓名不能为空'],
            ]);
            //验证数据合法性
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            if (empty($post['password'])) {
                unset($post['password']);
            } else {
                $post['password'] = md5($post['password']);
            }

            if (false === $model->editData($post)) {
                $this->error('修改失败：' . $model->getError());
            } else {
                $this->success('修改成功');
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $id = $this->admin_id;
            }
            if ($id < 1) {
                $this->error('ID非法或者该账号不允许修改');
            }
            $this->assign(Db::name('admin')->find($id));
            $rulelist = Db::name('auth_group')->select();
            $this->assign('rulelist', $rulelist);
            return $this->fetch();
        }
    }

    /**
     * 修改用户信息
     */
    public function password()
    {
        $model = Loader::model('admin/Admin');
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $userid = intval($post['userid']);
            // 判断是不是有userid存在
            if ($userid < 1) {
                $this->error('请指明要修改信息的用户');
            }
            if ($post['new_password'] != $post['new_pwdconfirm']) {
                $this->error('两次输入密码不一样');
            }
            // 查询旧密码输入是否正确
            $where = [];
            $where['userid'] = $userid;
            $where['password'] = md5(trim($post['oldpasswrod']));

            $info = $model->where($where)->find();
            if (empty ($info)) {
                $this->error('原始密码错误');
            }

            $data = [];
            $data['password'] = md5(trim($post['new_password']));
            if (false === $model->save($data)) {
                $this->error('修改信息错误');
            } else {
                $this->success('修改成功');
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $this->error('ID非法或者该账号不允许修改');
            }
            $info = $model->find($id);
            $this->assign($info);
            return $this->fetch();
        }
    }


    ###########################################################用户组管理################################################################
    /**
     * 修改用户信息
     */
    public function groupList()
    {
        $model = Loader::model('admin/AuthGroup');
        if ($this->request->isPost()) {
            $post = $this->request->param();

            if (false === $model->editData($post)) {
                $this->error('修改信息错误');
            } else {
                $this->success('修改成功');
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $id = $this->admin_id;
            }
            if ($id < 1) {
                $this->error('ID非法或者该账号不允许修改');
            }
            $lists = $model->getList();
            $this->assign('lists', $lists);
            return $this->fetch();
        }
    }

    /**
     * 用户组添加/修改
     */
    public function groupPublish()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            //验证  唯一规则： 表名，字段名，排除主键值，主键名
            $validate = new \think\Validate([
                ['title', 'require', '角色名称不能为空'],
            ]);
            //验证数据合法性
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            $model = Loader::model('admin/AuthGroup');
            $id = isset($post['id']) ? intval($post['id']) : 0;
            if ($id < 1) {
                $status = $model->addData($post, 'create');
            } else {
                $status = $model->editData($post);
            }
            if ($status) {
                createCache('auth_group');
                $this->success('操作成功', url('grouplist'));
            } else {
                $this->error($model->getError());
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            $info = [];
            if ($id > 0) {
                $info = Loader::model('admin/AuthGroup')->getOne($id);
            }
            $this->assign($info);
            return $this->fetch();
        }
    }


    /**
     * 设置用户组权限
     */
    public function groupAuth()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            if ($this->admin_id != 1) {
                $this->error('你没有权限设置');
            }
            if (false === Db::name('auth_group')->where(['id' => $post['id']])->setField(['rules' => implode(',', $post['menuid'])])) {
                $this->error('设置失败');
            } else {
                $this->success('设置成功');
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $this->error('ID非法或者该账号不允许修改');
            }
            $lists = Db::name('menu')->order('listorder asc')->select();
            /**
             * 查询用户的权限
             */
            $rules = Db::name('auth_group')->where(['id' => $id])->value('rules');
            //用户组权限集
            $ruleArray = explode(',', $rules);

            $tree = new \zzcms\util\Tree();
            $tree->icon = ['&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ '];
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            foreach ($lists as $r) {
                $r['class'] = $r['parentid'] > 0 ? 'class="child-of-node-' . $r['parentid'] . '" ' : '';
                $r['checked'] = in_array($r['id'], $ruleArray) ? 'checked' : '';
                $array [] = $r;
            }
            $tree->init($array);
            $str = "<tr id='node-\$id' \$class>
                  <td style='padding-left:30px;'>\$spacer
                    <input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked  onclick='javascript:checknode(this);'>\$name</td>
                </tr>";
            $info['menu_str'] = $tree->get_tree(0, $str);
            $info['groupid'] = $id;
            $this->assign($info);
            return $this->fetch();
        }
    }


    /**
     * 用户组删除
     * @return mixed
     */
    public function groupDelete()
    {
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if ($id < 1) {
            return $this->error("ID非法！");
        }
        $model = Loader::model('admin/AuthGroup');
        if ($model->deleteData($id)) {
            return $this->success("删除成功！");
        } else {
            return $this->error($model->getError());
        }
    }


    public function get_cate_trees($items)
    {
        $tree = []; //初始化格式化好的树
        foreach ($items as $item) {
            if (isset($items[$item['parentid']])) {
                $items[$item['parentid']]['child'][] = &$items[$item['id']];
            } else {
                $tree[] = &$items[$item['id']];
            }
        }
        return $tree;
    }


    /**
     * 角色权限分配
     * @return mixed
     */
    public function group_access_edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            if ($this->admin_id != 1) {
                $this->error('你没有权限设置');
            }
            if (false === Db::name('auth_group')->where(['id' => $post['id']])->setField(['rules' => implode(',', $post['menuid'])])) {
                $this->error('设置失败');
            } else {
                $this->success('设置成功');
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $this->error('ID非法或者该账号不允许修改');
            }
            $lists = Db::name('menu')->order('listorder asc')->select();
            /**
             * 查询用户的权限
             */
            $rules = Db::name('auth_group')->where(['id' => $id])->value('rules');
            //用户组权限集
            $ruleArray = explode(',', $rules);

            $tree = new \zzcms\util\Tree();
            $tree->icon = ['&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ '];
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            foreach ($lists as $r) {
                $r['class'] = $r['parentid'] > 0 ? 'class="child-of-node-' . $r['parentid'] . '" ' : '';
                $r['checked'] = in_array($r['id'], $ruleArray) ? 'checked' : '';
                $array [] = $r;
            }
            $tree->init($array);
            $str = "<tr id='node-\$id' \$class>
                  <td style='padding-left:30px;'>&nbsp;&nbsp;&nbsp;│ &nbsp;&nbsp;&nbsp;├─
                    <input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked  onclick='javascript:checknode(this);'>\$name</td>
                </tr>";
            $info['menu_str'] = $tree->get_tree(0, $str);
            $info['groupid'] = $id;
            $this->assign($info);
            return $this->fetch();
        }
    }

}
