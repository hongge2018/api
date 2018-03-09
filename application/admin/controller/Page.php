<?php

namespace zzcms\admin\controller;

use zzcms\common\controller\Admin;
use \think\Loader;
use \think\Db;

class Page extends Admin
{
    protected $model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = Loader::model('admin/Page');
    }

    public function lists()
    {
        $this->assign('list', Db::name('page')->order('listorder desc,id desc')->paginate(config('paginate.list_rows')));
        return $this->fetch();
    }

    /*
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            if ($this->model->addData($post)) {
                $this->success("添加成功！", url("lists"));
            } else {
                $this->error("添加失败！:" . $this->model->getError());
            }
        } else {
            return $this->fetch();
        }
    }

    /*
     * 编辑
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            if ($this->model->editData($post)) {
                $this->success("修改成功！", url("lists"));
            } else {
                $this->error("验证失败！:" . $this->model->getError());
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $this->error('非法参数');
            }
            $this->assign(Db::name('page')->find($id));
            return $this->fetch();
        }
    }

    // 删除
    public function delete()
    {
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if ($id < 1) {
            $this->error("ID非法！");
        }
        if ($this->model->deleteData($id)) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 排序
     */
    public function listorder()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            foreach ($post['listorders'] as $id => $listorder) {
                $this->model->where(['id' => intval($id)])->setField(['listorder' => intval($listorder)]);
            }
            $this->success('设置成功', url('lists'));
        } else {
            $this->redirect(url('lists'));
        }
    }
}