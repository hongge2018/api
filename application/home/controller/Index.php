<?php
namespace zzcms\home\controller;

use think\Db;
use zzcms\common\controller\Home;


class Index extends Home
{

    // 初始化
    protected function _initialize()
    {
        parent::_initialize();


    }

    public function index()
    {
        $this->redirect('admin/common/login');
    }

    /**
     * 会员邀请
     * 业务流程，根据访问地址获取邀请人的ID，然后以此ID为参数跳转到注册页面
     * 在注册的时候，判断是否有邀请人，如果有则写入到数据表中（referrals）
     */
    public function share()
    {
        $url = $this->request->has('url') ? $this->request->param('url') : '';
        if (empty($url)) {
            $this->error('非法链接', url('index'));
        }
        $uid = intval(base64_decode($url));
        if ($uid < 1) {
            $this->error('非法链接', url('index'));
        }
        $this->redirect('member/common/reg', ['share_id' => $uid]);

    }

    /**
     * 搜索跳转
     */

    public function search()
    {
        $post = $this->request->param();
        switch ($post['module']) {
            case 'goods':
                $url = 'life/goods/lists';
                break;
            case 'tuan':
                $url = 'life/tuan/index';
                break;
            case 'event':
                $url = 'life/events/index';
                break;
            case 'seller':
                $url = 'life/stores/index';
                break;
            default:
                $url = 'home/index/index';
        }
        $this->redirect($url, ['kerword' => urlencode($post['keyword'])]);
    }

}
