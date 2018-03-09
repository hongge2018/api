<?php
namespace zzcms\admin\controller;

use zzcms\common\controller\Admin;
use think\Loader;
use \think\Db;


class Index extends Admin
{
    protected function _initialize()
    {
        parent::_initialize();

    }

    // 后台框架首页
    public function index()
    {
        //顶部菜单，也是一级菜单
        $root_menu = $this->rootMenu();
        //获取顶部菜单中的第一个菜单，以便取出其ID，用来生成默认的左侧菜单
        $first_menu = reset($root_menu);
        $info = ['root_menu' => $root_menu];
        //根据ID值获取生成的菜单内容
        $menu_info = Loader::controller('admin/Menu')->menuChildList($first_menu['id']);
        //获取菜单内容
        $info['default_left_menu'] = $menu_info['msg'];
        $this->assign($info);
        return $this->fetch();
    }


    // 更新全部缓存
    public function updateAllCache()
    {
        $this->update_all_cache();
        $this->success('更新完成', U('main'));
    }


    /**
     * 顶部菜单
     * @return mixed
     */
    public function rootMenu()
    {
        $root_menu = cache('root_menu');
        if (empty($root_menu)) {
            return Loader::model('admin/Menu')->createCache(['type' => 'root']);
        }
        return $root_menu;

    }

    // 顶部菜单
    public function menuLeft()
    {
        $menuid = $this->request->has('menuid') ? $this->request->param('menuid', 0, 'intval') : 0;// 获取父级menuid
        if ($menuid < 1) {
            $this->error('菜单ID不能为空');
        }
        return Loader::controller('admin/Menu')->menuChildList($menuid);
    }

    // 右侧默认页面
    public function main()
    {
        return $this->fetch();
    }


    // 后台框架首页菜单搜索
    public function publicFind()
    {
        $keyword = $this->request->param('keyword');
        if (!$keyword) {
            $this->error("请输入需要搜索的关键词！");
        }
        $where = [];
        $where ['name'] = ["LIKE", "%$keyword%"];
        $where ['status'] = ["EQ", 1];
        $where ['type'] = ["EQ", 1];
        $data = Db::name('menu')->where($where)->select();
        $menu_data = $menu_name = [];
        $module = createCache("module");
        foreach ($data as $k => $item) {
            $menu_data [ucwords($item['app'])] [] = $item;
            $menu_name [ucwords($item['app'])] = $module [ucwords($item['app'])] ['name'];
        }
        $this->assign("menu_data", $menu_data);
        $this->assign("menu_name", $menu_name);
        $this->assign("keyword", $keyword);
        return $this->fetch();
    }
}
