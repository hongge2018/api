<?php
namespace zzcms\admin\model;

use zzcms\common\model\Base;

class Menu extends Base
{
    protected $name = 'menu';
    protected $child_node = [];


    /**添加
     * @param $data
     * @return bool
     */
    public function addInfo(array $data)
    {
        $id = $this->isUpdate(false)->allowField(true)->save($data);
        if ($id) {
            $this->createCache();
            return $id;
        } else {
            $this->error = '添加失败！';
            return false;
        }
    }

    /**
     * 编辑
     *
     * @param type $data
     *            提交数据
     * @return boolean
     */
    public function editInfo($data, $id = 0)
    {
        if (empty ($data)) {
            $this->error = '数据不能为空！';
            return false;
        }
        $id = $id ? $id : (int)$data[$this->pk];
        if (!$id) {
            $this->error = 'ID不能为空！';
            return false;
        }
        $info = $this->get($id);
        if (empty ($info)) {
            $this->error = '信息不存在！';
            return false;
        }
        $data [$this->pk] = $id;
        if (false !== $this->isUpdate(true)->allowField(true)->save($data)) {
            $this->createCache();
            return $id;
        } else {
            $this->error = '更新失败！';
            return false;
        }
    }


    /**删除
     * @param $id
     * @return bool
     */
    public function deleteInfo($id)
    {
        if (empty ($id)) {
            $this->error = 'ID不存在！';
            return false;
        }
        $info = $this->where([$this->pk => $id])->find();
        if (empty($info)) {
            $this->error = '您要删除的信息不存在！';
            return false;
        }
        //查询其下属所有分类
        $deleteid = $this->getChildNode($id);
        if (count($deleteid) > 1) {
            $this->error = '此菜单下有子类，不允许删除！';
            return false;
        }

        $status = $this->where(['id' => $id])->delete();
        if (false === $status) {
            $this->error = '删除信息错误！';
            return false;
        } else {
            $this->createCache();
            return true;
        }
    }

    /**
     * 更新缓存
     *
     * @param type $data
     * @return type
     */
    public function createCache($options = ['type' => 'all'])
    {
        $list = $this->order('listorder asc')->select();
        if (empty($list)) {
            return false;
        }
        //转换为数组
        $list = collection($list)->toArray();
        $cache = $root_menu = [];
        foreach ($list as $item) {
            $pk = (int)$item['id'];
            $cache[$pk] = $item;
            if ($item['parentid'] == 0 && $item['display'] == 1) {
                $root_menu[$pk] = $item;
            }
        }
        unset($list);
        cache('menu', $cache);
        cache('root_menu', $root_menu);
        //默认返回所有菜单
        return $options['type'] == 'all' ? $cache : $root_menu;
    }

    /**
     * 递归查询下属菜单
     * @param $id   要查询的菜单ID
     * @return array
     */
    private function getChildNode($id)
    {
        $this->child_node[] = intval($id);
        $list = $this->where(['parentid' => $id])->select();
        if (is_array($list)) {
            foreach ($list as $item) {
                $this->get_childnode($item['id']);
            }
        }
        return $this->child_node;
    }

}