<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    // 应用调试模式
    'app_debug' => true,
    // 应用Trace
    'app_trace' => true,
    // 应用模式状态
    'app_status' => '',
    // 是否支持多模块
    'app_multi_module' => true,
    // 注册的根命名空间
    'root_namespace' => [],
    // 扩展配置文件
    'extra_config_list' => ['database', 'route'],
    // 扩展函数文件
    'extra_file_list' => [THINK_PATH . 'helper' . EXT, 'forms'],
    // 默认输出类型
    'default_return_type' => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return' => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler' => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler' => 'callback',
    // 默认时区
    'default_timezone' => 'PRC',
    // 是否开启多语言
    'lang_switch_on' => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter' => 'trim,htmlspecialchars',
    // 默认语言
    'default_lang' => 'zh-cn',
    // 应用类库后缀
    'class_suffix' => false,
    // 控制器类后缀
    'controller_suffix' => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module' => 'home',
    // 禁止访问模块
    'deny_module_list' => ['common'],
    // 默认控制器名
    'default_controller' => 'Index',
    // 默认操作名
    'default_action' => 'index',
    // 默认验证器
    'default_validate' => '',
    // 默认的空控制器名
    'empty_controller' => 'Error',
    // 操作方法后缀
    'action_suffix' => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------


    // PATHINFO变量名 用于兼容模式
    'var_pathinfo' => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch' => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr' => '/',
    // URL伪静态后缀
    'url_html_suffix' => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param' => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type' => 0,
    // 是否开启路由
    'url_route_on' => true,
    // 路由使用完整匹配
    'route_complete_match' => false,
    // 路由配置文件（支持配置多个）
    'route_config_file' => ['route'],
    // 是否强制使用路由
    'url_route_must' => false,
    // 域名部署
    'url_domain_deploy' => true,
    // 域名根，如.thinkphp.cn
    'url_domain_root' => '.city.com',
    //网站默认域名，为避开二级域名的干扰，跨模块取值
    'url_base' => 'www.city.com',
    // 是否自动转换URL中的控制器和操作名
    'url_convert' => true,
    // 默认的访问控制器层
    'url_controller_layer' => 'controller',
    // 表单请求类型伪装变量
    'var_method' => '_method',
    // 表单ajax伪装变量
    'var_ajax' => '_ajax',
    // 表单pjax伪装变量
    'var_pjax' => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache' => false,
    // 请求缓存有效期
    'request_cache_expire' => null,
    // 全局请求缓存排除规则
    'request_cache_except' => [],

    //'base_url' => 'index.php?s=',
    // +----------------------------------------------------------------------
    // | 上传设置
    // +----------------------------------------------------------------------
    'upload' => [
        // 上传路径
        'path' => SITE_PATH . 'uploads/',
        'path_name' => 'uploads/',
        'thumb_path' => SITE_PATH . 'thumbs/',
        'thumb_path_name' => 'thumbs/',
        // 最大限制
        'maxsize' => 2 * 1024 * 1024,
        // 生成名字
        'savename' => 'uniqid',
    ],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template' => [
        // 模板引擎类型 支持 php think 支持扩展
        'type' => 'Think',
        // 模板路径
        'view_path' => '',
        // 模板后缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr' => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin' => '{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end' => '}',
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => APP_PATH . 'common' . DS . 'view' . DS . 'msg.php',
    'dispatch_error_tmpl' => APP_PATH . 'common' . DS . 'view' . DS . 'msg.php',
    // 视图输出字符串内容替换
    'view_replace_str' => [
        '__PUBLIC__' => '',
        '__CSS__' => '/static/css',
        '__JS__' => '/static/js',
        '__IMG__' => '/static/images',
        '__ROOT__' => '/',
    ],


    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl' => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg' => true,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle' => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log' => [
        // 日志记录方式，内置 file socket 支持扩展
        'type' => 'File',
        // 日志保存目录
        'path' => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace' => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache' => [
        // 驱动方式
        'type' => 'File',
        // 缓存保存目录
        'path' => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session' => [
        'id' => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix' => 'think',
        // 驱动方式 支持redis memcache memcached
        'type' => '',
        // 是否自动开启 SESSION
        'auto_start' => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie' => [
        // cookie 名称前缀
        'prefix' => '',
        // cookie 保存时间
        'expire' => 0,
        // cookie 保存路径
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        //  cookie 启用安全传输
        'secure' => false,
        // httponly设置
        'httponly' => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate' => ['type' => 'bootstrap', 'var_page' => 'page', 'list_rows' => 10,],
    'addons' => [
        // 可以定义多个钩子
        'testhook' => 'test' // 键为钩子名称，用于在业务中自定义钩子处理，值为实现该钩子的插件，
        // 多个插件可以用数组也可以用逗号分割
    ],
    'rollback' => false,
    'aliwap' => [
        //应用ID,您的APPID。
        'app_id' => "2017022205813107",
        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key' => "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDEvQW377yW86JUCbWovcPxV+vRJ+Wrv0koaeBcM/yLa9QXLh2CPvfzGsW2kfJnGl9DgqnGRIsiuMyEjnwpCayhQYpvNoPAB9bM+G8wDNg3GemiJkqaoMo59AiddNr5ES0/mAFXH6+dQeFQcAces+wPiDtPA68HUVkf9LsDyK2L54NJGnvRuz1iIkloXEY4+pzYfw+xDPAXeft5bYfM0C+zALIOYUipOU22qa7IkyXzTdN2rocLW31Xz1DUH11GFTKWNwK+VUdQA80m3es+jUHEloPKNQaEHgaMpROeIYMrNweXlZYcr/QULdkRBjHN1aI3MgI37sXCt46oIKk2YCgJAgMBAAECggEAQQWoCi61yb4j/FppK6fsRqukSLBC/AxwhWbYvCCCayHsKu0W26EsGbDTf4+k6eaRbaKVse2dfpBNJ9JfvsBvyav7sN33lVQB7iR9uwfDvhJWYTz+kzRpIdZBsqiYZpECv23ho4XZaMQJT6h28fLNJiUEVQ0GoAiGyET+OjXtBbueBESvNBm9qpZwaxSwcoWHLNIJFwoHYrCGL5FXfTibXGUw0OA6N5RbT0bqCNMeh1xvD/mBEwS2u3QmZNTc/RO5on/f37ebyxhm9zDuv4cIhUPlnpqY6LXohcwOI9F5PUdKOgTiyIGEW6z7DC8xQwqea2mVoLO91XKyecq68EioMQKBgQDm+80hdZqRU2MuDQ3wLE7eAs3KBwRt7mATCRuboHh+6Wju7enHpRpHUWbTIFo63U9dsTn2+EfT1iiXcINfPSiYjpkbK+euyFVa2sISTFf4qw4wro/kHWW26LDrBW2ZvnzGPEFE8ylfGOSIhlvz4j3nSrw+PJDVGqMZczol68xQ1wKBgQDaC77efLypjE9/8TSThIoozMpOTZJWO4o5wKXNLVWOZ+n2BMxH3JFmIkq81hEGYdZLQ+mC7PN+Y4CZCmh8tjrb2+p2upJVyp8/GiOgBTEsBXuGqBmIqufzaIVYHEbVWN2Hc6PHVgJLEzE4CfP5uMW2aI9IR9ft/rsPRgwiz/7SHwKBgASn5K2c+j2dqa0e9D34Fqrg8Zb15z/0Axm/IEBVzrf4KnZOc1zj1hDD8kelKkxvc48W6G0y+feqJG1RBkTgLbZNgYaLrwLV5OqM6EaIJWnMwN94VUwqz4cNT6udE2V68nbodgRWxmm1Tb32v1m+ILNNzVePzveMrubvbKehBiBHAoGBALrLdSiFE9rsKO8iZDg8Q1/QkT+jlMnrF++B6ohrXfAxdW0djyPBAY7Nsdk0SkF9b2frNhDDjZWpCHITSJAOTT4smCA+lT6J5wLYEcz0pbtgtwIU4EsEntEXqaRiFQyQpLd1ickrFavbNT8cx7YXZMlvqc/yPDnXD8l16M+qAFNhAoGASg3YAcDMsfy5Ah66bn+9z3dwjKJLpNAgA2SHz8NoPaCnDrqfKfRbyMuiO3yyKXAc6+Sp0bTeezc8H6tY3kJRzvMV0n5+xkrB50tNQOO3TRbkO7Sr49iLB+ckPuBknw7ieHUVLHIC/g1wwSRJ0+ARB6JLBcV7SGqB7dX1COUg8Ow=",
        //编码格式
        'charset' => "UTF-8",
        //签名方式
        'sign_type' => "RSA2",
        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmKyhDK0uKqm1nfUjJmYQ3obVsyINfy50UJb87wB6ag3cd0psGs3QGedmQllbKg7hxXJ32eKXNYccX73HVHLJPRTyZ4/hhG4slghGuELcIf0GVA/nTnhQVMBhMT7Uj7bqCAJbtL3yqwAjzupCAULPJAC+MO3rOEoDyrbACYkGl97UhQiWWiI33aBTUs4zsIMPLZ1ULPdMpTJW7aN43c+GPGds1zAvQsdhH79+OMEfaqNv30+2lqSEM3Nuv9c/SCVyVlwKzgwYnkZJAUDNAsR8tPJ+T5dxMeSNhJIk6ucxDcCXCh2P3tT2ryY3pYHIoWUvvY7bd6Za4CSjtsqV/es8xwIDAQAB"
    ],
];
