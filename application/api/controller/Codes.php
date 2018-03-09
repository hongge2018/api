<?php
namespace zzcms\api\controller;

use think\Request;
use think\captcha\Captcha;

/**
 * 验证码管理
 * Class Codes
 * @package zzcms\api\controller
 */
class Codes extends \think\Controller
{
    protected $request;

    protected function _initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
        $this->request = Request::instance();
    }

    /**
     * 生成 验证码
     * 用法 ：
     * @param string $id
     * @return \think\Response
     */
    public function index()
    {
        ob_end_clean();
        $captcha = new \zzcms\util\Captcha();
        if ($this->request->has('height')) {
            $captcha->imageH = $this->request->param('height');
        }
        if ($this->request->has('width')) {
            $captcha->imageW = $this->request->param('width');
        }
        if ($this->request->has('fontsize')) {
            $captcha->fontSize = $this->request->param('fontsize');
        }
        if ($this->request->has('len')) {
            $captcha->length = $this->request->param('len');
        }
        $captcha->fontttf = '2.ttf';
        return $captcha->entry('code');
    }
}
