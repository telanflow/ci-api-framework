<?php
/**
 * Created by PhpStorm.
 *
 * @author Bi Zhiming <evan2884@gmail.com>
 * @created 2017/10/31  上午9:17
 * @since 1.0
 */

class MY_Input extends CI_Input
{
    /**
     * 重写获取IP地址方法
     * @return mixed|string
     */
    public function ip_address()
    {
        if ($this->ip_address !== FALSE) {
            return $this->ip_address;
        }
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $this->ip_address = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $this->ip_address = $_SERVER['REMOTE_ADDR'];
                } else {
                    $this->ip_address = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $this->ip_address = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $this->ip_address = getenv('HTTP_CLIENT_IP');
            } else {
                $this->ip_address = getenv('REMOTE_ADDR');
            }
        }
        $onlineip = array();
        preg_match("/[\d\.]{7,15}/", $this->ip_address, $onlineip);
        $this->ip_address = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
        return $this->ip_address;
    }
}