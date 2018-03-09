<?php
namespace zzcms\home\controller;

use think\Db;
use zzcms\common\controller\Home;
use think\Cookie;


class Common extends Home
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->assign('catelist', $this->get_catelist('tuan'));
    }


    /**
     * 注册
     * @return mixed
     */
    public function reg()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();

            //验证  唯一规则： 表名，字段名，排除主键值，主键名
            $validate = new \think\Validate([
                ['__token__', 'require|token', '您的请求不正确'],
                ['password', 'require', '密码不能为空'],
            ]);
            //验证数据合法性
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            $member = \think\Loader::model('common/Member');
            $id = $member->addData($post);
            if ($id === false) {
                $this->error('注册失败：' . $member->getError());
            } else {
                $this->success('注册成功', url('common/login'));
            }
        } else {
            $url = $this->request->get('url') ? $this->request->param('url') : '';
            $this->assign('url', $url);
            return $this->fetch();
        }
    }

    /**
     * 登录
     * @return mixed
     */
    public function login()
    {
        if ($this->request->isPost()) {
            $post = $this->request->param();
            $info = Db::name('member')->where(['username' => $post['username']])->find();
            if (empty($info)) {
                $this->error('用户名不存在');
            }
            if ($info['password'] != md5($post['password'])) {
                $this->error('您输入的密码不正确');
            }
            //保存COOKIE信息
            unset($info['password']);
            Cookie::set('userinfo', $info);
            $this->redirect('user/index/index');
        } else {
            return $this->fetch();
        }
    }

    /**
     * 退出成功
     */
    public function logout()
    {
        Cookie::delete('userinfo');
        $this->redirect('index/index');
    }


}
