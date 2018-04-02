<?php
class MY_Config extends CI_Config
{
    /**
     * 构造函数初始化https解决
     */
    public function __construct()
    {
        $this->config =& get_config();
        if ($this->config['base_url'] == '') {
            if (isset($_SERVER['HTTP_HOST'])) {
                $base_url  = is_https()? 'https':'http';
                $base_url .= '://'. $_SERVER['HTTP_HOST'];
                $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            } else {
                $base_url = 'http://localhost/';
            }
            $this->set_item('base_url', $base_url);
        }
    }
}