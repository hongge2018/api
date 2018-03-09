<?php

namespace zzcms\admin\controller;

use zzcms\common\controller\Admin;
use \think\Loader;

class App extends Admin
{
    protected $model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = Loader::model('admin/App');
    }

    // 网站基本设置
    public function index()
    {
        $list = $this->model->select();
        $this->assign('lists', $list);
        return $this->fetch();
    }

    // 添加菜单
    public function publish()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            if (empty($post)) {
                $this->error('数据不能为空');
            }
            //验证  唯一规则： 表名，字段名，排除主键值，主键名
            $validate = new \think\Validate([
                ['app_name', 'require', '应用名称不能为空'],
                ['app_id', 'require', 'AppId不能为空'],
                ['app_secret', 'require', 'AppSecret不能为空'],
            ]);
            //验证数据合法性
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }
            $id = isset($post['id']) ? intval($post['id']) : 0;
            if ($id < 1) {
                $status = $this->model->addData($post);
            } else {
                $status = $this->model->editData($post);
            }
            if ($status) {
                $this->success('操作成功', url('index'));
            } else {
                $this->error($this->model->getError());
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;

            $info = [];
            if ($id > 0) {
                $info = $this->model->getOne($id);
            } else {
                $info['app_id'] = random(8, 'number');
                $info['app_secret'] = random(32);
            }
            $this->assign($info);
            return $this->fetch();
        }
    }


}
