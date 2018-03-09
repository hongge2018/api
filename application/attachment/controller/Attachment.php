<?php

namespace zzcms\attachment\controller;

use zzcms\common\controller\Home;
use \think\Loader;
use think\Request;
use \think\Db;

class Attachment extends Home
{
    protected $request;

    protected function _initialize()
    {
        $this->request = Request::instance();
    }

    public function upimg()
    {
        $post = $this->request->param();
        $ext = isset($post['ext']) ? $post['ext'] : 'jpg|jpeg|gif|png|bmp';//后缀，默认只能上传图片
        $info['ext'] = str_replace('|', ',', $ext);
        $info['name'] = $post['name'];
        $info['path_name'] = config('upload.path_name');
        $this->assign($info);
        return $this->fetch();
    }

    /**
     * 上传
     * @return \think\response\Json
     */
    public function upload_img()
    {
        //获取上传的文件
        $filelist = $this->request->file();
        $file = reset($filelist);
        //获取待上传文件的信息
        $fileinfo = $file->getInfo();
        //文件上传保存目录
        $upload_path = config('upload.path');
        /**
         * 计算待上传文件的MD5值，为了对比数据库是否已有此值，可避免重复上传，并且可以闪电上传
         */
        $md5 = md5_file($fileinfo['tmp_name']);
        $model = Loader::model('attachment/Attachments');
        //根据MD5值获取信息，查找数据库中是否已有文件
        $att_obj = $model->where(['md5' => $md5])->order('aid desc')->find();
        if (empty($att_obj)) {
            $att_info = [];
        } else {
            $att_info = $att_obj->toArray();
        }
        unset($att_obj);
        //现在时间
        $times = time();
        /**
         * 实例化上传
         */
        //是否存在数据
        if (empty($att_info)) {
            $info = $file->move($upload_path);// 移动到服务器的上传目录 并且使用原文件名
            if ($info) {
                $save_name = str_replace('\\', '/', $info->getSaveName());//保存的文件名（含部分路径）
                //写入到附件表
                $data = [];
                $data['module'] = 'life';//模块
                $data['filename'] = $fileinfo['name'];//文件名
                $data['filepath'] = $save_name;//文件路径
                $data['fileext'] = $this->get_ext($fileinfo['name']);//文件后缀
                $data['filesize'] = $fileinfo['size'];//文件大小
                $data['md5'] = $md5;//文件MD5值
                $data['isimage'] = 1;
                $data['userid'] = isset($this->userid) ? $this->userid : 0;
                $data['sellerid'] = isset($this->sellerid) ? $this->sellerid : 0;
                $data['create_time'] = $times;//时间
                $data['uploadip'] = $this->request->ip();//IP
                $aid = $model->addData($data);
                if (false === $aid) {
                    return json(['status' => 0, 'msg' => '写入信息失败']);
                } else {
                    return json(['status' => 1, 'pic' => $save_name, 'id' => $aid, 'name' => $info->getFilename()]);
                }
            } else {
                // 上传失败获取错误信息
                return json(['code' => 0, 'msg' => '上传文件失败']);
            }
        } else {
            //查询文件是否存在
            $src = $upload_path . $att_info['filepath'];
            if (!file_exists($src)) {
                $info = $file->move($upload_path);// 移动到服务器的上传目录 并且使用原文件名
                $save_name = str_replace('\\', '/', $info->getSaveName());//保存的文件名（含部分路径）
                $name = $att_info['filename'] = $fileinfo['name'];//文件名
                $att_info['filepath'] = $save_name;//文件路径
                /**
                 * 如果出现之前文件不存在情况，是否需要把此文件的所有记录的路径更新为最新路径
                 * 此做法有助于更新因删除文件导致的错误，利于补充缺失文件，如果，缺少哪个文件，随便在一个地方上传即可
                 */
            } else {
                $save_name = $att_info['filepath'];//保存的文件名（含部分路径）
                $name = $att_info['filename'];
            }
            //查询是否是本人上传
            $att_info['userid'] = isset($this->userid) ? $this->userid : 0;
            $att_info['sellerid'] = isset($this->sellerid) ? $this->sellerid : 0;
            $att_info['create_time'] = $times;//时间
            $att_info['uploadip'] = $this->request->ip();//IP
            unset($att_info['aid']);//删除主键
            $aid = $model->addData($att_info);
            if (false === $aid) {
                return json(['status' => 0, 'msg' => '写入信息失败']);
            } else {
                return json(['status' => 1, 'pic' => $save_name, 'id' => $aid, 'name' => $name]);
            }
        }
    }

    /**
     * 裁剪图片
     * @return mixed
     */
    public function crop()
    {
        $post = $this->request->param();
        $ext = isset($post['ext']) ? $post['ext'] : 'jpg|jpeg|gif|png|bmp';//后缀，默认只能上传图片
        $info['ext'] = str_replace('|', ',', $ext);
        //$info['name'] = $post['name'];
        $info['path_name'] = config('upload.path_name');
        $this->assign($info);
        return $this->fetch();
    }

    /**
     * 上传裁剪图片
     * @return \think\response\Json
     */
    public function crop_img()
    {
        //获取上传的文件
        $filelist = $this->request->file();
        //上传文件列表
        $file = reset($filelist);
        //获取待上传文件的信息
        $fileinfo = $file->getInfo();
        $post = $this->request->param();
        if (!isset($post['avatar_data']) || empty($post['avatar_data'])) {
            return ['state' => 0, 'message' => '请先裁剪图片'];
        }

        $data_json = htmlspecialchars_decode($post['avatar_data']);
        //裁剪参数
        $cropinfo = json_decode($data_json, true);
        $image = \think\Image::open($fileinfo['tmp_name']);
        //裁剪图保存的路径和名字
        $save_dir = config('upload.path') . date('Ymd');

        if (!file_exists($save_dir)) {
            mkdir($save_dir, 0777, true);
        }

        $save_filename = uniqid() . '.' . $this->get_ext($fileinfo['name']);
        $image->crop($cropinfo['width'], $cropinfo['height'], $cropinfo['x'], $cropinfo['y'], 400, 400)->save($save_dir . '/' . $save_filename);
        return ['state' => 200, 'result' => date('Ymd') . '/' . $save_filename, 'message' => '裁剪完成'];
    }

    protected function get_ext($filename)
    {
        $info = pathinfo($filename);
        return $info['extension'];
    }


    /**
     * 提交注册信息
     * 解密提交上来的加密信息，合并后一并提交
     * @return mixed
     */
    public function weixin()
    {
        $post = $this->request->param();
        $ext = isset($post['ext']) ? $post['ext'] : 'jpg|jpeg|gif|png|bmp';//后缀，默认只能上传图片
        $info['ext'] = str_replace('|', ',', $ext);
        $info['maxnum'] = 30;
        $info['name'] = $post['name'];
        $info['path_name'] = config('upload.path_name');
        $this->assign($info);
        return $this->fetch();
    }

    /**
     * 上传
     * @return \think\response\Json
     */
    public function weixin_upload_img()
    {
        //获取上传的文件
        $filelist = $this->request->file();
        $file = reset($filelist);
        //获取待上传文件的信息
        $fileinfo = $file->getInfo();
        //文件上传保存目录
        $upload_path = config('upload.path');
        /**
         * 计算待上传文件的MD5值，为了对比数据库是否已有此值，可避免重复上传，并且可以闪电上传
         */
        $md5 = md5_file($fileinfo['tmp_name']);
        $model = Loader::model('attachment/Attachments');
        //根据MD5值获取信息，查找数据库中是否已有文件
        $att_obj = $model->where(['md5' => $md5])->find();
        if (empty($att_obj)) {
            $att_info = [];
        } else {
            $att_info = $att_obj->toArray();
        }

        unset($att_obj);
        //现在时间
        $times = time();
        /**
         * 实例化上传
         */

        //是否存在数据
        if (empty($att_info)) {
            $info = $file->move($upload_path);// 移动到服务器的上传目录 并且使用原文件名
            if ($info) {
                $save_name = str_replace('\\', '/', $info->getSaveName());//保存的文件名（含部分路径）
                //写入到附件表
                $data = [];
                $data['module'] = 'life';//模块
                $data['filename'] = $fileinfo['name'];//文件名
                $data['filepath'] = $save_name;//文件路径
                $data['fileext'] = $this->get_ext($fileinfo['name']);//文件后缀
                $data['filesize'] = $fileinfo['size'];//文件大小
                $data['md5'] = $md5;//文件MD5值
                $data['isimage'] = 1;
                $data['userid'] = isset($this->userid) ? $this->userid : 0;
                $data['sellerid'] = isset($this->sellerid) ? $this->sellerid : 0;
                $data['create_time'] = $times;//时间
                $data['uploadip'] = $this->request->ip();//IP
                $aid = $model->addData($data);
                if (false === $aid) {
                    return json(['code' => 0, 'msg' => $model->getError()]);
                } else {
                    return json(['code' => 1, 'msg' => '上传成功', 'data' => ['src' => $save_name, 'name' => $fileinfo['name']]]);
                }
            } else {
                // 上传失败获取错误信息
                return json(['code' => 0, 'msg' => '上传文件失败']);
            }
        } else {
            //查询文件是否存在
            $src = $upload_path . $att_info['filepath'];
            if (!file_exists($src)) {
                $info = $file->move($upload_path);// 移动到服务器的上传目录 并且使用原文件名
                $save_name = str_replace('\\', '/', $info->getSaveName());//保存的文件名（含部分路径）
                $name = $att_info['filename'] = $fileinfo['name'];//文件名
                $att_info['filepath'] = $save_name;//文件路径
                //设置已调用
            } else {
                $save_name = $att_info['filepath'];//保存的文件名（含部分路径）
                $name = $att_info['filename'];
            }
            //查询是否是本人上传
            $att_info['userid'] = isset($this->userid) ? $this->userid : 0;
            $att_info['sellerid'] = isset($this->sellerid) ? $this->sellerid : 0;
            $att_info['create_time'] = $times;//时间
            $att_info['uploadip'] = $this->request->ip();//IP
            unset($att_info['aid']);//删除主键
            $aid = $model->addData($att_info);
            if (false === $aid) {
                return json(['code' => 0, 'msg' => $model->getError()]);
            } else {
                return json(['code' => 1, 'msg' => '上传成功', 'data' => ['src' => $save_name, 'name' => $name]]);
            }
        }
    }


    /**
     * BASE64上传
     * 主要用移动端上传
     */
    public function baseimg($imgString)
    {
        /**
         * 用正则解析base64编码,如果成功，得到的结果如下:
         * array(3) {
         *   [0] => string(22) "data:image/png;base64,"
         *   [1] => string(22) "data:image/png;base64,"
         *   [2] => string(3) "png"
         * }
         */
        if (!empty($imgString) && preg_match('/^(data:\s*image\/(\w+);base64,)/', $imgString, $result)) {
            $ext = $result[2];
            //生成当天日期字符串
            $today = date('Ymd', time());
            //文件上传目录
            $upload_path = config('upload.path') . $today;
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777);
            }
            //文件路径名
            $filename = uniqid() . '.' . $ext;
            if (file_put_contents($upload_path . '/' . $filename, base64_decode(str_replace($result[1], '', $imgString)))) {
                return $today . '/' . $filename;
            } else {
                return '';
            }
        }
        return '';
    }

    /**
     * 图片删除
     * 先查询是否图片是否公用，如果公用，只删除数据库记录即可
     * @return bool
     */
    public function img_del()
    {
        $post = $this->request->param();
        $aid = intval($post['aid']);
        /**
         *
         * 根据要删除的文件名查询要删除的项,假如传值过来的是aid
         */
        $model = Loader::model('attachment/Attachments');
        $info = $model->where(['aid' => $aid])->find();
        if (empty($info)) {
            return ['stauts' => 0, 'msg' => '要删除的文件不存在'];
        }
        /**
         * 查询是否此文件是否有其他占用,如果未占用则需要删除此信息，并且删除文件，否则只删除本条信息
         */
        $ainfo = $model->where(['md5' => $info['md5']])->find();
        if (false === $model->deleteData($aid)) {
            return ['stauts' => 0, 'msg' => '删除信息失败'];
        }
        /**
         * 删除文件
         */
        if (empty($ainfo)) {
            $src = config('upload.path') . $ainfo('filepath');
            if (file_exists($src)) {
                if (false === unlink($src)) {
                    return ['stauts' => 0, 'msg' => '删除文件失败'];
                }
            }
        }
        return ['stauts' => 1, 'msg' => '删除成功'];
    }


}
