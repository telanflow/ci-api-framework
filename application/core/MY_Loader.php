<?php
/**
 * 扩展CI_Loader类
 *
 * @since 1.0
 */

class MY_Loader extends CI_Loader
{
    /**
     * List of loaded services
     *
     * @var	array
     */
    protected $_ci_services =	array();

    /**
     * List of paths to load services from
     *
     * @var	array
     */
    protected $_ci_service_paths =	array(APPPATH);

    /**
     * 加载服务类
     * @param $service
     * @param null $params
     * @return $this
     */
    public function service($service, $name = '')
    {
        if (empty($service))
        {
            return $this;
        }
        elseif (is_array($service))
        {
            foreach ($service as $key => $value)
            {
                is_int($key) ? $this->service($value, '') : $this->service($key, $value);
            }

            return $this;
        }

        $path = '';

        // Is the service in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($service, '/')) !== FALSE)
        {
            // The path is in front of the last slash
            $path = substr($service, 0, ++$last_slash);

            // And the service name behind it
            $service = substr($service, $last_slash);
        }

        if (empty($name))
        {
            $name = $service;
        }

        if (in_array($name, $this->_ci_services, TRUE))
        {
            return $this;
        }

        $CI =& get_instance();
        if (isset($CI->$name))
        {
            throw new RuntimeException('The service name you are loading is the name of a resource that is already being used: '.$name);
        }

        $service = ucfirst($service);
        if ( ! class_exists($service, FALSE))
        {
            foreach ($this->_ci_service_paths as $mod_path)
            {
                if ( ! file_exists($mod_path.'services/'.$path.$service.'.php'))
                {
                    continue;
                }

                require_once($mod_path.'services/'.$path.$service.'.php');
                if ( ! class_exists($service, FALSE))
                {
                    throw new RuntimeException($mod_path."services/".$path.$service.".php exists, but doesn't declare class ".$service);
                }

                break;
            }

            if ( ! class_exists($service, FALSE))
            {
                throw new RuntimeException('Unable to locate the service you have specified: '.$service);
            }
        }

        $this->_ci_services[] = $name;
        $CI->$name = new $service();
        return $this;
    }
}