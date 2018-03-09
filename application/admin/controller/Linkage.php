<?php

// +----------------------------------------------------------------------
// | ZZCMS 菜单管理
// +----------------------------------------------------------------------
namespace zzcms\admin\controller;

use zzcms\common\controller\Admin;
use \think\Loader;
use \think\Db;

class Linkage extends Admin
{
    protected $model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = Loader::model('admin/Linkage');
    }

    public function lists()
    {
        $list = Db::name('linkage')->where(['keyid' => 0])->select();
        $this->assign('list', $list);
        return $this->fetch();
    }


    //联动菜单
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            if (empty($post)) {
                $this->error('数据不能为空');
            }
            $post['parentid'] = 0;
            if ($this->model->addData($post)) {
                $this->success("添加成功！", url('lists'));
            } else {
                $this->error($this->model->getError() . ":添加失败");
            }
        } else {
            return $this->fetch();
        }
    }

    // 编辑
    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();

            if (empty($post)) {
                $this->error('数据不能为空');
            }
            $post['old_parentid'] = 0;
            $post['parentid'] = 0;
            if ($this->model->editData($post)) {
                $this->success('修改成功', url('lists'));
            } else {
                $this->error("更新失败:" . $this->model->getError());
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $this->error('ID错误');
            }
            $this->assign(Db::name('linkage')->where(['linkageid' => $id])->find());
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

    // 菜单首页
    public function linkage_lists()
    {
        $keyid = $this->request->has('keyid') ? $this->request->param('keyid', 0, 'intval') : 0;
        if ($keyid < 1) {
            $this->error('ID错误');
        }
        $parentid = $this->request->has('parentid') ? $this->request->param('parentid', 0, 'intval') : 0;
        $list = Db::name('linkage')->where(['keyid' => $keyid, 'parentid' => $parentid])->order('listorder desc,linkageid asc')->select();
        foreach ($list as $k => $item) {
            $this->model->is_have_child($item['linkageid']) ? $list[$k]['is_have_child'] = 1 : $list[$k]['is_have_child'] = 0;
        }

        $this->assign("list", $list);
        $this->assign('keyid', $keyid);
        return $this->fetch();
    }

    /**
     * 排序
     */
    public function listorder()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            foreach ($post['listorders'] as $id => $listorder) {
                $this->model->where(['linkageid' => $id])->setField(['listorder' => intval($listorder)]);
            }
            $this->success('设置成功', url('linkage_lists', ['keyid' => 1]));
        } else {
            $this->redirect(url('index'));
        }
    }

    // 添加菜单
    public function linkage_add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            if (empty($post)) {
                $this->error('数据不能为空');
            }
            $status = $this->model->addData($post);
            if ($status) {
                $this->success('添加成功', url('linkage_lists', ['keyid' => $post['keyid']]));
            } else {
                $this->error($this->model->getError());
            }
        } else {
            $keyid = $this->request->has('keyid') ? $this->request->param('keyid', 0, 'intval') : 0;
            if ($keyid < 1) {
                $this->error('ID错误');
            }
            $linkageCache = cache('linkage_' . $keyid);
            $select_linkages = '';
            if (!empty($linkageCache)) {
                $tree = new \zzcms\util\Tree();
                $parentid = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
                if ($parentid > 0) {
                    foreach ($linkageCache as $k => $item) {
                        $item ['selected'] = $item ['linkageid'] == $parentid ? 'selected' : '';
                        $linkageCache[$k] = $item;
                    }
                }
                $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
                $tree->init($linkageCache);
                $select_linkages = $tree->get_tree(0, $str);
            }
            $this->assign("select_linkages", $select_linkages);
            $this->assign('keyid', $keyid);
            return $this->fetch();
        }
    }

    // 编辑
    public function linkage_edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            if (empty($post)) {
                $this->error('数据不能为空');
            }

            if ($this->model->editData($post)) {
                $this->success('修改成功', url('linkage_lists', ['keyid' => $post['keyid']]));
            } else {
                $this->error("更新失败:" . $this->model->getError());
            }
        } else {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if ($id < 1) {
                $this->error('ID错误');
            }
            $data = Db::name('linkage')->where(['linkageid' => $id])->find();
            if (empty($data)) {
                $this->error('信息不存在');
            }

            $keyid = (int)$data['keyid'];
            $linkageCache = cache('linkage_' . $keyid);
            $select_linkages = '';
            if (!empty($linkageCache)) {
                $tree = new \zzcms\util\Tree();
                $parentid = (int)$data['parentid'];
                if ($parentid > 0) {
                    foreach ($linkageCache as $k => $item) {
                        $item ['selected'] = $item ['linkageid'] == $parentid ? 'selected' : '';
                        $linkageCache[$k] = $item;
                    }
                }
                $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
                $tree->init($linkageCache);
                $select_linkages = $tree->get_tree(0, $str);
            }
            $this->assign("select_linkages", $select_linkages);
            $this->assign($data);
            return $this->fetch();
        }
    }

    // 删除
    public function linkage_delete()
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
     * 更新某一菜单下的缓存
     */
    public function create_cache()
    {
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if ($id < 1) {
            $this->error("ID非法！");
        }
        if ($this->model->create_cache_byid($id)) {
            $this->success("更新成功！");
        } else {
            $this->error("更新失败！");
        }
    }

    ################################################################重新定义 省-市-区 地区###############################################################################

    public function provinces()
    {
        $provinces = Db::name('linkage')->where(['keyid' => 1, 'parentid' => 0])->order('linkageid asc')->select();
        foreach ($provinces as $k => $item1) {
            $item1['fromid'] = $item1['linkageid'];
            $item1['siteid'] = 1;
            unset($item1['linkageid']);
            //Db::name('linkage2')->insert($item1);
        }
    }

    public function citys()
    {
        $caches = cache('linkages');
        $provinces = Db::name('linkage2')->where(['siteid' => 1, 'parentid' => 0, 'fromid' => ['gt', 0]])->order('linkageid asc')->select();
        $citylist = [];
        foreach ($provinces as $k => $item) {
            foreach ($caches as $k2 => $item2) {
                if ($item2['parentid'] == $item['fromid']) {
                    $item2['fromid'] = $item2['linkageid'];
                    $item2['siteid'] = 2;
                    $item2['parentid'] = $item['linkageid'];
                    unset($item2['linkageid']);
                    $citylist[] = $item2;
                }
            }
        }
        //Db::name('linkage2')->insertAll($citylist);
    }

    public function areas()
    {
        $caches = cache('linkages');
        $citys = Db::name('linkage2')->where(['siteid' => 2])->order('fromid asc,linkageid asc')->select();
        $arealist = [];
        foreach ($citys as $k => $item) {
            foreach ($caches as $k2 => $item2) {
                if ($item2['parentid'] == $item['fromid']) {
                    $item2['fromid'] = $item2['linkageid'];
                    $item2['siteid'] = 3;
                    $item2['parentid'] = $item['linkageid'];
                    unset($item2['linkageid']);
                    $arealist[] = $item2;
                }
            }
        }
        //Db::name('linkage2')->insertAll($arealist);
    }
    ################################################################重新定义 省-市-区 地区   end  ##########################################################################


}
