<?php

/**
 * API文档 - Debug
 * @author lsa 2018.04.02
 */
class Debug extends MY_Controller
{
    // 控制器路径
    private $controller_path = null;
    private $dir_readme = 'readme.txt';

    private $version_name = '';
    private $version_path = '';
    private $version_list = [];

    public function __construct()
    {
        parent::__construct();
        $this->controller_path = APPPATH . 'controllers';
        include APPPATH . 'libraries/DocParser.php';

        // 本地域名
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        define('LOCAL_HOST', $http_type . $_SERVER['HTTP_HOST']);

        // 获取版本列表
        list($this->version_list, $this->version_name, $this->version_path) = $this->get_version_list();
    }

	// Package
	public function package()
    {
        $package_name = $this->input->get('p');   // Package路径
        $class_name = $this->input->get('c'); // API接口名称

        $package_list = !empty($package_name) ? explode('/', $package_name) : [];


        // 反射 - 获取接口信息
        if(!empty($class_name))
        {
            list($class_path, $version) = $this->get_class_path(ucfirst($class_name) . '.php', $package_name);

            if(file_exists($class_path))
            {
                include_once($class_path);

                // 获取对象信息
                $instance = new $class_name();
                $class = new ReflectionClass($class_name);
                $doc = $class->getDocComment();

                // 解析Doc
                $parse = (new DocParser())->parse($doc);
                if(!empty($parse['description']) || !empty($parse['long_description'])){
                    $desc = !empty($parse['description']) ? $parse['description'] : $parse['long_description'];
                }else{
                    $desc = '';
                }

                $objMethod = $class->getMethods();
                foreach($objMethod as $obj)
                {
                    $method_name = $obj->name;
                    if(substr($method_name, 0, 5) == 'rest_')
                    {
                        $real = substr($method_name, 5);
                        $obj->setAccessible(true);
                        $res = $obj->invoke($instance);
                        $class_info[$real] = $res;
                    }

                    if(in_array($method_name, array('_get_code', '_sys_code')))
                    {
                        $obj->setAccessible(true);
                        $res = $obj->invoke($instance);
                        $class_info[$method_name] = $res;
                    }
                }

                $class_info['name'] = $class_name;
                $class_info['author'] = !empty($parse['author']) ? trim($parse['author']) : '';
                $class_info['title'] = $desc;
                $class_info['method'] = !empty($parse['method']) ? strtoupper(trim($parse['method'])) : '未知';
                $class_info['version'] = $version;

                $package_list[] = $class_name;
                $data['class_info'] = $class_info;
                $data['current_class'] = $class_name;
            }
        }

        // 遍历接口
        list($aDirList, $aFileList) = $this->get_package_list($package_name);

        $data['lists'] = array_merge($aDirList, $aFileList);

        $data['package_uri'] = $package_list;
        $data['version_list'] = $this->version_list;
        $data['version_name'] = $this->version_name; // 当前版本
        $this->display('debug/package', $data);
    }

    // 调试接口
    public function api()
    {
        $package_name = $this->input->get('p');
        $class_name = $this->input->get('c');

        $package_list = !empty($package_name) ? explode('/', $package_name) : [];

        if(!empty($class_name))
        {
            $package_list[] = $class_name;

            list($class_path, $version) = $this->get_class_path(ucfirst($class_name) . '.php', $package_name) ;
            if(file_exists($class_path))
            {
                include_once($class_path);

                // 获取对象信息
                $instance = new $class_name();
                $class = new ReflectionClass($class_name);
                $doc = $class->getDocComment();

                // 解析Doc
                $parse = DocParserFactory::getInstance()->parse($doc);
                $data['doc'] = $parse;

                if(!empty($parse['description']) || !empty($parse['long_description'])){
                    $desc = !empty($parse['description']) ? $parse['description'] : $parse['long_description'];
                }else{
                    $desc = '';
                }

                $objMethod = $class->getMethods();
                $class_info = array();
                foreach($objMethod as $obj)
                {
                    $method_name = $obj->name;
                    if(substr($method_name, 0, 5) == 'rest_')
                    {
                        $real = substr($method_name, 5);
                        $obj->setAccessible(true);
                        $res = $obj->invoke($instance);
                        $class_info[$real] = $res;
                    }
                }

                $data['class_info'] = $class_info;
                $data['package_list'] = $package_list;
                $data['class_name'] = $class_name;
                $data['version'] = $version; // 接口版本

                $data['version_list'] = $this->version_list;
                $data['version_name'] = $this->version_name; // 当前版本
                $this->display('debug/api', $data);
            }
        }
    }

    // 获取 [版本列表、当前版本名称、当前版本路径]
    private function get_version_list()
    {
        $version_list = get_dirs($this->controller_path);

        $v = trim($this->input->get('v'));
        if(!empty($v) && in_array($v, $version_list)) {
            $current_version = $v;
        }else{
            $current_version = $version_list[0];
        }

        // [ 版本列表、当前版本名称、当前版本路径 ]
        return [$version_list, $current_version, $this->controller_path . DIRECTORY_SEPARATOR . $current_version];
    }

    // 扫描目录列表
    private function get_package_list($package_name = '')
    {
        $version_list = $this->version_list;

        $aDirList = [];
        $aFileList = [];

        $cache_list = []; // 缓存列表
        foreach ($version_list as $v)
        {
            if($v > $this->version_name) {
                continue;
            }

            $base_path = $this->get_version_path($v) . DIRECTORY_SEPARATOR;
            $package_path = !empty($package_name) ? $base_path . $package_name : $base_path;

            if(is_dir($package_path))
            {
                $dir_list = array_diff(scandir($package_path), ['.', '..', '...'], $cache_list);
                $cache_list = array_merge($cache_list, $dir_list); // cache_list 用于过滤不同版本的重复文件

                foreach ($dir_list as $name)
                {
                    $test_path = $package_path . DIRECTORY_SEPARATOR . $name;
                    if(is_dir($test_path)) {
                        // 目录
                        $readme_file = $test_path . DIRECTORY_SEPARATOR . $this->dir_readme;
                        if(file_exists($readme_file)) {
                            $dir_desc = file_get_contents($readme_file);
                        }else{
                            $dir_desc = '';
                        }

                        $aDirList[] = array(
                            'name' => $name,
                            'type' => 1,
                            'path' => $package_path . $name,
                            'desc' => $dir_desc,
                            'version' => $v
                        );

                    } else {
                        // 文件
                        $suffix = substr(strrchr($name, '.'), 1);
                        $view_name = basename($name, '.' . $suffix);
                        if($suffix == 'php')
                        {
                            // 反射
                            include_once($test_path);
                            $class = new ReflectionClass($view_name);
                            $doc = $class->getDocComment();
                            $parse = (new DocParser())->parse($doc);
                            if(!empty($parse['description']) || !empty($parse['long_description'])){
                                $desc = !empty($parse['description']) ? $parse['description'] : $parse['long_description'];
                            }else{
                                $desc = '';
                            }

                            $aFileList[] = array(
                                'name' => $view_name,
                                'type' => 2,
                                'path' => $package_path . $name,
                                'desc' => $desc,
                                'version' => $v
                            );
                        }
                    }
                }
            }
        }

        return [$aDirList, $aFileList];
    }

    // 获取当前版本接口路径
    private function get_class_path($class_name, $package_name = '')
    {
        $version_list = $this->version_list;
        $class_path = '';
        $version = '';
        foreach($version_list as $v)
        {
            if($v > $this->version_name){
                continue;
            }

            $base_path = !empty($package_name) ? $this->get_version_path($v) . DIRECTORY_SEPARATOR . $package_name : $this->get_version_path($v);
            $class_path = $base_path . DIRECTORY_SEPARATOR . $class_name;

            if(file_exists($class_path)) {
                $version = $v;
                break;
            }
        }

        return [$class_path, $version];
    }

    // 根据版本号获取版本路径
    private function get_version_path($version)
    {
        return $this->controller_path . DIRECTORY_SEPARATOR . $version;
    }

    // 帮助文档
    public function help()
    {
        $data = [];
        $data['version_list'] = $this->version_list;
        $data['version_name'] = $this->version_name; // 当前版本
        $this->display('debug/help', $data);
    }

    // PHP开发规范，配置
    public function specification()
    {
        $data = [];
        $data['version_list'] = $this->version_list;
        $data['version_name'] = $this->version_name; // 当前版本
        $this->display('debug/specification', $data);
    }
}
