<?php
use \think\Loader;

// 应用公共文件
/**
 * 数组转对象
 * @param $array
 * @return StdClass
 */
function array2object($array)
{
    if (is_array($array)) {
        $obj = new StdClass();
        foreach ($array as $key => $val) {
            $obj->$key = $val;
        }
    } else {
        $obj = $array;
    }
    return $obj;
}


/**对象转数组
 * @param $object
 * @return array
 */
function object2array($object)
{
    $array = [];
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            $array[$key] = $value;
        }
    } else {
        $array = $object;
    }
    return $array;
}

/**
 * 文本区域转换
 * @param unknown $str
 * @return mixed
 */
function areatext2html($str)
{
    $str = htmlspecialchars($str);
    $str = str_replace("\r\n", "<br>", $str);
    $str = str_replace("\r", "<br>", $str);
    $str = str_replace("\n", "<br>", $str);
    return $str;
}

/**
 * 还原文本区域转换
 * @param unknown $str
 * @return mixed
 */
function html2areatext($str)
{
    $str = str_replace("<br>", "\r\n", $str);
    $str = htmlspecialchars_decode($str);
    return $str;
}


/**
 * 检测验证码是否正确
 * @param $mobile  要验证的手机号
 * @param array $options 【phonecode：手机验证码  active_time：默认有效时间，默认为 3600秒  error_num：默认错误次数 】
 * @return array
 */
function checkSms($mobile, $options = [])
{
    /**
     * 检测验证码是否正确
     * 1、先查询数据表中是否存在此手机号码并且未用的验证码
     * 2、判断是否超过最大错误次数
     * 2、判断验证码是否过期
     * 3、判断验证码是否正确
     *
     */
    $db = \think\Db::name('sms');
    $info = $db->where(['mobile' => $mobile, 'is_use' => 0])->order('id desc')->find();

    if (empty($info)) {
        return ['status' => 0, 'msg' => '亲，您还未获取验证码'];
    }
    $active_time = isset($options['time']) ? intval($options['time']) : 3600;//有效时间，默认为 3600秒
    $error_num = isset($options['error_num']) ? intval($options['error_num']) : 3;//错误次数 默认3
    $end_time = $info['create_time'] + $active_time;
    if ($end_time < time()) {
        $db->where(['id' => $info['id']])->setField(['is_use' => 1]);
        return ['status' => 0, 'msg' => '验证码已过期'];
    }
    if ($info['error_num'] >= $error_num) {
        $db->where(['id' => $info['id']])->setField(['is_use' => 1]);
        return ['status' => 0, 'msg' => '已超过验证码允许最大错误次数，请重新获取'];
    }
    if ($info['code'] != $options['phonecode']) {
        $db->where(['id' => $info['id']])->setInc('error_num');
        return ['status' => 0, 'msg' => '验证码错误'];
    }
    $db->where(['id' => $info['id']])->setField(['is_use' => 1]);
    return ['status' => 1, 'msg' => '验证成功'];
}

/**
 * 生成二维数组排列组合,主要生成商品的所有属性组合
 * @param $arrs 二维数组
 * @return mixed
 */
function combinationArr($arrs)
{
    $num = 1;
    foreach ($arrs as $k => $v) {
        $num *= count($v);
    }
    $arr_num = $num;
    $result = [];
    foreach ($arrs as $key => $v_list) {
        $v_num = count($v_list);
        $v_num = max($v_num, 1);

        //$v_list中的元素在排列组合中出现的最大重复次数
        $arr_num = $arr_num / $v_num;
        $position = 0;
        // 开始对$v_list进行循环
        foreach ($v_list as $v) {
            $v_position = $position;
            $count = $num / $v_num / $arr_num;
            for ($j = 1; $j <= $count; $j++) {
                for ($i = 0; $i < $arr_num; $i++) {
                    $result[$v_position + $i][$key] = $v;
                }
                $v_position += $arr_num * $v_num;
            }
            $position += $arr_num;
        }
    }
    return $result;
}


/**
 * 时间格式化
 * @param $time  时间
 * @param int $type 格式类别
 * @return bool|string
 */
function formatTime($time, $type = 's')
{
    $time = intval($time);
    if ($time < 9999999) {
        return '';
    }
    switch ($type) {
        case 's':
            $timestr = date('Y-m-d H:i:s', $time);
            break;
        case 'i':
            $timestr = date('Y-m-d H:i', $time);
            break;
        case 'h':
            $timestr = date('Y-m-d H', $time);
            break;
        case 'd':
            $timestr = date('Y-m-d', $time);
            break;
        case 'm':
            $timestr = date('Y-m', $time);
            break;
        case 'y':
            $timestr = date('Y', $time);
            break;
        default;
            $timestr = date('Y-m-d H:i:s', $time);
            break;
    }
    return date('Y-m-d H:i:s', $time);
}

/**
 * 货币格式化
 * param money 数字
 * rerutn 如1.01金额格式
 */
function formatMoney($money, $num = 2)
{
    return number_format(floatval($money), $num, '.', '');
}

/**
 * 数字格式化
 * @param $float
 * @param int $num
 * @return string
 */
function formatNumber($float, $num = 2)
{
    return number_format(floatval($float), $num, '.', '');
}

/**
 * 格式化二进制，前面补位
 * @param $bin  二进制
 * @param int $len 长度
 * @return string
 */
function formatBinary($bin, $len = 6)
{
    $differ_len = $len - strlen($bin);
    if ($differ_len > 0) {
        $cover = '';
        for ($i = 0; $i < $differ_len; $i++) {
            $cover .= '0';
        }
        return $cover . $bin;
    } else {
        return $bin;
    }
}

/**
 * 在线生成缩略图
 * 缩略图存放路径
 *
 * @param unknown $imgurl 要生成的图片地址
 * @param number $width 缩略图宽度
 * @param number $height 缩略图高度
 * @param string $smallpic 默认图片
 * @param string $alt 图片说明
 * @return string
 */
function getThumb($imgurl, $width = 100, $height = 100, $smallpic = 'empty.jpg')
{
    //如果源图片地址为空，则返回默认空图片
    if (empty($imgurl)) {
        return '/static/images/' . $smallpic;
    }
    //判断源图是否是文件，查询源图是否存在
    if (!is_file(config('upload.path') . $imgurl)) {
        return '/static/images/' . $smallpic;
    }
    //获取图片的绝对路径（本地路径 ）
    $thumburl = config('upload.thumb_path_name') . $imgurl;
    $fileArr = pathinfo($thumburl);//返回文件路径的信息
    $dirname = (string)$fileArr ['dirname'];//文件所在目录
    //检测目录是否存在，如果不存在则创建目录
    if (!is_dir(SITE_PATH . $dirname)) {
        mkdir(SITE_PATH . $dirname, 0777, true);
    }
    $filename = (string)$fileArr ['filename'];//文件名字
    $extension = (string)$fileArr ['extension'];//文件后缀
    $thumb = $dirname . "/" . $filename . "_" . $width . "_" . $height . "." . $extension;//此次要生成的缩略图及完整路径
    //检测文件是否存在
    if (!is_file($thumb)) {
        $realurl = config('upload.path_name') . $imgurl;//源图片路径
        //检查源文件是否存在
        if (is_file($realurl)) {
            $image = \zzcms\util\Image::open($realurl);//打开一个图片文件
            $image->thumb($width, $height, 6)->save(SITE_PATH . $thumb);//保存图像
        } else {
            $thumb = 'static/images/' . $smallpic;//如果源文件不存在，则返回默认图片
        }
    }
    return '/' . $thumb;
}


/**
 * 获取URL,计算参数
 * @param $url
 * @param array $param
 */
function getParamUrl($params = [])
{
    $request = \think\Request::instance();
    $get = $request->param();
    foreach ($get as $urlparam => $value) {
        if (strpos($urlparam, $request->action())) {
            unset($get[$urlparam]);
        }
    }
    if (!empty($params)) {
        foreach ($params as $field => $item) {
            if (isset($get[$field])) {
                $get[$field] = $item;
            } else {
                $get = array_merge($get, $params);
            }
        }
    }
    return url($request->action(), $get);
}


/**
 * 获取联动菜单列表，可以获取，子列表，同级列表，父级的同级列表
 * @param int $linkageid 当时的ID
 * @param array $options name:联动菜单名称  type: parent:父级同级菜单，siblings:同级菜单  children:下级菜单,
 * @return array
 */
function getLinkage($linkageid = 0, $options = [])
{
    //联动菜单名称
    $name = isset($options['name']) ? intval($options['name']) : 'area';
    //要读取的菜单级别， parent:父级同级菜单，siblings:同级菜单  children:下级菜单,
    $type = isset($options['type']) ? $options['type'] : 'children';
    //读取联动菜单缓存
    $linkage = cache('linkage_' . $name);
    //如果缓存为空，则重新生成缓存
    if (empty($linkage)) {
        \think\Loader::model('admin/Linkage')->create_cache_byname($name);
        $linkage = cache('linkage_' . $name);
    }
    switch ($type) {
        case 'parent':
            $parentid = isset($linkage[$linkageid]) ? intval($linkage[$linkageid]['parentid']) : 0;
            $parentid = isset($linkage[$parentid]) ? intval($linkage[$parentid]['parentid']) : 0;
            break;
        case 'siblings':
            $parentid = isset($linkage[$linkageid]) ? intval($linkage[$linkageid]['parentid']) : 0;
            break;
        default:
            $parentid = intval($linkageid);
    }
    $array = [];
    foreach ($linkage as $k => $item) {
        if ($parentid == $item['parentid']) {
            $array[$item['linkageid']] = $item;
        }
    }
    unset($linkage);
    return $array;
}

/**
 * 返回下级联动菜单（目的地）
 * @return string
 */
function getNextLinkage($id)
{
    $id = intval($id);
    if ($id < 1) {
        return ['status' => 0, 'msg' => 'ID错误'];
    }
    $linkageCache = zcache('linkage');
    $str = '';
    foreach ($linkageCache as $k => $item) {
        if ($item['parentid'] == $id) {
            $str .= '<option value = "' . $item['linkageid'] . '" > ' . $item['name'] . ' </option > ';
        }
    }
    return ['status' => 1, 'msg' => $str];
}

/**
 * 获取类别
 * 类型：life_tuan  life_events life_mall life_daren life_good
 * @param string $module
 */
function getCateList($module = 'tuan', $options = [])
{
    //分类列表(一级和二级关联)
    $catelist = cache('category');
    //如果缓存不存在，则生成缓存
    if (empty($catelist)) {
        $catelist = \think\Loader::model('admin/category')->create_cache();
    }
    $cate_array = [];
    $type = isset($options['pre']) ? $options['pre'] . '_' . $module : 'life_' . $module;
    if (!empty($catelist)) {
        foreach ($catelist as $item) {
            if ($item['type'] == $type) {
                $cate_array[$item['catid']] = $item;
            }
        }
    }
    return $cate_array;
}


/**
 * 格式化分类为树形结构
 * @param $items
 * @return array
 */
function getCateTree($items)
{
    $tree = []; //初始化格式化好的树
    foreach ($items as $item) {
        if (isset($items[$item['parentid']])) {
            $items[$item['parentid']]['child'][] = &$items[$item['catid']];
        } else {
            $tree[] = &$items[$item['catid']];
        }
    }
    return $tree;
}


/**
 * curl访问
 * @param  string $url
 * @param boolean $data
 * @param string $err_msg
 * @param int $timeout
 * @param array $cert_info
 * @return string
 */
function goCurl($url, $type, $data = false, &$err_msg = null, $timeout = 20, $cert_info = array())
{
    $type = strtoupper($type);
    if ($type == 'GET' && is_array($data)) {
        $data = http_build_query($data);
    }
    $option = array();
    if ($type == 'POST') {
        $option[CURLOPT_POST] = 1;
    }
    if ($data) {
        if ($type == 'POST') {
            $option[CURLOPT_POSTFIELDS] = $data;
        } elseif ($type == 'GET') {
            $url = strpos($url, '?') !== false ? $url . '&' . $data : $url . '?' . $data;
        }
    }
    $option[CURLOPT_URL] = $url;
    $option[CURLOPT_FOLLOWLOCATION] = TRUE;
    $option[CURLOPT_MAXREDIRS] = 4;
    $option[CURLOPT_RETURNTRANSFER] = TRUE;
    $option[CURLOPT_TIMEOUT] = $timeout;
    //设置证书信息
    if (!empty($cert_info) && !empty($cert_info['cert_file'])) {
        $option[CURLOPT_SSLCERT] = $cert_info['cert_file'];
        $option[CURLOPT_SSLCERTPASSWD] = $cert_info['cert_pass'];
        $option[CURLOPT_SSLCERTTYPE] = $cert_info['cert_type'];
    }
    //设置CA
    if (!empty($cert_info['ca_file'])) {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 1;
        $option[CURLOPT_CAINFO] = $cert_info['ca_file'];
    } else {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 0;
    }
    $ch = curl_init();
    curl_setopt_array($ch, $option);
    $response = curl_exec($ch);
    $curl_no = curl_errno($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);
    // error_log
    if ($curl_no > 0) {
        if ($err_msg !== null) {
            $err_msg = '(' . $curl_no . ')' . $curl_err;
        }
    }
    return $response;
}

/**
 * 生成GUID
 * @return string
 */
function guid()
{
    if (function_exists('com_create_guid')) {
        return com_create_guid();
    } else {
        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12)
            . chr(125);// "}"
        return $uuid;
    }
}

/**
 * 插入二进制到数据表中
 * 用途：主要用于快速定位条件筛选中，1:代表选中 0：代表不选   此次共插入10位，可供10个以内条件随意选择组合，
 * 用法：在二进制前面补位字符‘0’，形成各种组合
 *
 */
function insertBinary()
{
    exit('弃用');
    $bin = '0';//初始化二进制
    $binarr = [];//保存二进制的数组，为批量增加做准备
    for ($i = 0; strlen($bin) <= 10; $i++) {
        $dec = bindec($bin);//二进制转化为十进制
        $dec = $dec + 1;//十进制+1
        $binarr[] = ['binary' => $bin = decbin($dec), 'number' => $dec];
    }
    array_pop($binarr);//因为最后一个元素超过出长度，所以要移除出数组
    \think\Db::name('binary')->insertAll($binarr);
}

/**
 * 选择选中状态
 * @param $id  值
 * @param $ids
 * @return string
 */
function isSelected($id, $ids)
{
    if (empty($ids)) {
        return '';
    }
    if (is_array($ids)) {
        $arr = $ids;
    } else {
        $arr = explode(',', $ids);
    }
    unset($ids);
    if (in_array($id, $arr)) {
        return 'checked="checked"';
    } else {
        return '';
    }
}

/**
 * 随机字符
 * @param int $length 长度
 * @param string $type 类型
 * @param int $convert 转换大小写 1大写 0小写
 * @return string
 */
function random($length = 10, $type = 'letter', $convert = 0)
{
    $config = [
        'number' => '1234567890',
        'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    ];

    if (!isset($config[$type])) $type = 'letter';
    $string = $config[$type];

    $code = '';
    $strlen = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
        //字符串中的随机字符
        $code .= $string{mt_rand(0, $strlen)};
    }
    if (!empty($convert)) {
        $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
    }
    return $code;
}

/**
 * 生成二维码
 * @param  string $url url连接
 * @param  integer $size 尺寸 纯数字
 */
function qrcode($url, $options = [])
{    //生成二维码
    Vendor('phpqrcode.phpqrcode');
    // 如果没有http 则添加
    if (strpos($url, 'http') === false) {
        $url = 'http://' . $url;
    }

    //生成的二维码大小,默认4
    $size = isset($options['size']) ? intval($options['size']) : 4;
    //是否保存二维码，默认不保存
    $create = isset($options['create']) ? intval($options['create']) : 0;

    $object = new \QRcode();
    if ($create == 0) {
        ob_end_clean();
        $object->png($url, false, QR_ECLEVEL_L, $size, 2, false, 0xFFFFFF, 0x000000);
    } else {
        //保存路径
        $path = isset($options['path']) ? $options['path'] : config('upload.path') . 'qrcode/' . date('Ymd');
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                return false;
            }
        }
        // 生成的文件名
        $fileName = isset($options['name']) ? $path . '/' . $options['name'] . '.png' : $path . '/' . random() . '.png';
        //当文件不存在时，生成
        if (!is_file($fileName)) {
            $object->png($url, $fileName, QR_ECLEVEL_L, $size, 2, false, 0xFFFFFF, 0x000000);
        }
    }
}

/**
 * 短信发送（阿里大鱼）
 * 短信模板内容：${code}(动态验证码)，请于${times}分钟内正确输入验证码
 * @return mixed|void
 */
function sms($mobile, $options = [])
{
    if (strlen($mobile) != 11) {
        return ['status' => 0, 'msg' => '手机号格式不正确'];
    }
    //同一个用户一个小时只发送一次验证码
    $sms_info = \think\Db::name('sms')->where(['mobile' => $mobile, 'is_use' => 0])->order('id desc')->find();
    $active_time = isset($options['time']) ? intval($options['time']) : 3600;//默认有效时间，默认为 3600秒
    $minute = isset($options['minute']) ? intval($options['minute']) : 10;//默认说明时间，默认为 10分钟
    $templet = isset($options['templet']) ? intval($options['templet']) : 'SMS_26065209';//阿里大宇短信模板
    $module = isset($options['module']) ? intval($options['module']) : 'reg';//业务类别，默认为注册
    if (!empty($sms_info)) {
        $send_time = $sms_info['create_time'] + $active_time;
        if ($send_time > time()) {
            return ['status' => 0, 'msg' => '同一手机号码在一定时间内只允许发送一条验证码'];
        }
    }
    $Send = new \zzcms\common\model\SendSms;
    $code = (string)rand(100000, 999999);
    $result = $Send->sms(['param' => ['code' => $code, 'times' => $minute], 'mobile' => $mobile, 'template' => $templet, 'module' => $module,]);
    if ($result !== true) {
        return ['status' => 0, 'msg' => $result];
    }
    return ['status' => 1, 'msg' => '验证码已发送，请查收'];
}

/**
 * 字符截取
 * @param $string 需要截取的字符串
 * @param $length 长度
 * @param
 *   $dot
 */
function strCut($sourcestr, $length, $dot = '...')
{
    if (empty($sourcestr)) {
        return '';
    }
    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr); // 字符串的字节数
    while (($n < $length) && ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = Ord($temp_str); // 得到字符串中第$i位字符的ascii码
        if ($ascnum >= 224) { // 如果ASCII位高与224，
            $returnstr = $returnstr . substr($sourcestr, $i, 3); // 根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3; // 实际Byte计为3
            $n++; // 字串长度计1
        } elseif ($ascnum >= 192) { // 如果ASCII位高与192，
            $returnstr = $returnstr . substr($sourcestr, $i, 2); // 根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; // 实际Byte计为2
            $n++; // 字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) { // 如果是大写字母，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; // 实际的Byte数仍计1个
            $n++; // 但考虑整体美观，大写字母计成一个高位字符
        } else { // 其他情况下，包括小写字母和半角标点符号，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; // 实际的Byte数计1个
            $n = $n + 0.5; // 小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length > strlen($returnstr)) {
        $returnstr = $returnstr . $dot; // 超过长度时在尾处加上省略号
    }
    return $returnstr;
}

/**
 * 把表中的数据生成缓存
 * @param mixed $name 缓存名称
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function createCache($name, $value = '', $options = [])
{
    if (empty ($value)) {
        // 读取缓存
        $data = cache($name);
        // 如果缓存为空，则重新生成缓存
        if (empty ($data)) {
            $list = \think\Db::name($name)->select();//查询数据
            $data = [];
            if (!empty($list)) {
                //查询主键
                $table_name = config('database.prefix') . $name;
                $pks = \think\Db::query("select COLUMN_KEY,COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where table_name='$table_name' AND COLUMN_KEY='PRI'");
                $pk = $pks[0]['COLUMN_NAME'];
                foreach ($list as $item) {
                    $data[$item[$pk]] = $item;
                }
                unset($list, $table_name, $pks, $pk);
            }
            cache($name, $data);//生成缓存
        }
        return $data;
        //当没有value值时表示读取数据，并且需要返回
    } else {
        cache($name, $value);//生成缓存
    }
}

/**
 * 所有用到的密码加密方式
 * @param $password  密码
 * @param $password_code 密码额外加密字符
 * @return string
 */
function zzcmsPassword($password, $password_code)
{
    return md5(md5($password) . md5($password_code));
}

/**
 * 转换SQL关键字
 *
 * @param unknown_type $string
 * @return unknown
 */
function zzcmsSql($string)
{
    $pattern_arr = ["/\bunion\b/i", "/\bselect\b/i", "/\bupdate\b/i", "/\bdelete\b/i", "/\boutfile\b/i", "/\bor\b/i", "/\bchar\b/i", "/\bconcat\b/i", "/\btruncate\b/i", "/\bdrop\b/i", "/\binsert\b/i", "/\brevoke\b/i", "/\bgrant\b/i", "/\breplace\b/i", "/\balert\b/i", "/\brename\b/i", "/\bcreate\b/i", "/\bmaster\b/i", "/\bdeclare\b/i", "/\bsource\b/i", "/\bload\b/i", "/\bcall\b/i", "/\bexec\b/i", "/\bdelimiter\b/i"];
    $replace_arr = ['ｕｎｉｏｎ', 'ｓｅｌｅｃｔ', 'ｕｐｄａｔｅ', 'ｄｅｌｅｔｅ', 'ｏｕｔｆｉｌｅ', 'ｏｒ', 'ｃｈａｒ', 'ｃｏｎｃａｔ', 'ｔｒｕｎｃａｔｅ', 'ｄｒｏｐ', 'ｉｎｓｅｒｔ', 'ｒｅｖｏｋｅ', 'ｇｒａｎｔ', 'ｒｅｐｌａｃｅ', 'ａｌｅｒｔ', 'ｒｅｎａｍｅ', 'ｃｒｅａｔｅ', 'ｍａｓｔｅｒ', 'ｄｅｃｌａｒｅ', 'ｓｏｕｒｃｅ', 'ｌｏａｄ', 'ｃａｌｌ', 'ｅｘｅｃ', 'ｄｅｌｉｍｉｔｅｒ'];
    return is_array($string) ? array_map('strip_sql', $string) : preg_replace($pattern_arr, $replace_arr, $string);
}


/**
 * 是否微信访问
 * @return bool
 */
function isWeixin()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
}
