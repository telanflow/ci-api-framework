<?php
/**
 * 获取版本信息
 *
 * @method GET
 * @author telan
 */
class Get_version extends Rest_Controller
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
        $this->set_result('version', '1.0');

        return 0000;
    }

    // 接口 - 状态
    protected function rest_code()
    {
        return array(
            '00000' => '操作成功',
            '10010' => 'Illegal request.(非法请求)',
        );
    }

    // 接口 - 参数说明
    protected function rest_parameter()
    {
        return array(
        );
    }

    // 接口 - 返回结果
    protected function rest_result()
    {
        return array(
            'version' => '版本号'
        );
    }

    // 接口 - 详细说明
    protected function rest_description()
    {
        return '获取版本信息';
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
            '2018-04-02' => '[telan] 创建接口',
        );
    }
}