<?php
use \think\Loader;

/**
 * 编辑器
 * @param string $name ：字段
 * @param string $type ：编辑器风格
 * @param int $is_load ：是否加载JS，默认加载
 * @param string $width :编辑区宽度【像素或者百分比】
 * @param int $height :编辑区高度【像素】
 * @return string
 */

function formEditor($name = 'content', $options = [])
{
    $is_load = isset($options['is_load']) ? $options['is_load'] : 1;
    $type = isset($options['type']) ? $options['type'] : 'default';
    $width = isset($options['width']) ? $options['width'] : '800px';
    $height = isset($options['height']) ? $options['height'] : 300;
    $value = isset($options['value']) ? $options['value'] : '';
    $str = '';
    if ($is_load == 1) {
        $str .= '<script charset="utf-8" src="__JS__/ueditor/ueditor.config.js"></script><script charset="utf-8" src="__JS__/ueditor/ueditor.all.min.js"></script>';
    }
    switch ($type) {
        case 'simple':
            $str .= '<script type="text/javascript">var ue = UE.getEditor(\'' . $name . '\', {
                    toolbars: [
                        [\'fullscreen\', \'source\', \'undo\', \'redo\',\'bold\', \'italic\', \'underline\', \'fontborder\', \'strikethrough\', \'superscript\', \'subscript\', \'removeformat\', \'formatmatch\', \'autotypeset\', \'blockquote\', \'pasteplain\', \'|\', \'forecolor\',\'fontsize\', \'backcolor\', \'insertorderedlist\', \'insertunorderedlist\', \'selectall\', \'cleardoc\', \'simpleupload\', \'insertimage\', \'map\',\'insertvideo\', \'inserttable\', \'edittable\', \'edittd\', \'date\']
                    ],
                    autoHeightEnabled: true,
                    autoFloatEnabled: true
                });</script>';
            break;

        default:
            $str .= '<script type="text/javascript">var ue = UE.getEditor(\'' . $name . '\');</script> ';
            break;
    }
    $str .= '<script id="' . $name . '" name="' . $name . '" style="width:' . $width . ';height:' . $height . 'px" type="text/plain">' . htmlspecialchars_decode($value) . '</script>';
    return $str;
}


/**
 * 日期控件
 * @param string $name字段名字
 * @param array $options
 * $options可包含参数   is_load:是否加载JS（1：加载） typpe:日期格式化 value:值（编辑的时候要用到） size:文本框长度
 *    "%Y-%m-%d %I:%M:00 ”
 * @return string
 */
function formCalendar($name = 'create_time', $options = [])
{
    $is_load = isset($options['is_load']) ? $options['is_load'] : 1;
    $type = isset($options['type']) ? $options['type'] : 'd';
    $value = isset($options['value']) ? format_date($options['value'], $options['type']) : '';
    $size = isset($options['size']) ? $options['size'] : 10;
    $str = '';
    if ($is_load == 1) {
        $str .= '<link rel="stylesheet" type="text/css" href="__JS__/calendar/jscal2.css"/>
                <link rel="stylesheet" type="text/css" href="__JS__/calendar/border-radius.css"/>
                <link rel="stylesheet" type="text/css" href="__JS__/calendar/win2k.css"/>
                <script type="text/javascript" src="__JS__/calendar/calendar.js"></script>
                <script type="text/javascript" src="__JS__/calendar/lang/en.js"></script>';
    }

    switch ($type) {
        case 'd':
            $format = '%Y-%m-%d';
            break;
        case 'm':
            $format = '%Y-%m';
            break;
        case 'y':
            $format = '%Y';
            break;
        case 'h':
            $format = '%Y-%m-%d %I';
            break;
        case 'i':
            $format = '%Y-%m-%d %I:%M';
            $size = 20;
            break;
        case 's':
            $format = '%Y-%m-%d %I:%M:00';
            $size = 20;
            break;
        default:
            $format = '%Y-%m-%d';
    }
    $str .= '<input type = "text" name = "' . $name . '" id = "' . $name . '" value = "' . format_date($value, 'd') . '" size = "' . $size . '" class="date" readonly>
              &nbsp;<script type = "text/javascript" >
    Calendar.setup({
			weekNumbers: false,
		    inputField: "' . $name . '",
		    trigger: "' . $name . '",
		    dateFormat:"' . $format . '",
		    showTime:false,
		    minuteStep:1,
		    onSelect:function ()
{
    this.hide();
}
			});
        </script > ';
    return $str;
}


/**
 * 日期时间控件
 * 此控件采用 laydate
 * @param string $name
 * @param array $options
 * @return string
 */
function formDate($name = 'create_time', $options = [])
{
    $is_load = isset($options['is_load']) ? intval($options['is_load']) : 1;
    $type = isset($options['type']) ? $options['type'] : 'd';
    switch ($type) {
        case 'm':
            $format = 'YYYY-MM';
            $value = empty($options['value']) ? '' : date('Y-m', $options['value']);
            $size = 10;
            $istime = 'false';
            break;
        case 'y':
            $format = 'YYYY';
            $value = empty($options['value']) ? '' : date('Y', $options['value']);
            $size = 10;
            $istime = 'false';
            break;
        case 'h':
            $format = 'YYYY-MM-DD hh';
            $value = empty($options['value']) ? '' : date('Y-m-d H', $options['value']);
            $size = 15;
            $istime = 'true';
            break;
        case 'i':
            $format = 'YYYY-MM-DD hh:mm';
            $value = empty($options['value']) ? '' : date('Y-m-d H:i', $options['value']);
            $size = 20;
            $istime = 'true';
            break;
        case 's':
            $format = 'YYYY-MM-DD hh:mm:ss';
            $value = empty($options['value']) ? '' : date('Y-m-d H:i:s', $options['value']);
            $size = 20;
            $istime = 'true';
            break;
        default:
            $format = 'YYYY-MM-DD';
            $value = empty($options['value']) ? '' : date('Y-m-d', $options['value']);
            $size = 15;
            $istime = 'false';
    }
    $str = '';
    if ($is_load == 1) {
        $str .= '<script type="text/javascript" src="__JS__/laydate/laydate.js"></script>';
    }
    $str .= '<input type ="text" name ="' . $name . '" id = "' . $name . '" value ="' . $value . '" size = "' . $size . '" class="layui-input" readonly>&nbsp;
            <script type = "text/javascript" >laydate({elem: \'#' . $name . '\',istime: ' . $istime . ',format:   \'' . $format . '\'});</script > ';
    return $str;
}

function formThumb($name = 'thumb', $options = [])
{
    $value = isset($options['value']) ? $options['value'] : '';
    $width = isset($options['width']) ? intval($options['width']) : 135;
    $height = isset($options['height']) ? $options['height'] : 113;
    $default_thumb = isset($options['nopic']) ? $options['nopic'] : 'upload-pic.png';
    $fullurl = empty($value) ? '__IMG__/' . $default_thumb : '/' . config('upload.path_name') . $value;
    $param = ['name' => $name];//参数
    if (isset($options['ext'])) {
        $param['ext'] = $options['ext'];//默认只能上传图片
    }
    $str = '<input name = "' . $name . '" id = "' . $name . '" type = "hidden" value = "' . $value . '" />
<a href = "javascript:void(0);" onclick = "layupload(\'' . url('Attachment/Attachment/upimg', $param) . '\', \'缩略图\',\'' . $name . '\');return false;" > <img src = "' . $fullurl . '" id = "' . $name . '_preview" width = "' . $width . '" style = "cursor:hand;" /></a > ';

    return $str;
}


/**
 * 图片集上传控件
 * @param string $name
 * @param string $nopic 是否设置默认空图片
 * @return string
 */
function formImages($name = 'imgs', $options = [])
{
    $param = ['name' => $name];//参数
    $param['ext'] = isset($options['ext']) ? $options['ext'] : 'jpg|jpeg|gif|png';//默认只能上传图片
    $str = '<div class=""><a href="javascript:void(0);" onclick="open_piclist(\'' . url('Attachment/Weixin/lists', $param) . '\', \'批量上传图片\',\'' . $name . '\')"/> <img src="__IMG__/upload-pic.png" /></div>';
    return $str;
}


/**
 * 地图-表单控件
 * @param string $name
 * @param array $options
 * @return string
 */
function formBmap($name = 'map', $options = [])
{
    $xpoint = isset($options['xpoint']) ? $options['xpoint'] : '';
    $ypoint = isset($options['ypoint']) ? $options['ypoint'] : '';
    $str = '<button type="button" class="layui-btn layui-btn-warm" onClick="open_map(\'' . url('map/baidu/seller') . '\')">在地图上标注</button>';
    $str .= '<input name="' . $name . '" id="' . $name . '" type="hidden" value="' . $xpoint . ',' . $ypoint . '" />';
    return $str;
}

/**
 * Layui图片上传控件
 * @param string $name
 * @param string $url
 * @param string $width
 * @param string $height
 * @param string $nopic 是否设置默认空图片
 * @return string
 */
function layUpload($name = 'thumb', $options = [])
{
    $value = isset($options['value']) ? $options['value'] : '';
    $width = isset($options['width']) ? intval($options['width']) : 135;
    $height = isset($options['height']) ? $options['height'] : 113;
    $default_thumb = isset($options['nopic']) ? $options['nopic'] : 'upload-pic.png';
    $fullurl = empty($value) ? '__IMG__/' . $default_thumb : '/' . config('upload.path_name') . $value;
    $param = ['name' => $name];//参数
    if (isset($options['ext'])) {
        $param['ext'] = $options['ext'];//默认只能上传图片
    }
    $str = '<input name = "' . $name . '" id = "' . $name . '" type = "hidden" value = "' . $value . '" />
<a href = "javascript:void(0);" onclick = "layupload(\'' . url('Attachment/Attachment/crop', $param) . '\', \'缩略图\',\'' . $name . '\');return false;" > <img src = "' . $fullurl . '" id = "' . $name . '_preview" width = "' . $width . '" style = "cursor:hand;" /></a > ';

    return $str;
}

/**
 * 生成微信缩略图
 * @param string $name
 * @param array $options
 * @return string
 */
function weixinThumb($name = 'thumb', $options = [])
{
    $value = isset($options['value']) ? $options['value'] : '';
    $width = isset($options['width']) ? intval($options['width']) : 100;
    $height = isset($options['height']) ? $options['height'] : 120;
    $default_thumb = isset($options['nopic']) ? $options['nopic'] : 'weixin_upload_pic.png';
    $fullurl = empty($value) ? '__IMG__/' . $default_thumb : '/' . config('upload.path_name') . $value;
    $param = ['name' => $name];//参数
    $param['ext'] = isset($options['ext']) ? $options['ext'] : 'jpg|jpeg|gif|png';//默认只能上传图片
    $str = '<input name = "' . $name . '" id = "' . $name . '" type = "hidden" value = "' . $value . '" />
<a href = "javascript:void(0);" onclick = "open_thumb(\'' . url('Attachment/Attachment/weixin', $param) . '\', \'缩略图\',\'' . $name . '\');return false;" > <img src = "' . $fullurl . '" id = "' . $name . '_preview" width = "' . $width . '%" height = "' . $height . '" style = "cursor:hand;" /></a > ';

    return $str;
}
