<?php

/**
 * 获取服务器时间戳
 *
 * @method GET
 * @author telan 2017.02.10
 */
class Get_timestamp extends Rest_Controller
{
    // 初始化
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 接口入口函数
     *
     * @param array $params 传入的参数，框架底层会做类型过滤
     * @return int
     */
    protected function run($params)
    {
        if($this->input->method() != 'get') {
            return 10010;
        }

        $this->set_result('timestamp', get_time_millisecond());
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
        return array();
    }

    // 接口 - 返回结果
    protected function rest_result()
    {
        return array(
            'timestamp' => '服务器时间戳[单位：ms]'
        );
    }

    // 接口 - 详细说明
    protected function rest_description()
    {
        return '获取服务器UTC时间戳（毫秒）';
    }

    // 接口 - 注意事项
    protected function rest_precautions()
    {
        return '该接口不参与签名校验';
    }

    // 接口 - 维护日志
    protected function rest_logs()
    {
        return array(
            '2017-02-10' => '[telan] 创建接口',
        );
    }
}