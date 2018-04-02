<div class="row">
    <div class="col-xs-12 col-md-6">

        <div class="panel panel-primary">
            <div class="panel-heading">框架配置文件 - <small>config/restful_api.php</small></div>
            <div class="panel-body">
                <ul class="ul">
                    <li>
                        <p>rest_not_login - 配置无需登录的接口<span class="text-danger">【不在此配置中的接口都要登录！！】</span>（若为目录名则该目录下所有接口都无需登录）</p>
                        <pre>[
    'common',
    'get_timestamp',
    'test'
]</pre>
                    </li>

                    <li>
                        <p>rest_filter_api - 该配置项下的接口不进行签名校验</p>
                        <pre>[
    'Get_timestamp'
]</pre>
                    </li>

                    <li>
                        <p>rest_timeout - 超时配置（校验API接口请求是否超时，超时则拒绝重放请求）</p>
                        <pre>rest_timeout = 10; // 单位：秒</pre>
                    </li>

                    <li>
                        <p>rest_access_key - 框架密钥配置</p>
                        <pre>[
    'LAPtXJMN1FImCLQa' => 'oT953JxnSHijGuRkhvFalAb7OVrdga'  // 开发环境
]</pre>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">框架结构及流程 - <small>（接口参考文件：Test.php）</small></div>
            <div class="panel-body">

                <ul class="ul">
                    <li>
                        <p>一个API控制器的各个方法含义</p>
                        <ul>
                            <li>__construct - 构造函数</li>
                            <li>run - 入口函数</li>
                            <li>rest_code - 接口返回状态码描述</li>
                            <li>rest_result - 接口返回结果说明</li>
                            <li>rest_parameter - 接口请求参数说明</li>
                            <li>rest_description - 接口功能描述</li>
                            <li>rest_precautions - 接口注意事项</li>
                            <li>rest_logs - 接口维护日志</li>
                        </ul>
                    </li>
<br>
                    <li>
                        <p>API接口执行流程：</p>
                        <ol>
                            <li>run - 启动函数( $params )</li>
                            <li>在run内编写代码，请求参数都在 $params 数组中（$params中的参数已经过框架过滤）</li>
                            <li>接口执行完成 直接 return 状态码。不返回值默认返回0（代表接口执行成功）</li>
                        </ol>
                    </li>
<br>
                    <li>
                        <p>API接口命名约定：</p>
                        <ol>
                            <li>接口文件名首字母大写，下划线分隔。 接口名简洁易懂。例如：Get_user_info、Set_user_info、Update_user_info等</li>
                            <li>接口存放位置通过文件夹归类（类似JAVA中的Package）。例如：user/Get_user_info</li>
                        </ol>
                    </li>

                </ul>

            </div>
        </div>
    </div>
</div>