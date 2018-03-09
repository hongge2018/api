<?php

namespace zzcms\admin\controller;

use think\Db;
use think\Loader;
use zzcms\common\controller\Admin;

class Menu extends Admin
{
    protected $model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = Loader::model('admin/Menu');
    }

    // 菜单首页
    public function index()
    {
        $result = createCache('menu');
        $tree = new \zzcms\util\Tree();
        $tree->icon = ['&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ '];
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<div class="layui-btn-group"><a class="layui-btn layui-btn-normal publish" data-url="' . url("publish", array("id" => $r['id'], 'menuid' => $this->menu_id)) . '">修改</a>';
            $r['str_manage'] .= '<a class="layui-btn layui-btn-danger delete" data-id="' . $r['id'] . '">删除</a></div>';
            $r['display'] = $r['display'] ? "<i class=\"layui-icon icon_style\">&#xe616;</i> " : "<i class=\"layui-icon icon_style\">&#x1007;</i> ";
            $array [] = $r;
        }
        $tree->init($array);
        $str = "<tr id='tr\$id'>
                    <td><input name='listorders[\$id]' type='text' size='1' value='\$listorder' class='layui-input'></td>
                    <td>\$id</td>
                    <td>\$spacer\$name</td>
                    <td>\$controller/\$action</td>
                    <td>\$display</td>
                    <td>\$str_manage</td>
                 </tr>";

        $categorys = $tree->get_tree(0, $str);
        $this->assign("categorys", $categorys);
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
                ['name', 'require', '名称不能为空'],
                ['app', 'require', '模块名不能为空'],
                ['controller', 'require', '文件名不能为空'],
                ['action', 'require', '方法名不能为空'],
            ]);
            //验证数据合法性
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            $id = isset($post['id']) ? intval($post['id']) : 0;
            if ($id < 1) {
                $status = $this->model->addData($post, 'create');
            } else {
                $status = $this->model->editData($post);
            }
            if ($status) {
                $this->model->createCache();
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
                $info['parentid'] = 0;
            }

            $menu_cache = createCache('menu');
            foreach ($menu_cache as $item) {
                $item['selected'] = $item['id'] == $info['parentid'] ? 'selected' : '';
                $array [] = $item;
            }
            unset($menu_cache);
            $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
            $tree = new \zzcms\util\Tree();
            $tree->init($array);
            $info['select_categorys'] = $tree->get_tree(0, $str);
            $this->assign($info);
            return $this->fetch();
        }
    }


    // 删除
    public function delete()
    {
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if ($id < 1) {
            return $this->error("ID非法！");
        }
        if ($this->model->deleteInfo($id)) {
            return $this->success("删除成功！");
        } else {
            return $this->error($this->model->getError());
        }
    }

    /**
     * 排序
     */
    public function listorder()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            foreach ($post['listorders'] as $id => $listorder) {
                $this->model->where(['id' => $id])->setField(['listorder' => intval($listorder)]);
            }
            $this->success('设置成功', url('index'));
        } else {
            $this->redirect(url('index'));
        }
    }

    /**
     * 根据 menuid 获取后台左侧下级菜单
     */
    public function leftMenu()
    {
        $menuid = $this->request->has('menuid') ? $this->request->param('menuid', 0, 'intval') : 0;// 获取父级menuid
        if ($menuid < 1) {
            $this->error('参数不能为空');
        }
        return $this->menuChildList($menuid);
    }


    /**
     * 获取下属菜单
     * @param $menuid
     * @return array
     */
    public function menuChildList($menuid)
    {
        // 菜单缓存
        $menulist = createCache('menu');
        //生成树形结构
        $menu_tree = $this->getTree($menulist);
        $menulist = isset($menu_tree[$menuid]) ? $menu_tree[$menuid] : '';
        if (empty($menulist)) {
            return ['code' => 0, 'msg' => '菜单不存在'];
        }
        //所有下属菜单列表
        $childlists = isset($menulist['child']) ? $menulist['child'] : '';
        if (empty($childlists)) {
            return ['code' => 0, 'msg' => '不存在下属菜单'];
        }
        $str = '';
        $i = 0;
        foreach ($childlists as $item) {
            if ($i == 0) {
                $str .= '<li class="layui-nav-item layui-nav-itemed">';
            } else {
                $str .= '<li class="layui-nav-item">';
            }
            if (isset($item['child'])) {
                $str .= '<a class="" href="javascript:;">' . $item['name'] . '</a>';
                $str .= '<dl class="layui-nav-child">';
                foreach ($item['child'] as $childinfo) {
                    $mid = $childinfo['id'];
                    $param = ['menuid' => $mid];
                    //参数处理
                    if (!empty($childinfo['data'])) {
                        $params = explode('=', $childinfo['data']);
                        $param[$params[0]] = $params[1];
                    }
                    $str .= '<dd class=""><a data-url="' . url($childinfo['app'] . '/' . $childinfo['controller'] . '/' . $childinfo['action'], $param) . '">' . $childinfo['name'] . '</a></dd>';
                }
                $str .= '</dl>';
            } else {
                $mid = $item['id'];
                $param = ['menuid' => $mid];
                //参数处理
                if (!empty($item['data'])) {
                    $params = explode('=', $item['data']);
                    $param[$params[0]] = $params[1];
                }
                $str .= '<a data-url="' . url($item['app'] . '/' . $item['controller'] . '/' . $item['action'], $param) . '">' . $item['name'] . '</a>';
            }
            $str .= '</li>';
            $i++;
        }
        return ['code' => 1, 'msg' => $str];
    }

    /*
     * 格式化树形结构
     */
    function getTree($items)
    {
        $tree = []; //初始化格式化好的树
        foreach ($items as $item) {
            $mid = (int)$item['id'];
            if (isset($items[$item['parentid']])) {
                $items[$item['parentid']]['child'][$mid] = &$items[$mid];
            } else {
                $tree[$mid] = &$items[$mid];
            }
        }
        return $tree;
    }
}
