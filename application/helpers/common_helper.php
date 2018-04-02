<?php

/**
 * 遍历目录下的文件列表
 *
 * @param string $path 目录全路径
 * @return array 文件列表
 */
function file_list($path)
{
    $result = array();
    if (is_dir($path) && $handle = opendir($path)) {
        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') continue 1;
            $real_path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path . DIRECTORY_SEPARATOR . $file);
            //realpath($path.DIRECTORY_SEPARATOR.$file);//得到当前文件的全称路径
            $result[] = $real_path;
            if (is_dir($real_path)) {
                $result = array_merge($result, file_list($real_path));
            }
        }
        closedir($handle);
    }
    return $result;
}

if (!function_exists('json_format')) {
    /**
     * Json数据格式化
     *
     * @param array $data 数据
     * @param string $indent 缩进字符，默认4个空格
     * @return string
     */
    function json_format($data, $indent = '    ')
    {
        // 对数组中每个元素递归进行urlencode操作，保护中文字符
        array_walk_recursive($data, 'json_format_protect');

        // json encode
        $data = json_encode($data);

        // 将urlencode的内容进行urldecode
        $data = urldecode($data);

        // 缩进处理
        $ret = '';
        $pos = 0;
        $length = strlen($data);
        $newline = "\n";
        $prevchar = '';
        $outofquotes = true;

        for ($i = 0; $i <= $length; $i++) {

            $char = substr($data, $i, 1);

            if ($char == '"' && $prevchar != '\\') {
                $outofquotes = !$outofquotes;
            } elseif (($char == '}' || $char == ']') && $outofquotes) {
                $ret .= $newline;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }

            $ret .= $char;

            if (($char == ',' || $char == '{' || $char == '[') && $outofquotes) {
                $ret .= $newline;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }

            $prevchar = $char;
        }

        return $ret;
    }

    /**
     * 将数组元素进行urlencode
     *
     * @param String
     */
    function json_format_protect(&$val)
    {
        if (is_string($val)) {
            $val = urlencode($val);
        }
    }
}

/**
 * 获取访问令牌(代替session)
 *
 * @return string
 */
function get_access_token()
{
    $array = explode('.', uniqid(mt_rand(1, 99999999), true));
    $array[0] = $array[0] . dechex(rand(0, 15));
    $array[1] = substr(md5($array[1]), 8, 16);
    $token = substr($array[0], 0, 8) . '-' . substr($array[0], 8, 4) . '-' . substr($array[0], 12, 4);
    $token .= substr($array[1], 0, 4) . '-' . substr($array[1], 4, 12);
    return $token;
}

/**
 * 生成 Access Key 或 Access Secret
 *
 * @param   int $len 长度
 * @param   string $prefix 前缀
 * @return  string
 */
function get_access_str($len = 16, $prefix = '')
{
    // 字符集，可任意添加你需要的字符
    $chars = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
        'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
        'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
        'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2',
        '3', '4', '5', '6', '7', '8', '9',
    );

    $len = $len - strlen($prefix);
    shuffle($chars); // 打乱数组

    // 在 $chars 中随机取 $len 个数组元素键名
    $keys = array_rand($chars, $len);

    $ak = '';
    for ($i = 0; $i < $len; $i++) {
        $ak .= $chars[$keys[$i]];
    }
    return $prefix . $ak;
}

/**
 * 数据签名认证
 *
 * @param  array $data 被认证的数据
 * @param  string $secret 密钥
 * @return string 签名
 */
function access_sign($data, $secret = '')
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data, SORT_STRING); //排序

    $cache = array();
    foreach ($data as $k => $v) {
        $cache[] = $k . '=' . hash('md5', $v);
    }
    $str = implode('&', $cache) . $secret;
    $sign = sha1($str); //生成签名
    return $sign;
}

/**
 * 获取毫秒时间戳
 *
 * @return int
 */
function get_time_millisecond()
{
    return floor(microtime(true) * 1000);
}

/**
 * 系统加密方法
 *
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int $expire 过期时间 (单位:秒)
 * @return string
 */
function str_encrypt($data, $key, $expire = 0)
{
    $key = md5($key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l)
            $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 *
 * @param string $data 要解密的字符串 （必须是str_encrypt方法加密的字符串）
 * @param string $key 加密密钥
 * @return string
 */
function str_decrypt($data, $key)
{
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);
    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l)
            $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * Http请求函数
 *
 * @param string $url 请求的地址
 * @param string $type POST/GET/post/get
 * @param array $data 要传输的数据
 * @param string $err_msg 可选的错误信息（引用传递）
 * @param int $timeout 超时时间
 * @param array $cert_info 证书信息
 * @return array
 */
function request($url, $type = 'GET', $data = array(), &$err_msg = null, $timeout = 20, $cert_info = array())
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
 * 验证手机号是否正确
 *
 * @param string $mobile 手机号
 * @return bool
 */
function is_mobile($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
//    return preg_match('#^1\d{10}$#', $mobile) ? true : false;
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}
