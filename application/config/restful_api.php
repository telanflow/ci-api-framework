<?php

/**
 * 根据API版本选择不同的配置文件
 *
 * @author lsa 2018.01.11
 */

// 默认配置文件
$default = [

    // Access-Token header名称
    'rest_access_token' => 'Access-Token',

    // 签名密钥
    'rest_access_key' => [
        'LAPtXJMN1FImCLQa' => 'oT953JxnSHijGuRkhvFalAb7OVrdga', // 开发环境
    ],

    // 请求超时：单位s (拒绝重放请求)
    'rest_timeout' => 10,

    // API 调试配置
    'rest_service' => [
        // [
        //     'title' => 'remote test (远程测试环境)',
        //     'host' => 'http://test.api.cn',
        // ],
    ],

    // CORS跨域设置 - 前端设置
    'rest_cors' => [
        'headers' => [
            'x-requested-with',
            'content-type',
            'access-key',
            'access-token',
            'authorizations',
            'debug',
            'api-version',
            'remote-address',
        ],
        'methods' => ['*'],
        'origin' => ['*']
    ],

    // 不参与签名校验的接口
    'rest_filter_api' => [
        'Get_timestamp',
    ],

    // 无需登录接口
    'rest_not_login' => [
        'common', // common 目录下所有接口无需登录
        'get_timestamp',
        'test',
    ],

];

// V1.1版本配置文件
$v1_1 = [

];

// 合并配置文件
if (defined('REQUEST_API_VERSION')) {
    $api_version = str_replace('.', '_', REQUEST_API_VERSION);
    if(!empty($$api_version)) {
        $default = array_merge($default, $$api_version);
    }
}

$config = $default;