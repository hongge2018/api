<?php
namespace zzcms\attachment\model;

use think\Model;

class Attachments extends Model
{
    protected $name = 'attachment';
    protected $pk = 'aid';
    protected $createTime = 'create_time';


    // 初始化
    protected function initialize()
    {
        parent::initialize();

    }


    /**添加
     * @param $data
     * @return bool
     */
    public function addData(array $data)
    {
        $id = $this->isUpdate(false)->allowField(true)->save($data);
        if ($id) {
            return $this->aid;
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
    public function editData($data, $id = 0)
    {
        if (empty ($data)) {
            return false;
        }
        $id = $id ? $id : ( int )$data [$this->pk];
        if (!$id) {
            $this->error = 'ID不能为空！';
            return false;
        }
        $info = $this->get($id);
        if (empty ($info)) {
            $this->error = '该信息不存在！';
            return false;
        }
        $data [$this->pk] = $id;
        if (false !== $this->isUpdate(true)->allowField(true)->save($data)) {
            return true;
        } else {
            $this->error = '更新失败！';
            return false;
        }

    }

    /**删除
     * @param $id
     * @return bool
     */
    public function deleteData($id)
    {
        if (empty ($id)) {
            $this->error = 'ID不存在！';
            return false;
        }
        $info = $this->get($id);
        if (empty($info)) {
            $this->error = '您要删除的信息不存在！';
            return false;
        }
        $num = $this->destroy($id);
        if ($num != 1) {
            $this->error = '删除信息错误！';
            return false;
        } else {
            return true;
        }
    }

}