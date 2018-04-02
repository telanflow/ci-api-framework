<?php

/**
 * 接口Demo
 *
 * @method POST
 * @author telan 2017.02.10
 */
class Test extends Rest_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 接口入口函数
     *
     * @param array $params 传入的参数
     * @return int
     */
    protected function run($params)
    {
        if($this->input->method() != 'post') {
            return 10010; // 非法请求
        }

        // 设置返回值
        $this->set_result('params', $params);
        $this->set_result('header', $this->header);

        // 返回状态码。如果无返回值默认为0，代表接口返回成功. （注意：任何接口返回00000表示最终结果返回成功！！）
        // 注意：使用 print_r echo var_dump 调试输出时，必须die 或者 exit，否则看不到调试结果。
        return 00000;
    }

    // 接口 - 状态
    protected function rest_code()
    {
        return array(
            '00000' => 'success',
            '10010' => 'Illegal request.(非法请求)'
        );
    }

    // 接口 - 参数说明
    protected function rest_parameter()
    {
        // 参数名    - [类型, 必需, 参数说明]

        // 传参过滤  - 框架底层根据填写的参数类型进行强类型过滤，并且去除多余参数。
        // string   - 字符串过滤
        // int      - 整型强转
        // float    - 单精度浮点数强转
        // double   - 双精度浮点数强转
        // 必需      - 参数若为必需，若请求时无此参数，框架底层将会返回00940错误码(Missing required parameters.(缺少必需参数))
        return array(
            'id' => ['int', '必需', '测试ID']
        );
    }

    // 接口 - 返回结果
    protected function rest_result()
    {
        // 返回数据格式描述
        return array(
            'list' => '结果集'
        );
    }

    // 接口 - 详细说明
    protected function rest_description()
    {
        return '详细说明（暂无）';
    }

    // 接口 - 注意事项
    protected function rest_precautions()
    {
        return '';
    }

    // 接口 - 维护日志
    protected function rest_logs()
    {
        return array(
            '2017-02-10' => '[telan] 创建接口',
            '2017-02-16' => '更新Demo，添加00940错误码说明',
            '2017-02-28' => '新增00950错误码(接口必需登录)',
            '2017-03-01' => '新增00951错误码(接口发生异常)',
            '2017-04-18' => '更新API文档规范、配置项说明',
            '2017-12-18' => '优化框架核心代码，去除多余功能，添加新特性。(API版本迭代、API降级处理、)',
        );
    }
}