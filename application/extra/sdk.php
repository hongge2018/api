<?php
/**
 * 第三方登录
 */

//定义回调URL通用的URL
//define('URL_CALLBACK', 'http://www.city.com/index.php?m=Index&a=callback&type=');

return [
    //腾讯QQ登录配置
    'sdk_qq' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => true,
        'callback' => '',
    ],
    //腾讯微博配置
    'sdk_tencent' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'tencent',
    ],
    //新浪微博配置
    'sdk_sina' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => '',
    ],
    //微信配置
    'sdk_weixin' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => true,
        'callback' => '',
    ],

    //网易微博配置
    'sdk_t163' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 't163',
    ],
    //人人网配置
    'sdk_renren' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'renren',
    ],
    //360配置
    'sdk_x360' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'x360',
    ],
    //豆瓣配置
    'sdk_douban' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'douban',
    ],
    //Github配置
    'sdk_github' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'callback' => 'github',
    ],
    //Google配置
    'sdk_google' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'google',
    ],
    //MSN配置
    'sdk_msn' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'msn',
    ],
    //点点配置
    'sdk_diandian' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'diandian',
    ],
    //淘宝网配置
    'sdk_taobao' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'taobao',
    ],
    //百度配置
    'sdk_baidu' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'baidu',
    ],
    //开心网配置
    'sdk_kaixin' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'kaixin',
    ],
    //搜狐微博配置
    'sdk_sohu' => [
        'app_key' => '', //应用注册成功后分配的 APP ID
        'app_secret' => '', //应用注册成功后分配的KEY
        'display' => false,
        'callback' => 'sohu',
    ],
];