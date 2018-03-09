<?php
namespace zzcms\common\controller;

use think\Controller;
use think\Cookie;
use think\Loader;
use think\Request;
use think\Db;
use think\Session;

class Admin extends Controller
{
    protected $admin_id = 0;
    protected $admin_info = [];
    protected $menu_id;
    protected $request;

    protected function _initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
        $this->admin_info = Session::has('admininfo') ? Session::get('admininfo') : [];
        if (empty($this->admin_info)) {
            $this->error('亲，请先登录吧', url('common/login'));
        }
        $this->admin_id = $this->admin_info['id'];
        //实例化工具类
        $this->request = Request::instance();
        $this->menu_id = $this->request->has('menuid') ? $this->request->param('menuid', 0, 'intval') : 0;
        $info = ['admin_name' => $this->admin_info['username']];
        $info['submenu'] = $this->subMenu($this->menu_id);
        $info['is_submenu'] = 1;
        $this->assign($info);
    }

    /**
     * 检查用户权限
     */
    public function checkAuth($ruleid)
    {
        if (in_array(strtolower($this->request->controller()), ['index'])) {
            return true;
        }
        if (in_array(strtolower($this->request->action()), ['get_left_menu'])) {
            return true;
        }
        if ($ruleid == 1) {
            return true;
        }
        $rules_str = Db::name('auth_group')->where(['id' => $ruleid])->value('rules');
        if (empty($rules_str)) {
            return false;
        }
        $rules = explode(',', $rules_str);
        if (!is_array($rules)) {
            return false;
        }
        //查询此次请求的地址
        $menuid = Db::name('menu')->where(['controller' => $this->request->controller(), 'action' => $this->request->action()])->value('id');
        if (empty($menuid)) {
            return false;
        }
        return in_array($menuid, $rules);
    }


    /**
     * 菜单
     * @param $parentid 父ID
     * @param int $with_self 是否包含自己
     */
    final public static function adminMenu($parentid, $with_self = 0)
    {
        // 菜单缓存
        $menu_cache = cache('menu');
        //如果缓存不存在，则生成缓存
        if (empty($menu_cache)) {
            $menu_cache = Loader::model('admin/Menu')->createCache();
        }
        //下级菜单，本菜单初始化
        $array = $array2 = [];
        foreach ($menu_cache as $k => $item) {
            if ($item['parentid'] == $parentid && $item['project1'] == 1) {
                $array[$k] = $item;
            }
        }
        //如果包含本菜单，则合并数组
        if ($with_self == 1 && $parentid != 0) {
            $array2[] = isset($menu_cache[$parentid]) ? $menu_cache[$parentid] : [];
            $array = array_merge($array2, $array);
        }
        return $array;
    }

    //子菜单
    public function subMenu($parentid = '', $big_menu = false)
    {
        $parentid = intval($parentid);
        //如果没有获取到菜单ID，则重新获取
        if ($parentid < 1) {
            $parentid = (int)Db::name('menu')->where(['app' => strtolower($this->request->module()), 'controller' => strtolower($this->request->controller()), 'action' => strtolower($this->request->action())])->value('id');
            $this->menu_id = $parentid;
        }
        $array = self::adminMenu($parentid, 1);
        if (empty($array) || empty($array[0])) {
            return '';
        }
        $str = '';
        foreach ($array as $item) {
            if ($item['parentid'] == 0 || empty($item['app'])) {
                continue;
            }
            //是否有附加参数，只允许添加一个附加参数
            //$param = empty ($item ['data']) ? [] : explode(' = ', $item['data']);
            $classname = strtolower($this->request->module()) == strtolower($item['app']) && strtolower($this->request->controller()) == strtolower($item['controller']) && strtolower($this->request->action()) == strtolower($item['action']) ? 'class="on"' : '';
            $param = ['menuid' => $parentid];
            //参数处理
            if (!empty ($item['data'])) {
                $params = explode('=', $item['data']);
                $param[$params[0]] = $params[1];
            }
            if ($classname) {
                $str .= "<a href='javascript:;' $classname><em>" . $item ['name'] . "</em></a><span>|</span>";
            } elseif ($parentid == $item ['id'] || $parentid == $item ['parentid'] && 1 == $item ['display']) {
                $str .= "<a href='" . url($item ['app'] . '/' . $item ['controller'] . '/' . $item ['action'], $param) . "'><em>" . $item ['name'] . "</em></a><span>|</span>";
            }
        }
        //去除结束的字符：<span>|</span>
        $str = substr($str, 0, -14);
        $this->assign('menuid', $parentid);
        return empty ($str) ? '' : '<div class="subnav" ><div class="content-menu ib-a blue line-x">' . $str . '</div ></div >';
    }


}
