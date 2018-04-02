<?php
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 渲染视图
     *
     * @param string $view
     * @param array $data
     */
    protected function display($view, $data = array())
    {
        $this->load->view('header', $data);
        $this->load->view($view);
        $this->load->view('footer');
    }
}

/**
 * API框架 - 基类
 *
 * @author telan
 * @date 2017.02.16
 */
abstract class Base_Controller extends My_Controller
{
    protected $header = array();    // Header
    private $_params = null;        // Request Params
    private $_result = null;        // Response Result

    public function __construct()
    {
        parent::__construct();
        $this->_result = new ArrayObject();

        // Header
        $this->header = $this->_get_header();

        // Params
        $this->_params = $this->_get_params();

        // Access Token
        $this->_init_access_token();

        // 日志扩展
        // $this->load->library('AliLog');
    }

    // 重定义接收方法
    public function _remap($oMethod, $oParams)
    {
        ob_start();
        define('RUN_START_TIME', microtime(true));

        try{
            // CheckSign
            $verify = $this->_check_sign();
            if($verify === true)
            {
                // 权限校验
                $code = $this->_check_auth();
                if($code === true)
                {
                    // 参数检查、过滤
                    $params = $this->filter_params($this->_params);
                    if($params === false) {
                        $code = '00940'; // 缺少必需参数
                    } else {
                        $this->benchmark->mark('run_start'); // 标记开始执行时间
                        $code = call_user_func_array(array($this, 'run'), array($params));
                        $this->benchmark->mark('run_end'); // 标记执行结束时间
                    }
                }
            }else{
                $code = $verify; // 签名校验未通过
            }

            $code = sprintf('%05d', $code);
            // $this->logging('frame', array('code' => $code)); // 日志记录
        } catch(Exception $e) {
            $code = '00951'; // 接口发生异常
            // $this->logging('frame', array('code' => $code, 'frame_error' => $e->getMessage())); // 错误记录
        }

        define('RUN_END_TIME', microtime(true));
        ob_end_clean();

        // HTTP Response
        $this->_response($code);
    }

    /**
     * 设置返回值
     *
     * @param string|array $k 键名
     * @param string $v 值
     */
    public function set_result($k, $v = '')
    {
        if(is_array($k))
        {
            foreach ($k as $key => $val) {
                $this->_result[$key] = $val;
            }
        }else{
            $this->_result[$k] = $v;
        }
    }

    /**
     * 强类型参数过滤，若缺少必需参数则返回false
     *
     * @param array $params 参数
     * @param bool $str_safe 是否强过滤
     * @return array|false
     */
    public function filter_params(&$params, $str_safe = false)
    {
        $result = array();

        $pre_params = $this->rest_parameter();
        if(!empty($pre_params))
        {
            foreach($pre_params as $k => $v)
            {
                if(isset($v[1]) && $v[1] === '必需' && $v[0] != 'file') {
                    $isNeed = true;
                }else{
                    $isNeed = false;
                }

                if(isset($params[$k]))
                {
                    $val = null;
                    switch($v[0])
                    {
                        case 'string':
                            $val = trim($params[$k]);
                            if($str_safe){
                                $val = addslashes($val);
                            }
                            break;
                        case 'int':
                            $val = intval($params[$k]);
                            break;
                        case 'float':
                            $val = floatval($params[$k]);
                            break;
                        case 'double':
                            $val = doubleval($params[$k]);
                            break;
                        default:
                            $val = $params[$k];
                    }
                    $result[$k] = $val;
                } else {
                    if($isNeed) {
                        $result = false; // 缺少必需的参数
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 日志记录
     *
     * @param string $type 日志类型
     * @param array $msg 消息内容
     * @return bool
     */
    public function logging($type = 'frame', array $msg)
    {
        if(!empty($msg) && is_array($msg))
        {
            // 实际调用的接口版本
            if(defined('RESTFUL_API_VERSION')){
                $msg['api-version'] = RESTFUL_API_VERSION;
            }
            // 请求的接口版本
            if(defined('REQUEST_API_VERSION')){
                $msg['api-version-request'] = REQUEST_API_VERSION;
            }
            if(isset($this->header['app-version'])){
                $msg['app-version'] = $this->header['app-version'];
            }
            if(isset($this->header['user-agent'])){
                $msg['user-agent'] = $this->header['user-agent'];
            }
            if(isset($this->header['access-key'])){
                $msg['access-key'] = $this->header['access-key'];
            }

            // 输入参数
            if(!empty($this->_params) && is_array($this->_params))
            {
                $cache = array();
                foreach ($this->_params as $k => $v){
                    $cache[] = $k . '=' . rawurlencode($v);
                }
                $msg['request-params'] = implode('&', $cache); // 请求的参数
                unset($cache);
            }

            $msg['request-host'] = $_SERVER['HTTP_HOST']; // 请求的域名
            $msg['request-method'] = $this->input->method(); // 请求方法
            $msg['request-interface'] = $this->router->directory . $this->router->class; // 请求的接口
            return $this->alilog->set_log($type, $msg, $this->input->ip_address());
        }
        return false;
    }

    // 入口函数
    abstract protected function run($params);

    // 接口返回结果
    abstract protected function rest_result();

    // 状态码
    abstract protected function rest_code();

    // 参数说明
    abstract protected function rest_parameter();

    // 接口详细
    abstract protected function rest_description();

    // 注意事项
    abstract protected function rest_precautions();

    // 维护日志
    abstract protected function rest_logs();

    // HTTP - Header
    private function _get_header()
    {
        $header = $this->input->request_headers(true);
        return array_change_key_case($header, CASE_LOWER);
    }

    // HTTP - Params
    private function _get_params()
    {
        // 跨域
        $cors = $this->config->item('rest_cors');
        header('Access-Control-Allow-Origin: ' . implode(',', $cors['origin']));
        header('Access-Control-Allow-Methods: ' . implode(',', $cors['methods']));
        header('Access-Control-Allow-Headers: ' . implode(',', array_map('ucwords', $cors['headers'])));

        $method = $this->input->method();
        switch($method)
        {
            case 'post':
                $params = $this->input->post();
                break;
            case 'get':
                $params = $this->input->get();
                break;
            case 'options':
                header('HTTP/1.1 204 No Content'); // 表示option响应成功，并且无返回内容
                die;
            default:
                $params = file_get_contents('php://input');
        }
        return $params;
    }

    // Access-Token
    private function _init_access_token()
    {
        $uri_protocol = $this->config->item('uri_protocol');
        if($uri_protocol == 'RESTFUL_API')
        {
            $access_token_name = strtolower($this->config->item('rest_access_token'));

            $token = (!empty($this->header[$access_token_name]) && strlen($this->header[$access_token_name]) > 8) ? $this->header[$access_token_name] : get_access_token();
            header(ucwords($access_token_name) . ': ' . $token);

            session_id($token);
            $this->load->library('session');
        }
    }

    // Http - Response
    protected function _response($code)
    {   
        // 获取状态信息
        $status = $this->_get_code();
        $code = sprintf('%05d', $code);

        if(!isset($status[$code])) {
            $code = '00920'; // 未注册的返回状态值
        }
        $response = array(
            'result' => $this->_result,
            'status' => array(
                'code' => $code,
                'msg' => $status[$code],
                'runtime' => number_format(RUN_END_TIME - INDEX_TIME, 4)
            )
        );

        // 调试输出
        if(isset($this->header['debug'])) {
            header('Remote-Address:' . $this->input->ip_address());
            header('Api-Version: ' . RESTFUL_API_VERSION);
        }

       header('Content-Type: application/json; charset=utf-8;');
        $res = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $res;
        die;
    }

    // System - Code
    private function _get_code()
    {
        // 系统状态码
        $sys_code = $this->_sys_code();

        // 用户自定义状态值
        $user_code = $this->rest_code();

        foreach($sys_code as $k => $v) {
            $user_code[$k] = $v;
        }

        return $user_code;
    }

    private function _sys_code()
    {
        // 系统状态值
        return array(
            '00901' => 'Received protocol packets can not be resolved.(收到的协议包无法解析)',
            '00902' => 'Access key error.(访问密钥错误)',
            '00903' => 'Signature verification failed.(签名校验未通过)',
            '00911' => 'Authority check fails.(权限校验未通过)',
            '00915' => 'Refused to replay the request.(拒绝重放请求)',
            '00918' => 'API interface class not found.(api接口服务类未找到)',
            '00919' => 'API interface file not found.(api接口文件未找到)',
            '00920' => 'The returned value of the unregistered state.(未注册的返回状态值)',
            '00921' => 'API interface services no output result set.(api接口服务无输出结果集)',
            '00940' => 'Missing required parameters.(缺少必需参数)',
            '00950' => 'The interface must be logged in and accessible.(该接口必需登录后访问)',
            '00951' => 'The interface has an exception.(接口发生异常)',
        );
    }

    // 签名校验
    private function _check_sign()
    {
        $result = true;

        // 无需签名验证
        $filter_list = array_map('strtolower', $this->config->item('rest_filter_api'));

        // 是否不校验签名
        $request_url = $this->router->directory . $this->router->class;
        $request_url = preg_replace('/^v[0-9\.]\//i', '', $request_url, 1);
        foreach($filter_list as $item)
        {
            if(stripos($request_url, $item) !== false) {
                return $result; // 不校验签名
            }
        }

        $aKeys = $this->config->item('rest_access_key'); // 密钥列表
        $ak = !empty($this->header['access-key']) ? $this->header['access-key'] : false;
        $oSign = !empty($this->header['authorizations']) ? $this->header['authorizations'] : false;

        if(!empty($ak) && isset($aKeys[$ak]))
        {
            if(!empty($oSign))
            {
                $secret = $aKeys[$ak]; // 密钥
                $sign = access_sign($this->_params, $secret); // 生成签名
                if($sign === $oSign)
                {
                    // 拒绝重放请求
                    $timeout = $this->config->item('rest_timeout');
                    $t = !empty($this->_params['_t']) ? $this->_params['_t'] : 0;
                    $t = ($t / 1000 + $timeout) * 1000; // 超时单位是秒   所以要除以1000
                    if(empty($t) || $t < get_time_millisecond()) {
                        $result = '00915'; // 拒绝重放请求
                    }
                }else{
                    $result = '00903'; // 签名校验未通过
                }
            }else{
                $result = '00903'; // 签名校验未通过
            }
        }else{
            $result = '00902'; // 访问密钥错误
        }

        return $result;
    }

    // 权限校验
    private function _check_auth()
    {
        $result = true;

        // 获取请求的API
        $request_url = $this->router->directory . $this->router->class;
        $request_url = preg_replace('/^v[0-9\.]\//i', '', $request_url, 1);

        // 获取无需登录的接口列表
        $auth_list = $this->config->item('rest_not_login');

        // 判断是否无需登录
        $isLogin = true;
        foreach($auth_list as $item)
        {
            if(stripos($request_url, $item) !== false) {
                $isLogin = false; // 该接口无需登录
                break;
            }
        }

        if($isLogin)
        {
            // 如果不存在无需登录列表中则判断当前是否已经登录
            $user_info = $this->session->userdata('userinfo');
            if(empty($user_info)) {
                $result = '00950';
            }
        }

        return $result;
    }

}

/**
 * API - 权限控制
 *
 * @author telan
 * @date 2017.02.16
 */
abstract class Rest_Controller extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
}