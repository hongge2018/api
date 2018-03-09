<?php

// +----------------------------------------------------------------------
// | ZZCMS 后台首页
// +----------------------------------------------------------------------
namespace zzcms\admin\controller;

use zzcms\common\controller\Admin;
use \think\Loader;
use \think\Db;

class Cache extends Admin
{
    public static $menuCache = [];

    protected function _initialize()
    {
        parent::_initialize();
        if (empty ($this->menuCache)) {
            $this->menu();
            $this->menuCache = zcache('menu');
        }
    }

    // 后台框架首页
    public function index()
    {
        // 顶部菜单数组的第一个值
        $top_menuCache = $this->topmenu();
        $first_top_menu = reset($top_menuCache);
        // 取得ID
        $id = $first_top_menu ['id'];
        $array = [];
        // 得到其子菜单
        foreach ($this->menuCache as $v) {
            if ($v ['parentid'] == $id) {
                $array [] = $v;
            }
        }
        $this->assign("default_left_menu", $array);
        $this->assign("topMenu", $this->topMenu());
        return $this->fetch();
    }

    // 生成菜单缓存
    public function menu()
    {
        $model = Loader::model('admin/Linkage');
        $where = [];
        // $where ['display'] = 1;
        $list = $model->where($where)->order('listorder')->select();
        $data = [];
        foreach ($list as $v) {
            $key = $v ['id'];
            $data [$key] = $v;
        }
        cache('menu', $data);
    }

    // 生成顶部菜单
    public function topMenu()
    {
        $menu = cache('menu');
        if (empty ($menu)) {
            $this->menu();
            $menu = cache('menu');
        }
        $data = [];
        foreach ($menu as $v) {
            $key = $v ['id'];
            if ($v ['parentid'] == 0) {
                $data [$key] = $v;
            }
        }
        cache('topMenu', $data);
    }

    // 生成地区缓存
    public function area()
    {
        $model = Loader::model('admin/Linkage');
        $list = $model->select();
        $data = [];
        foreach ($list as $v) {
            $key = $v ['areaid'];
            $data [$key] = $v;
        }
        cache('area', $data);
    }


    // 更新全部缓存
    public function create_all()
    {
        $this->menu();
        $this->topMenu();
        $this->area();;
        return $this->fetch();
    }
}
